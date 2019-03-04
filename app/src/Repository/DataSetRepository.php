<?php

namespace App\Repository;

use App\Entity\Address;
use App\Entity\DataSet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DataSet|null find($id, $lockMode = null, $lockVersion = null)
 * @method DataSet|null findOneBy(array $criteria, array $orderBy = null)
 * @method DataSet[]    findAll()
 * @method DataSet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DataSetRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DataSet::class);
    }

    /**
     * @param Address $address
     * @param \DateTime $from
     * @param \DateTime $to
     * @return array
     */
    public function findBetween(Address $address, \DateTime $from, \DateTime $to): array
    {
        return $this->createQueryBuilder('x')
            ->andWhere('x.address = :address')
            ->andWhere('x.dateTime BETWEEN :from AND :to')
            ->setParameter('address', $address)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getQuery()
            ->getResult();
    }

    // /**
    //  * @return DataSet[] Returns an array of DataSet objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DataSet
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
