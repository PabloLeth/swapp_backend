<?php

namespace App\Repository;

use App\Entity\ShiftType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShiftType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShiftType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShiftType[]    findAll()
 * @method ShiftType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShiftTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShiftType::class);
    }

    // /**
    //  * @return ShiftType[] Returns an array of ShiftType objects
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
    public function findOneBySomeField($value): ?ShiftType
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
