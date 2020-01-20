<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\Time;
use App\Entity\User;
use App\Repository\TimeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatusController extends AbstractController
{
    /**
     * @Route("/status", name="status")
     */
    public function index()
    {
        /** @var TimeRepository $timeRepository */
        $timeRepository = $this->getDoctrine()->getRepository(Time::class);

        /** @var User $user */
        $user = $this->getUser();

        /** @var Time $task */
        $runningTime = $timeRepository->getRunningTime($user->getWorkspaces()[0]);

        $data = [
            //'icon_data'         => 'data:image/png;base64,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',
            'background_color'  => '255,85,100,255',
            "font_color"        => "100,200,100,255",
            "font_size"         => 13
        ];

        if(is_null($runningTime)){
            $data['icon_path'] = '/Users/emrecan/notrun.png';
            $data['text'] = 'NOT RUNNING';
            $data['background_color'] = '255,255,255,255';
            $data["font_color"] = "255,1,1,255";
        }else{
            $data['icon_path'] = '/Users/emrecan/running.png';
            $data['background_color'] = '255,255,255,255';
            $data['font_color'] = '40,167,69,255';
            $seconds = $timeRepository->sumSavedTimes($runningTime->getTask());
            $now = new \DateTime();
            $seconds += $now->getTimestamp() - $runningTime->getStartDate()->getTimestamp();
            $data['text'] = 'RUNNING ' .  $this->secondsToTime($seconds);
        }

        return new Response(json_encode($data));
    }

    function secondsToTime($inputSeconds) {

        $secondsInAMinute = 60;
        $secondsInAnHour  = 60 * $secondsInAMinute;
        $secondsInADay    = 24 * $secondsInAnHour;

        // extract days
        $days = floor($inputSeconds / $secondsInADay);

        // extract hours
        $hourSeconds = $inputSeconds % $secondsInADay;
        $hours = floor($hourSeconds / $secondsInAnHour);

        // extract minutes
        $minuteSeconds = $hourSeconds % $secondsInAnHour;
        $minutes = floor($minuteSeconds / $secondsInAMinute);

        // extract the remaining seconds
        $remainingSeconds = $minuteSeconds % $secondsInAMinute;
        $seconds = ceil($remainingSeconds);

        return sprintf( '%02d', $hours ) . ':' . sprintf( '%02d', $minutes );
    }
}
