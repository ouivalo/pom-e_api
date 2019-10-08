<?php

namespace App\Repository;

use App\Entity\PavilionsVolume;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method PavilionsVolume|null find($id, $lockMode = null, $lockVersion = null)
 * @method PavilionsVolume|null findOneBy(array $criteria, array $orderBy = null)
 * @method PavilionsVolume[]    findAll()
 * @method PavilionsVolume[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PavilionsVolumeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PavilionsVolume::class);
    }

    // /**
    //  * @return PavilionsVolume[] Returns an array of PavilionsVolume objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PavilionsVolume
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
