<?php

namespace App\Repository;

use App\Entity\Address;
use App\Entity\DateTimeContainer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method DateTimeContainer|null find($id, $lockMode = null, $lockVersion = null)
 * @method DateTimeContainer|null findOneBy(array $criteria, array $orderBy = null)
 * @method DateTimeContainer[]    findAll()
 * @method DateTimeContainer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DateTimeContainerRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, DateTimeContainer::class);
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
}
