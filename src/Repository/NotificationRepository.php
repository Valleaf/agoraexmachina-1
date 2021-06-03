<?php

namespace App\Repository;

use App\Entity\Notification;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Notification|null find($id, $lockMode = null, $lockVersion = null)
 * @method Notification|null findOneBy(array $criteria, array $orderBy = null)
 * @method Notification[]    findAll()
 * @method Notification[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

     /**
      * Permet de trouver les notifications appartenant à un utilisateur
     * @return Notification[] Returns an array of Notification objects - Toutes les notifications appartenant à un
      * utilisateur
     */
    public function findByUserId($id): array
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder();
        $query
            ->select('n')
            ->from('App:Notification','n')
            ->innerJoin('n.user','user')
            ->andWhere('user.id = '.$id)
            ->orderBy('n.date')
        ;
        return $query->getQuery()->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Notification
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
