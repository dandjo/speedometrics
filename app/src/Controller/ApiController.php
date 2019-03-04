<?php

namespace App\Controller;

use App\Entity\Address;
use App\Repository\AddressRepository;
use App\Repository\DataSetRepository;
use Carbon\Carbon;
use Doctrine\Common\Collections\Expr\Expression;
use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Tests\A;

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
        return $this->json(['data' => [
            ['link' => $this->generateUrl('charts.speed-categories'), 'title' => 'Speed Categories'],
            ['link' => $this->generateUrl('charts.average-speed.hour'), 'title' => 'Average Speed'],
            ['link' => $this->generateUrl('charts.average-speed.hour.stock'), 'title' => 'Average Speed Stock'],
        ]]);
    }

    /**
     * @Route("/average-speed/hour", name="api.average-speed.hour")
     * @param Request $request
     * @return Response
     */
    public function averageSpeedPerHour(Request $request): Response
    {
        // Disclaimer: We assume the driven speed per measurement to be the
        // average of the speed category's rangeFrom and rangeTo.
        $response = ['data' => []];
        $address = (new AddressRepository($this->doctrine))->findOneBy([
            'street' => 'Cumberlandstrasse',
            'number' => '47',
            'zip' => '1140',
        ]);
        $response['data']['address'] = $this->serializeAddress($address);
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
        $response['data']['chart']['data'] = array_map(function($result) {
            $avgSpeed = $result['amountVehicles'] > 0
                ? round($result['speedProducts'] / $result['amountVehicles'], 2)
                : null;
            return [$result['dateTime']->format('Y-m-d H:i:s'), $avgSpeed];
        }, $qb->getQuery()->getResult());
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
        $address = (new AddressRepository($this->doctrine))->findOneBy([
            'street' => 'Cumberlandstrasse',
            'number' => '47',
            'zip' => '1140',
        ]);
        $response['data']['address'] = $this->serializeAddress($address);
        $dateFrom = $request->query->get('from');
        $dateTo = $request->query->get('to');
        $qb = $this->doctrine->getEntityManager()->createQueryBuilder()
            ->select('CONCAT(speedCategories.rangeFrom, \' - \', speedCategories.rangeTo, \' km/h\') AS range')
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
        $response['data']['chart']['data'] = array_map(function($result) {
            return [$result['range'], intval($result['amountVehicles'])];
        }, $qb->getQuery()->getResult());
        return $this->json($response);
    }

    /**
     * @param Address $address
     * @return array
     */
    protected function serializeAddress(Address $address): array
    {
        return [
            'street' => $address->getStreet(),
            'number' => $address->getNumber(),
            'city' => $address->getCity(),
            'zip' => $address->getZip(),
            'display' => sprintf(
                '%s %s, %s %s',
                $address->getStreet(),
                $address->getNumber(),
                $address->getZip(),
                $address->getCity()
            ),
        ];
    }
}
