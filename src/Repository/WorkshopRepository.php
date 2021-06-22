<?php

namespace App\Repository;

use App\Entity\Workshop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Workshops|null find($id, $lockMode = null, $lockVersion = null)
 * @method Workshops|null findOneBy(array $criteria, array $orderBy = null)
 * @method Workshops[]    findAll()
 * @method Workshops[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkshopRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Workshop::class);
    }

    public function findAllWorkshops()
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder();
        $query
            ->select('w','t','c','p','f')
            ->from('App:Workshop', 'w')
            ->leftJoin('w.theme','t')
            ->leftJoin('t.category','c')
            ->leftJoin('w.proposals','p')
            ->leftJoin('p.forums','f')
        ;
        return $query->getQuery()->getResult();
    }
	public function searchBy(array $filters)
	{
		$entityManager = $this->getEntityManager();
		$query = $entityManager->createQueryBuilder();
		$query
            ->select('w','t','c','p','f')
            ->from('App:Workshop', 'w')
            ->leftJoin('w.theme','t')
            ->leftJoin('t.category','c')
            ->leftJoin('w.proposals','p')
            ->leftJoin('p.forums','f');
		
		foreach($filters as $key => $value)
		{
			if (gettype($value) == "object")
			{
				$query->andWhere('w.'.$key .' = :' . $key);
				$query->setParameter($key, $value);
			}
			else
			{
				$query->andWhere('w.'.$key .' LIKE :' . $key);
				$query->setParameter($key, '%'.$value.'%');
			}
		}
		
		dump($query);
	
		return $query->getQuery()->getResult();
	}

	public function searchByKeyword(int $id,int $userId)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder();
        $query
            ->select('w,c,t')
            ->from('App\Entity\Workshop', 'w')
            ->innerJoin('w.keywords','k')
            ->innerJoin('w.theme','t')
            ->innerJoin('t.category','c')
            ->innerJoin('c.users','u')
            ->andWhere('u.id IN (:userId)')
            ->setParameter('userId',$userId)
            ->andWhere('t.isPublic = true')
            ->andWhere('k.id = '.$id);
        dump($query);
        return $query->getQuery()->getResult();
    }

    public function findWorkshopsInCategories(int $userId)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder();
        $query
            ->select('w,c,t')
            ->from('App\Entity\Workshop', 'w')
            ->innerJoin('w.keywords','k')
            ->innerJoin('w.theme','t')
            ->innerJoin('t.category','c')
            ->innerJoin('c.users','u')
            ->andWhere('u.id IN (:userId)')
            ->setParameter('userId',$userId)
            ->andWhere('t.isPublic = true')
            #->andWhere('k.id = '.$id)
        ;
        dump($query);
        return $query->getQuery()->getResult();
    }


}
