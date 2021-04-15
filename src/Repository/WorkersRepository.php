<?php

namespace App\Repository;

use App\Entity\Workers;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Workers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Workers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Workers[]    findAll()
 * @method Workers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkersRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Workers::class);
    }

    /**
    * Used to upgrade (rehash) the user's password automatically over time.
    */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof Worker) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    public function getWorkersBranch($em, $branchId)
    {
        
        $sql = "SELECT w FROM App\Entity\Workers w WHERE w.branch = ?1";
        

        $query = $em->createQuery($sql);
        $query->setParameter(1, $branchId);
        
        
        $workers = $query->getResult();
        return $workers;
    }

    public function getManagers($em,$branchId)
    {
        $sql = "SELECT w FROM App\Entity\Workers w Where w.branch = ?1 AND w.roles LIKE ?2";

        $query = $em->createQuery($sql);
        $query->setParameter(1, $branchId);
        $query->setParameter(2,  '%"ROLE_MANAGER"%');
       
        
        $managers = $query->getResult();
        
        return $managers;
    }
    









    // /**
    //  * @return Workers[] Returns an array of Workers objects
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
    public function findOneBySomeField($value): ?Workers
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
