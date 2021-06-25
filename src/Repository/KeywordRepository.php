<?php

namespace App\Repository;

use App\Entity\Keyword;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Keyword|null find($id, $lockMode = null, $lockVersion = null)
 * @method Keyword|null findOneBy(array $criteria, array $orderBy = null)
 * @method Keyword[]    findAll()
 * @method Keyword[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class KeywordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Keyword::class);
    }

    /**
     * Permet de récupérer les mots-clé liés à un atelier depuis la BDD grâce à un paramètre donné
     * @param int $id En paramètre l'identifiant du mot-clé recherché
     * @return int|mixed|string Retourne le mot-clé en son entité Keyword.
     */
    public function findByWorkshopId(int $id)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder();
        $query
            ->select('k')
            ->from('App:Keyword', 'k')
            ->innerJoin('k.workshops', 'w')
            ->andWhere('w.id =' . $id);
        return $query->getQuery()->getResult();
    }

    /**
     * Cette fonction renvoie les mots cles qui commencent par $str envoyé en paramètre
     * @param string $str
     */
    public function findKeywordsStartingWith(string $str)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder();
        $query
            ->select('k')
            ->from('App:Keyword', 'k')
            ->andWhere('k.name LIKE :string')
            ->setParameter('string', $str.'%');
        return $query->getQuery()->getResult();

    }

    // /**
    //  * @return Keyword[] Returns an array of Keyword objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('k.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Keyword
    {
        return $this->createQueryBuilder('k')
            ->andWhere('k.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
