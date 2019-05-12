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


        $data = $timeRepository->getDailyProjectHours($workspace, new \DateTime('30 day ago'), new \DateTime('now'));
        $monthlyTotal = 0;
        $weeklyTotal = 0;
        $lastWeek = new \DateTime('1 week ago');
        foreach($data as $d){
            $monthlyTotal += $d['hours'];
            if($d['date'] >= $lastWeek->format('Y-m-d')){
                $weeklyTotal += $d['hours'];
            }
        }

        $data['dailyAvg'] = array(
            "last30day"  => $monthlyTotal / 22,
            "lastWeek" => $weeklyTotal / 5
        );

        $data['totals'] = array(
            "monthlyTotal" => $monthlyTotal,
            "weeklyTotal" => $weeklyTotal
        );

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
        $data = $timeRepository->getDailyProjectHours($workspace, new \DateTime('30 day ago'), new \DateTime('now'));

        return $this->json($data);
    }
}
