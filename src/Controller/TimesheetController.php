<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Receipt;
use App\Entity\Task;
use App\Entity\User;
use App\Form\TimesheetFilterType;
use App\Repository\TimeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TimesheetController extends AbstractController
{
    /**
     * @Route("/timesheet", name="timesheet")
     */
    public function index(TimeRepository $timeRepository, Request $request)
    {

        /** @var User $user */
        $user = $this->getUser();
        $workspace = $user->getWorkspaces()[0];

        $clientRepository = $this->getDoctrine()->getRepository(Client::class);
        $clients = $clientRepository->findBy(['workspace' => $workspace]);
        $filterData['client'] = $clients[0];
        $filterData['startDate'] = new \DateTime();
        $filterData['endDate'] = new \DateTime();
        $filterData['uninvoicedRows'] = false;


        /** @var TimesheetFilterType $form */
        $form = $this->createForm(TimesheetFilterType::class, $filterData, ['clients' => $clients]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filterData = $form->getData();
        }

        $data = $timeRepository->getClientTimesheet($filterData['client'], $filterData['startDate'], $filterData['endDate'], $filterData['uninvoicedRows']);

        $totalPrice = 0;
        $totalHour = 0;
        if($form->get('saveInvoice')->isClicked()){
            $receipt = new Receipt();
            $receipt->setClient($filterData['client']);
            $receipt->setUser($user);
            $receipt->setStartDate($filterData['startDate']);
            $receipt->setEndDate($filterData['endDate']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($receipt);

            $taskRepository = $this->getDoctrine()->getRepository(Task::class);
            foreach($data as $d){
                if($d['receiptId'] == null){
                    $task = $taskRepository->find($d['taskId']);
                    if (!$task->getIsBillable()){
                        continue;
                    }
                    $totalHour += $d['hours'];
                    $totalPrice += $d['hours'] * $d['billableRate'];
                    $task->setReceiptTime($d['hours']);
                    $task->setReceiptPrice($d['hours'] * $d['billableRate']);
                    $task->setReceipt($receipt);
                    $em->persist($task);
                }
            }
            $receipt->setTotalHours($totalHour);
            $receipt->setTotalPrice($totalPrice);

            $em->persist($receipt);
            $em->flush();
            return $this->redirectToRoute('receipt_show', ["id" => $receipt->getId()]);
        }else{

            foreach($data as $d){
                if($d['receiptId'] == null){
                    $totalHour += $d['hours'];
                    $totalPrice += $d['hours'] * $d['billableRate'];
                }
            }

            return $this->render('timesheet/index.html.twig', [
                'clients' => $clients,
                'data' => $data,
                'form' => $form->createView(),
                'totalHours' => $totalHour,
                'totalPrice' => $totalPrice
            ]);
        }
    }
}
