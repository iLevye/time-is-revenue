<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\Task;
use App\Entity\Time;
use App\Entity\Workspace;
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

    public function getRevenuesByProjects(Workspace $workspace){
        return $this->createQueryBuilder('time')
            ->select('client.name as client_name, project.name as project_name, SUM(timestampdiff(SECOND, time.startDate, time.finishDate) / 60 / 60 * task.billableRate) as revenue')
            ->leftJoin('time.task', 'task')
            ->leftJoin('task.project', 'project')
            ->leftJoin('project.client', 'client')
            ->where('task.isBillable = :billable')
            ->andWhere('time.finishDate is not null')
            ->andWhere('client.workspace = :workspace')
            ->andWhere('project.isArchived = :isArchived')
            ->setParameters([
                'workspace' => $workspace,
                'billable' => true,
                'isArchived' => false
            ])
            ->groupBy('project.id')
            ->orderBy('client.id')
            ->getQuery()
            ->getArrayResult();
    }

    public function getRevenueAndPaymentsByClient(Workspace $workspace){
        return $this->createQueryBuilder('time')
            ->select('client.name as client_name, SUM(timestampdiff(SECOND, time.startDate, time.finishDate) / 60 / 60 * task.billableRate) as revenue')
            ->addSelect('(select sum(payment.amount) from App\Entity\Payment payment where payment.client = client) as paymentAmount')
            ->leftJoin('time.task', 'task')
            ->leftJoin('task.project', 'project')
            ->leftJoin('project.client', 'client')
            ->where('task.isBillable = :billable')
            ->andWhere('time.finishDate is not null')
            ->andWhere('client.workspace = :workspace')
            ->andWhere('client.isArchived = :isArchived')
            ->setParameters([
                'workspace' => $workspace,
                'billable' => true,
                'isArchived' => false
            ])
            ->groupBy('client.id')
            ->orderBy('client.id')
            ->getQuery()
            ->getArrayResult();
    }

    public function getClientTimesheet(Client $client, \DateTime $startDate, \DateTime $endDate, bool $uninvoicedRows){
        $query = $this->createQueryBuilder('time')
            ->select('receipt.id as receiptId, task.id as taskId, task.date, task.description, task.asanaUrl, project.name as project_name, client.name as client_name, sum(timestampdiff(SECOND, time.startDate, time.finishDate)) / 60 / 60 as hours, task.billableRate, task.isBillable')
            ->leftJoin('time.task', 'task')
            ->leftJoin('task.project', 'project')
            ->leftJoin('project.client', 'client')
            ->leftJoin('task.receipt', 'receipt')
            ->andWhere('time.finishDate is not null')
            ->andWhere('client = :client')
            ->andWhere('time.startDate > :startDate')
            ->andWhere('time.startDate < :endDate');

            if ($uninvoicedRows){
                $query->andWhere('task.receipt is null');
            }

            $query->setParameters([
                'client' => $client,
                'startDate' => $startDate,
                'endDate' => $endDate
            ])
            ->groupBy('task.id')
            ->orderBy('task.id', 'desc');

        return $query->getQuery()
            ->getArrayResult();
    }

}
