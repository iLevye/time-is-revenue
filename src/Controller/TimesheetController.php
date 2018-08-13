<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TimesheetController extends AbstractController
{
    /**
     * @Route("/timesheet", name="timesheet")
     */
    public function index()
    {
        return $this->render('timesheet/index.html.twig', [
            'controller_name' => 'TimesheetController',
        ]);
    }
}
