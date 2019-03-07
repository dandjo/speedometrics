<?php

namespace App\Controller;

use App\Repository\AddressRepository;
use Carbon\Carbon;
use Doctrine\ORM\Query\Expr\Join;
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
            ['link' => $this->generateUrl('charts.speed-metrics'), 'title' => 'Speed Metrics'],
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
        // average of the speed metric's minSpeed and maxSpeed.
        $response = ['data' => []];
        $addressId = $request->query->get('address');
        $address = (new AddressRepository($this->doctrine))->find($addressId);
        $response['data']['address'] = $address->toArray();
        $dateFrom = $request->query->get('from');
        $dateTo = $request->query->get('to');
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder();
        $speedSum = $qb->expr()->sum('speedMetrics.minSpeed', 'speedMetrics.maxSpeed');
        $speedAvg = $qb->expr()->quot($speedSum, 2);
        $speedProd = $qb->expr()->prod($speedAvg, 'speedMetrics.amountVehicles');
        $qb->select('dtc.dateTime AS dateTime')
            ->addSelect('SUM(' . $speedProd . ') AS speedProducts')
            ->addSelect('SUM(speedMetrics.amountVehicles) AS amountVehicles')
            ->from('App\Entity\DateTimeContainer', 'dtc')
            ->join('dtc.speedMetrics', 'speedMetrics', Join::WITH)
            ->where('dtc.address = :address')
            ->groupBy('dtc.id')
            ->orderBy('speedMetrics.maxSpeed')
            ->addOrderBy('dtc.dateTime')
            ->setParameter('address', $address);
        if ($dateFrom && $dateTo) {
            $qb->andWhere('dtc.dateTime BETWEEN :from AND :to')
                ->setParameter('from', Carbon::createFromFormat('Y-m-d', $dateFrom))
                ->setParameter('to', Carbon::createFromFormat('Y-m-d', $dateTo));
        }
        $result = $qb->getQuery()->getResult();
        $response['data']['series'] = array_map(function($row) {
            return [
                $row['dateTime']
                    ->setTimeZone(new \DateTimeZone('UTC'))
                    ->format('Y-m-d\TH:i:s\Z'),
                $row['amountVehicles'] > 0
                    ? round($row['speedProducts'] / $row['amountVehicles'], 2)
                    : null
            ];
        }, $result);
        $filteredSpeeds = array_filter($response['data']['series'], function($row) {
            return $row[1] !== null;
        });
        $speedAvgs = array_map(function($row) {
            return $row[1];
        }, $filteredSpeeds);
        $response['data']['summarized']['average'] = round(array_sum($speedAvgs) / count($filteredSpeeds), 2);
        $response['data']['summarized']['min'] = min($speedAvgs);
        $response['data']['summarized']['max'] = max($speedAvgs);
        return $this->json($response);
    }

    /**
     * @Route("/speed-metrics", name="api.speed-metrics")
     * @param Request $request
     * @return Response
     */
    public function speedMetrics(Request $request): Response
    {
        $response = ['data' => []];
        $addressId = $request->query->get('address');
        $address = (new AddressRepository($this->doctrine))->find($addressId);
        $response['data']['address'] = $address->toArray();
        $dateFrom = $request->query->get('from');
        $dateTo = $request->query->get('to');
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder()
            ->select('CONCAT(speedMetrics.minSpeed, \' - \', speedMetrics.maxSpeed) AS speed')
            ->addSelect('SUM(speedMetrics.amountVehicles) AS amountVehicles')
            ->from('App\Entity\DateTimeContainer', 'dtc')
            ->join('dtc.speedMetrics', 'speedMetrics', Join::WITH)
            ->where('dtc.address = :address')
            ->groupBy('speed')
            ->orderBy('speedMetrics.maxSpeed')
            ->setParameter('address', $address);
        if ($dateFrom && $dateTo) {
            $qb->andWhere('dtc.dateTime BETWEEN :from AND :to')
                ->setParameter('from', Carbon::createFromFormat('Y-m-d', $dateFrom))
                ->setParameter('to', Carbon::createFromFormat('Y-m-d', $dateTo));
        }
        $response['data']['series'] = array_map(function($result) {
            return [$result['speed'], intval($result['amountVehicles'])];
        }, $qb->getQuery()->getResult());
        return $this->json($response);
    }
}
