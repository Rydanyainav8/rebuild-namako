<?php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }
    /**
     * @return void
     */
    public function getPaginatedTickets($page, $limit, $filters = null)
    {
        $query = $this->createQueryBuilder('t');
        if ($filters != null) {
            $query->andWhere('t.carnet IN(:cars)')
                ->setParameter(':cars', $filters);
        }
        $query->orderBy('t.id', 'DESC')
            // ->where('t.active = 0')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit);
        return $query->getQuery()->getResult();
    }
    /**
     * @return void
     */
    public function getPaginatedInActivateTickets($page, $limit, $filters = null)
    {
        $query = $this->createQueryBuilder('t');
        if ($filters != null) {
            $query->andWhere('t.carnet IN(:cars)')
                ->setParameter(':cars', $filters);
        }
        $query->orderBy('t.id', 'DESC')
            ->where('t.active = 0')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit);
        return $query->getQuery()->getResult();
    }
    /**
     * @return void
     */
    public function getTotalTickets($filters = null)
    {
        $query = $this->createQueryBuilder('t')
            ->select('COUNT(t)');
        if ($filters != null) {
            $query->andWhere('t.carnet IN(:cars)')
                ->setParameter(':cars', $filters);
        }
        return $query->getQuery()->getSingleScalarResult();
    }
    /**
     * @return Ticket[]
     */

    public function desactiveTicket($numero)
    {
        // $entityManager = $this->getEntityManager();
        // $query = $entityManager->createQuery(
        //     'UPDATE t 
        //     SET t.active = 1
        //     WHERE t.numero = :numero'
        // )
        $query = $this->createQueryBuilder('t')
            ->update()
            ->set('t.active', 1)
            ->where('t.numero = :numero')
            ->setParameter('numero', $numero);
        return $query->getQuery()->getResult();
    }
    /**
     * @return Ticket[]
     */
    public function FindNumero($numero)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.numero = :numero')
            ->setParameter('numero', $numero)
            ->getQuery() 
            ->getOneOrNullResult();
    }
    
    /**
     * @return void
     */
    public function searchTicket($mots)
    {
        $query = $this->createQueryBuilder('t');

        if ($mots != null) {
            // $query->andWhere('MATCH_AGAINST(t.numerox) AGAINST(:mots boolean)>0')
            //     ->setParameter('mots', $mots);
            // $query->andWhere('MATCH_AGAINST(t.numero) AGAINST(:mots boolean)>0')
            //     ->setParameter('mots', $mots);
            $query->andWhere('t.numero LIKE :mots')
                ->setParameter('mots', "%{$mots}%");
        }
        return $query->getQuery()->getResult();
    }
    /**
     * @return void
     */
    public function findbyActiveTicket()
    {
        $query = $this->createQueryBuilder('t')
            ->select('COUNT(t)')
            ->andWhere('t.active = 1');
        return $query->getQuery()->getSingleScalarResult();
    }
    /**
     * @return void
     */
    public function getTotalTicket()
    {
        $query = $this->createQueryBuilder('t')
            ->select('COUNT(t)');
        // ->andWhere('c.Active = 1');
        return $query->getQuery()->getSingleScalarResult();
    }
    /**
     * @return void
     */
    public function findbyInactiveTicket()
    {
        $query = $this->createQueryBuilder('t')
            ->select('COUNT(t)')
            ->andWhere('t.active = 0');
        return $query->getQuery()->getSingleScalarResult();
    }
    /**
     * @return void
     */

    public function desactiveTiket($numero)
    {
        $query = $this->createQueryBuilder('t')
            ->update()
            ->set('t.active', 1)
            ->where('t.numero = :numero')
            ->setParameter('numero', $numero);
        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return Ticket[] Returns an array of Ticket objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ticket
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
