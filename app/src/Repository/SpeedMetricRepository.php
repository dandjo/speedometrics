<?php

namespace App\Repository;

use App\Entity\SpeedMetric;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SpeedMetric|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpeedMetric|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpeedMetric[]    findAll()
 * @method SpeedMetric[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpeedMetricRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SpeedMetric::class);
    }
}
