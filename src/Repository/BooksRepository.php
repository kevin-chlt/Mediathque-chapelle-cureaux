<?php

namespace App\Repository;

use App\Data\FiltersBooks;
use App\Entity\Books;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Books|null find($id, $lockMode = null, $lockVersion = null)
 * @method Books|null findOneBy(array $criteria, array $orderBy = null)
 * @method Books[]    findAll()
 * @method Books[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BooksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Books::class);
    }

    public function getBookByCategory(FiltersBooks $filtersBooks) : Query
    {
        $query = $this->createQueryBuilder('b')
                ->select('c', 'b')
                ->join('b.categories', 'c');

            if(!empty($filtersBooks->getCategory())){
                $query = $query
                    ->andWhere('c.id IN (:categories)')
                    ->setParameter('categories', $filtersBooks->category)
                    ->orderBy('b.id', 'DESC');
            }
        return $query->getQuery();
    }

    public function getBooksByIsFree () : Query
    {
        return $this->createQueryBuilder('b')
            ->select('b')
            ->orderBy('b.isFree', 'DESC')
            ->getQuery();
    }


    /*
    public function findOneBySomeField($value): ?Books
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
