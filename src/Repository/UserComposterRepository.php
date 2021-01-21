<?php

namespace App\Repository;

use App\Entity\UserComposter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserComposter|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserComposter|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserComposter[]    findAll()
 * @method UserComposter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserComposterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserComposter::class);
    }

    // /**
    //  * @return UserComposter[] Returns an array of UserComposter objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserComposter
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
