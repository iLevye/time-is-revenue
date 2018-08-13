<?php

namespace App\Repository;

use App\Entity\Time;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Time|null find($id, $lockMode = null, $lockVersion = null)
 * @method Time|null findOneBy(array $criteria, array $orderBy = null)
 * @method Time[]    findAll()
 * @method Time[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Time::class);
    }

//    /**
//     * @return Time[] Returns an array of Time objects
//     */
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


    /**
     * @param $task
     * @return Time|null
     */
    public function findRunningTime($task): ?Time
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.finishDate is null')
            ->andWhere('t.task = :task')
            ->setParameter("task", $task)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @param $task
     * @return int|null
     */
    public function sumSavedTimes($task): ?int
    {
        return $this->createQueryBuilder('t')
            ->select('SUM(timestampdiff(SECOND, t.startDate, t.finishDate))')
            ->andWhere('t.finishDate is not null')
            ->andWhere('t.task = :task')
            ->setParameter("task", $task)
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

}
