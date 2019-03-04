<?php

namespace App\Repository;

use App\Entity\SpeedCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method SpeedCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method SpeedCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method SpeedCategory[]    findAll()
 * @method SpeedCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpeedCategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, SpeedCategory::class);
    }

    // /**
    //  * @return SpeedCategory[] Returns an array of SpeedCategory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SpeedCategory
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
