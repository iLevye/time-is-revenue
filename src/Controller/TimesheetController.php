<?php

namespace App\Controller;

use App\Entity\Client;
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


        /** @var ProjectType $form */
        $form = $this->createForm(TimesheetFilterType::class, $filterData, ['clients' => $clients]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filterData = $form->getData();

        }

        $data = $timeRepository->getClientTimesheet($filterData['client'], $filterData['startDate'], $filterData['endDate']);

        return $this->render('timesheet/index.html.twig', [
            'clients' => $clients,
            'data' => $data,
            'form' => $form->createView()
        ]);
    }
}
