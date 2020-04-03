<?php

namespace App\Repository;

use App\Entity\AsanaProject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method AsanaProject|null find($id, $lockMode = null, $lockVersion = null)
 * @method AsanaProject|null findOneBy(array $criteria, array $orderBy = null)
 * @method AsanaProject[]    findAll()
 * @method AsanaProject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AsanaProjectRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, AsanaProject::class);
    }

//    /**
//     * @return AsanaProject[] Returns an array of AsanaProject objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AsanaProject
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
