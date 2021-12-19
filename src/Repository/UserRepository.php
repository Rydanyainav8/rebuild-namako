<?php

namespace App\Repository;

use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }
    /**
     * @return void
     */
    public function getPaginatedusers($page, $limit, $filters = null)
    {
        $query = $this->createQueryBuilder('u');
        if ($filters != null) {
            $query->andWhere('u.gender IN(:gen)')
                ->setParameter(':gen', $filters);
        }
        $query->orderBy('u.id', 'DESC')
            ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit);
        return $query->getQuery()->getResult();
    }
    /**
     * @return void
     */
    public function getTotalusers($filters)
    {
        $query = $this->createQueryBuilder('u')
            ->select('COUNT(u)');
        if ($filters != null) {
            $query->andWhere('u.gender IN(:gen)')
                ->setParameter(':gen', $filters);
        }
        return $query->getQuery()->getSingleScalarResult();
    }
    /**
     * @return void
     */
    public function search($mots)
    {
        $query = $this->createQueryBuilder('u');

        // if ($mots != null) {
        //     $query->andWhere('MATCH_AGAINST(b.firstname, b.lastname, b.matricule) AGAINST(:mots boolean)>0')
        //         ->setParameter('mots', "%{$mots}%");
        // }
        // if ($mots != null) {
        //     $query->andWhere('(b.firstname, b.lastname, b.matricule) LIKE(:mots boolean)>0')
        //         ->setParameter('mots', "%{$mots}%");
        // }
        if ($mots != null) {
            $query->andWhere('u.firstname LIKE :mots OR u.lastname LIKE :mots OR u.matricule LIKE :mots')
                ->setParameter('mots', "%{$mots}%");
        }

        return $query->getQuery()->getResult();
    }
    /**
     * @return void
     */
    public function getuserId($id)
    {
        // $query = $this->createQueryBuilder('b')
        //     ->where('b.id = :id')
        //     ->innerJoin(Tiket::class, 
        //                     't', 
        //                     Join::WITH,
        //                     'b.id = t.badgeId'                 
        //     )
        //     // ->andWhere(' App\Entity\Tiket t, t.randomNumber = 0')
        //     ->setParameter(':id', $id);
        //     echo $query;
        // dd($query);

        // $entityManager = $this->getEntityManager();
        // $query = $entityManager->createQuery(
        //     // 'SELECT b, t 
        //     // FROM App\Entity\Tiket t
        //     // INNER JOIN t.badge b
        
        //     // WHERE b.id = :id
        //     // AND t.active = 0')
        //     'SELECT b
        //     FROM App\Entity\Badge b
        //     WHERE b.id = :id
        //     AND App\Entity\Tiket t.active = 0
        //     ')
        //     ->setParameter(':id', $id);

        $query = $this->createQueryBuilder('u')
                ->join(Ticket::class, 't')
                ->andWhere('u.id = :id')
                ->setParameter(':id', $id);
        return $query->getQuery()->getResult();
    }
    // /**
    //  * @return User[] Returns an array of User objects
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
    public function findOneBySomeField($value): ?User
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
