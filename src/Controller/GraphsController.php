<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\TimeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GraphsController extends AbstractController
{
    /**
     * @Route("/graphs", name="graphs")
     */
    public function index(TimeRepository $timeRepository)
    {
        /** @var User $user */
        $user = $this->getUser();
        $workspace = $user->getWorkspaces()[0];


        $data = $timeRepository->getDailyProjectHours($workspace, new \DateTime('2019-01-01'), new \DateTime('2019-02-15'));

        return $this->render('graphs/index.html.twig', [
            'controller_name' => 'GraphsController',
            'data'              => $data
        ]);
    }

    /**
     * @Route("/graphs.json", name="graphs_json")
     */
    public function data(TimeRepository $timeRepository){
        /** @var User $user */
        $user = $this->getUser();
        $workspace = $user->getWorkspaces()[0];
        $data = $timeRepository->getDailyProjectHours($workspace, new \DateTime('2019-01-01'), new \DateTime('2019-02-15'));

        return $this->json($data);
    }
}
