<?php

namespace App\Repository;

use App\Entity\Shift;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method Shift|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shift|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shift[]    findAll()
 * @method Shift[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null) 
 */
class ShiftRepository extends ServiceEntityRepository
{

    public function getRotaRange($em, $dateFrom, $dateTo)
    {/*quering all rotas form a range of dates*/
        // $sql = "SELECT s FROM App\Entity\Shift s WHERE s.startShift >= ?1 AND s.startShift <= ?2";
        $sql = "SELECT s FROM App\Entity\Shift s WHERE s.date >= ?1 AND s.date <= ?2";
  
        

        $query = $em->createQuery($sql);
        $query->setParameter(1, $dateFrom);
        $query->setParameter(2, $dateTo);
    
        
        
        $rotasRange = $query->getResult();
        return $rotasRange;
    }
    public function getRotaRangeWorker($em, $dateFrom, $dateTo, $workerId)
    {/* quering rota from a worker in a range of dates*/
        $sql = "SELECT s FROM App\Entity\Shift s WHERE s.worker = ?3 AND s.date >= ?1 AND s.date <= ?2";
        

        $query = $em->createQuery($sql);
        $query->setParameter(1, $dateFrom);
        $query->setParameter(2, $dateTo);
        $query->setParameter(3, $workerId);
        
        
        $rotasRange = $query->getResult();
        return $rotasRange;
    }
    public function getRotaBranch($em, $dateFrom, $dateTo, $branchId)
    {/* quering rota from a branch in a range of dates*/
        $sql = "SELECT s FROM App\Entity\Shift s WHERE s.branch = ?3 AND s.date >= ?1 AND s.date <= ?2";
        

        $query = $em->createQuery($sql);
        $query->setParameter(1, $dateFrom);
        $query->setParameter(2, $dateTo);
        $query->setParameter(3, $branchId);
        
        
        $rotasRange = $query->getResult();
        return $rotasRange;
    }
    public function findDaysOff($em, $id)
    {
        $sql = "SELECT s FROM App\Entity\Shift s WHERE s.active = ?1 AND s.worker = ?2";
        
        $query = $em->createQuery($sql);
        $query->setParameter(1, false);
        $query->setParameter(2, $id);
        
        $shiftsOff = $query->getResult();
        return $shiftsOff;
    }

    public function matchOff($em, $date, $shiftType, $id)
    {
        $sql = "SELECT s FROM App\Entity\Shift s WHERE s.date = ?1 AND s.shiftType = ?2 AND s.worker = ?3";
        
        $query = $em->createQuery($sql);
        $query->setParameter(1, $date);
        $query->setParameter(2, $shiftType);
        $query->setParameter(3, $id);
        
        $shiftOff = $query->getResult();
        return $shiftOff;
    }
    public function findSwappingByJobId($em, $job, $id)
    {
        $sql = "SELECT s, sw FROM App\Entity\Shift s JOIN s.worker sw WHERE s.swapping = ?1 AND sw.job = ?2 AND s.worker != ?3";
        
        $query = $em->createQuery($sql);
        $query->setParameter(1, 1);
        $query->setParameter(2, $job);
        $query->setParameter(3, $id);
        
        $swappingShifts = $query->getResult();
        return $swappingShifts;
    }

























    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shift::class);
    }

    // /**
    //  * @return Shift[] Returns an array of Shift objects
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
    public function findOneBySomeField($value): ?Shift
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
