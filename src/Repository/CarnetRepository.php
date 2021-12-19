<?php

namespace App\Repository;

use App\Entity\Carnet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Carnet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Carnet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Carnet[]    findAll()
 * @method Carnet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CarnetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Carnet::class);
    }
     /**
     * @return void
     */
    public function findbyActiveCarnet()
    {
        $query = $this->createQueryBuilder('c')
            ->select('COUNT(c)')
            ->andWhere('c.active = 1');
        return $query->getQuery()->getSingleScalarResult();
    }
    /**
     * @return void
     */
    public function findbyInactiveCarnet()
    {
        $query = $this->createQueryBuilder('c')
            ->select('COUNT(c)')
            ->andWhere('c.active = 0');
        return $query->getQuery()->getSingleScalarResult();
    }
    /**
     * @return void
     */
    public function getTotalCarnet()
    {
        $query = $this->createQueryBuilder('c')
            ->select('COUNT(c)');
            // ->andWhere('c.Active = 1');
            return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * @return void
     */
    public function getCarnetbyId($id)
    {
        $query = $this->createQueryBuilder('c')
            ->andWhere('c.id = :id')
            ->setParameter('id', $id)
            ;
        
            return $query->getQuery()->getResult();
    }
        /**
     * @return void
     */
    public function getTiketByCarnetId($matriculeCarnetid)
    {
        // $query = $this->createQueryBuilder('c')
        //     ->innerjoin('App\Entity\Tiket', 't', 'WITH', 'c.id')
        //     // ->where('c.id = t.matriculeCarnet')
        //     ->andWhere('t.matriculeCarnet = :id')
        //     ->setParameter('id', $matriculeCarnetid);
        $entityManager = $this->getEntityManager();


        $query = $entityManager->createQuery(
            'SELECT c, t 
            FROM App\Entity\Ticket t
            INNER JOIN t.carnet c
            WHERE t.carnet = :id'
        )
            ->setParameter('id', $matriculeCarnetid);

        // echo $query;
        // return $query->getQuery()->getResult();
        return $query->getResult();
    }
    // /**
    //  * @return Carnet[] Returns an array of Carnet objects
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
    public function findOneBySomeField($value): ?Carnet
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
