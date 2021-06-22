<?php

namespace App\Repository;

use App\Entity\Theme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Theme|null find($id, $lockMode = null, $lockVersion = null)
 * @method Theme|null findOneBy(array $criteria, array $orderBy = null)
 * @method Theme[]    findAll()
 * @method Theme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ThemeRepository extends ServiceEntityRepository
{

    /**
     * Cette fonction permet de recuperer tous les themes et leurs associations, pour limiter le nombre de queries a
     * la BDD
     * @return int|mixed|string
     */
    public function findAllThemes()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder();
        $query
            ->select('t','c','w','p','f')
            ->from('App\Entity\Theme', 't')
            ->leftJoin('t.category','c')
            ->leftJoin('t.workshops','w')
            ->leftJoin('w.proposals','p')
            ->leftJoin('p.forums','f')
            ->orderBy('c.name')
            ;
        dump($query);
        return $query->getQuery()->getResult();
    }

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Theme::class);
    }
}
