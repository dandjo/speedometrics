<?php

namespace App\Controller;

use App\Entity\Address;
use App\Repository\AddressRepository;
use Carbon\Carbon;
use Doctrine\ORM\Query\Expr\Join;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @var RegistryInterface
     */
    private $doctrine;

    /**
     * Import constructor.
     * @param RegistryInterface $doctrine
     */
    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/menu", name="api.menu")
     * @return Response
     */
    public function menu(): Response
    {
        return $this->json(['data' => ['items' => [
            ['link' => $this->generateUrl('charts.speed-categories'), 'title' => 'Speed Categories'],
            ['link' => $this->generateUrl('charts.average-speed'), 'title' => 'Average Speed'],
            ['link' => $this->generateUrl('charts.average-speed.stock'), 'title' => 'Average Speed Stock'],
        ]]]);
    }

    /**
     * @Route("/addresses", name="api.addresses")
     * @return Response
     */
    public function addresses(): Response
    {
        $addresses = (new AddressRepository($this->doctrine))->findAll();
        $response['data']['addresses'] = array_map(function($address) {
            return $address->toArray();
        }, $addresses);
        return $this->json($response);
    }

    /**
     * @Route("/average-speed/hour", name="api.average-speed")
     * @param Request $request
     * @return Response
     */
    public function averageSpeed(Request $request): Response
    {
        // Disclaimer: We assume the driven speed per measurement to be the
        // average of the speed category's rangeFrom and rangeTo.
        $response = ['data' => []];
        $addressId = $request->query->get('address');
        $address = (new AddressRepository($this->doctrine))->find($addressId);
        $response['data']['address'] = $address->toArray();
        $dateFrom = $request->query->get('from');
        $dateTo = $request->query->get('to');
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $rangeSum = $qb->expr()->sum('speedCategories.rangeFrom', 'speedCategories.rangeTo');
        $rangeAvg = $qb->expr()->quot($rangeSum, 2);
        $speedProd = $qb->expr()->prod($rangeAvg, 'speedCategories.amountVehicles');
        $qb->select('data.dateTime AS dateTime')
            ->addSelect('SUM(' . $speedProd . ') AS speedProducts')
            ->addSelect('SUM(speedCategories.amountVehicles) AS amountVehicles')
            ->from('App\Entity\DataSet', 'data')
            ->join('data.speedCategories', 'speedCategories', Join::WITH)
            ->where('data.address = :address')
            ->groupBy('data.id')
            ->orderBy('speedCategories.rangeTo')
            ->addOrderBy('data.dateTime')
            ->setParameter('address', $address);
        if ($dateFrom && $dateTo) {
            $qb->andWhere('data.dateTime BETWEEN :from AND :to')
                ->setParameter('from', Carbon::createFromFormat('Y-m-d', $dateFrom))
                ->setParameter('to', Carbon::createFromFormat('Y-m-d', $dateTo));
        }
        $result = $qb->getQuery()->getResult();
        $response['data']['series'] = array_map(function($row) {
            $avgSpeed = $row['amountVehicles'] > 0
                ? round($row['speedProducts'] / $row['amountVehicles'], 2)
                : null;
            return [
                $row['dateTime']
                    ->setTimeZone(new \DateTimeZone('UTC'))
                    ->format('Y-m-d\TH:i:s\Z'),
                $avgSpeed
            ];
        }, $result);
        $filteredSpeeds = array_filter($response['data']['series'], function($row) {
            return $row[1] !== null;
        });
        $avgSpeeds = array_map(function($row) {
            return $row[1];
        }, $filteredSpeeds);
        $response['data']['summarized']['average'] = round(array_sum($avgSpeeds) / count($filteredSpeeds), 2);
        $response['data']['summarized']['min'] = min($avgSpeeds);
        $response['data']['summarized']['max'] = max($avgSpeeds);
        return $this->json($response);
    }

    /**
     * @Route("/speed-categories", name="api.speed-categories")
     * @param Request $request
     * @return Response
     */
    public function speedCategories(Request $request): Response
    {
        $response = ['data' => []];
        $addressId = $request->query->get('address');
        $address = (new AddressRepository($this->doctrine))->find($addressId);
        $response['data']['address'] = $address->toArray();
        $dateFrom = $request->query->get('from');
        $dateTo = $request->query->get('to');
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder()
            ->select('CONCAT(speedCategories.rangeFrom, \' - \', speedCategories.rangeTo) AS range')
            ->addSelect('SUM(speedCategories.amountVehicles) AS amountVehicles')
            ->from('App\Entity\DataSet', 'data')
            ->join('data.speedCategories', 'speedCategories', Join::WITH)
            ->where('data.address = :address')
            ->groupBy('range')
            ->orderBy('speedCategories.rangeTo')
            ->setParameter('address', $address);
        if ($dateFrom && $dateTo) {
            $qb->andWhere('data.dateTime BETWEEN :from AND :to')
                ->setParameter('from', Carbon::createFromFormat('Y-m-d', $dateFrom))
                ->setParameter('to', Carbon::createFromFormat('Y-m-d', $dateTo));
        }
        $response['data']['series'] = array_map(function($result) {
            return [$result['range'], intval($result['amountVehicles'])];
        }, $qb->getQuery()->getResult());
        return $this->json($response);
    }
}
