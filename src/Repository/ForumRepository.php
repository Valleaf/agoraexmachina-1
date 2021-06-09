<?php

namespace App\Repository;

use App\Entity\Forum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Forum|null find($id, $lockMode = null, $lockVersion = null)
 * @method Forum|null findOneBy(array $criteria, array $orderBy = null)
 * @method Forum[]    findAll()
 * @method Forum[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ForumRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Forum::class);
    }

    /**
     * Cette fonction permet de trouver tous les forums dans les categories ou l'utilisateur est enregistré
     * @param int $userId L'utilisateur sur qui on effectue la recherche
     * @return int|mixed|string Les forums demandés
     */
    public function findForumsInCategories(int $userId)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder();
        $query
            ->select('f')
            ->from('App\Entity\Forum', 'f')
            ->innerJoin('f.proposal','p')
            ->innerJoin('p.workshop','w')
            ->innerJoin('w.theme','t')
            ->innerJoin('t.category','c')
            ->innerJoin('c.users','u')
            ->andWhere('u.id IN (:userId)')
            ->setParameter('userId',$userId)
            #->andWhere('t.isPublic = true')
            #->andWhere('k.id = '.$id)
        ;
        return $query->getQuery()->getResult();
    }

}
