<?php

namespace App\Repository;

use App\Entity\ChatType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ChatType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ChatType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ChatType[]    findAll()
 * @method ChatType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChatTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChatType::class);
    }

    // /**
    //  * @return ChatType[] Returns an array of ChatType objects
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
    public function findOneBySomeField($value): ?ChatType
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
