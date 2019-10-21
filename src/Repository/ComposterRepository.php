<?php


namespace App\Repository;


use App\Entity\Composter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class ComposterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Composter::class);
    }


    public function findAllForFrontMap(){

        return $this->createQueryBuilder('c')
            ->andWhere('c.lat IS NOT NULL')
            ->andWhere('c.lng IS NOT NULL')
            ->getQuery()
            ->getResult();
    }
}