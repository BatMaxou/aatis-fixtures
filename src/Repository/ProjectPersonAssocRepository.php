<?php

namespace Aatis\FixturesBundle\Repository;

use Aatis\FixturesBundle\Entity\ProjectPersonAssoc;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProjectPersonAssoc>
 *
 * @method ProjectPersonAssoc|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectPersonAssoc|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectPersonAssoc[]    findAll()
 * @method ProjectPersonAssoc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectPersonAssocRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectPersonAssoc::class);
    }

    public function save(ProjectPersonAssoc $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ProjectPersonAssoc $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    //    /**
    //     * @return ProjectPersonAssoc[] Returns an array of ProjectPersonAssoc objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ProjectPersonAssoc
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
