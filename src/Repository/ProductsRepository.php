<?php

namespace App\Repository;

use App\Entity\Products;
use App\Entity\Search;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Products|null find($id, $lockMode = null, $lockVersion = null)
 * @method Products|null findOneBy(array $criteria, array $orderBy = null)
 * @method Products[]    findAll()
 * @method Products[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Products::class);
    }

    /**
     * @return Products[] Returns an array of Products objects
     */

    public function findOneById($id)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.id = :val')
            ->setParameter('val', $id)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }


    public function FindBySearch(Search $search)
    {
        $query = $this->createQueryBuilder('a');

        if ($search->getSearchPrixMin()) {
            $query = $query->andWhere('a.Price >= :searchbienprixmin')
                ->setParameter('searchbienprixmin', $search->getSearchPrixMin());
        }

        if ($search->getSearchPrixMax()) {
            $query = $query->andWhere('a.Price <= :searchbienprixmax')
                ->setParameter('searchbienprixmax', $search->getSearchPrixMax());
        }
        if ($search->getSearchGlobal()) {
            $query = $query->andWhere('a.Title LIKE :searchsearchglobal OR a.Description LIKE :searchsearchglobal')
                ->setParameter('searchsearchglobal', '%' . addcslashes($search->getSearchGlobal(), '%_') . '%');
        }

        return $query->getQuery();
    }
    /*
    public function findOneBySomeField($value): ?Products
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
