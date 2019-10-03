<?php

namespace App\Repository;

use App\Entity\Permanence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Permanence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Permanence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Permanence[]    findAll()
 * @method Permanence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PermanenceRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Permanence::class);
    }

    /**
     * @throws
     * @return Permanence[] Returns an array of Permanence objects
     */
    public function findAllToNotify()
    {

        $today = new \DateTime();
        $dateMax = new \DateTime();
        $dateMax->add( new \DateInterval( 'P1W'));

        return $this->createQueryBuilder('p')
            ->andWhere('p.date > :date')
            ->andWhere('p.date < :dateMax')
            ->andWhere('p.hasUsersBeenNotify = :has_users_been_notify')
            ->setParameter('date', $today )
            ->setParameter('dateMax', $dateMax )
            ->setParameter('has_users_been_notify', false )
            ->orderBy('p.date', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Permanence
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
