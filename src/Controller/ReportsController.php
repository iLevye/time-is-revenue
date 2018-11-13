<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\TimeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ReportsController extends AbstractController
{
    /**
     * @Route("/reports", name="reports")
     */
    public function index(TimeRepository $timeRepository)
    {
        /** @var User $user */
        $user = $this->getUser();
        $workspace = $user->getWorkspaces()[0];

        $data = $timeRepository->getRevenuesByProjects($workspace);

        $payments = $timeRepository->getRevenueAndPaymentsByClient($workspace);

        return $this->render('reports/index.html.twig', [
            'data' => $data,
            'payments' => $payments
        ]);
    }
}
