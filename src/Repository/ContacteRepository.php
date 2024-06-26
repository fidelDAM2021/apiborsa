<?php

namespace App\Repository;

use App\Entity\Contacte;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Contacte>
 *
 * @method Contacte|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contacte|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contacte[]    findAll()
 * @method Contacte[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContacteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contacte::class);
    }

    public function save(Contacte $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Contacte $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function createOrderedByNameQueryBuilder(string $lletra=null): QueryBuilder {
        $queryBuilder=$this->addOrderByNameQueryBuilder();
        if($lletra) {
            $lletra=$lletra."%";
            $queryBuilder->andWhere('nomcontacte like :lletra')
            ->setParameter('lletra',$lletra);
        }
        return $queryBuilder;
    }
    public function addOrderByNameQueryBuilder(QueryBuilder $queryBuilder=null): QueryBuilder {
        $queryBuilder=$queryBuilder ?? $this->createQueryBuilder('contacte');
        return $queryBuilder->orderBy('contacte.nomcontacte','ASC');
    }
//    /**
//     * @return Contacte[] Returns an array of Contacte objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Contacte
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
