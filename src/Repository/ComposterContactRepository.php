<?php

namespace App\Repository;

use App\Entity\ComposterContact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ComposterContact|null find($id, $lockMode = null, $lockVersion = null)
 * @method ComposterContact|null findOneBy(array $criteria, array $orderBy = null)
 * @method ComposterContact[]    findAll()
 * @method ComposterContact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComposterContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ComposterContact::class);
    }

    // /**
    //  * @return ComposterContact[] Returns an array of ComposterContact objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ComposterContact
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
