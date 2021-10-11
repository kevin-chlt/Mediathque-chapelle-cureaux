<?php

namespace App\Repository;

use App\Entity\BooksReservations;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BooksReservations|null find($id, $lockMode = null, $lockVersion = null)
 * @method BooksReservations|null findOneBy(array $criteria, array $orderBy = null)
 * @method BooksReservations[]    findAll()
 * @method BooksReservations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BooksReservationsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BooksReservations::class);
    }


    public function getReservationOutdated(\DateTime $dateMax) : ?array
    {
        return $this->createQueryBuilder('b')
            ->where('b.reservedAt >= :dateMax')
            ->setParameter('val', $dateMax)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?BooksReservations
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
