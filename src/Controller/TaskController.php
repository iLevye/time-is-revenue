<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\Time;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\TimeRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/task")
 */
class TaskController extends Controller
{
    /**
     * @Route("/", name="task_index", methods="GET")
     */
    public function index(TaskRepository $taskRepository): Response
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var TimeRepository $timeRepository */
        $timeRepository = $em->getRepository(Time::class);

        $tasks = $taskRepository->findAll();
        foreach($tasks as $task){
            $runningTime = $timeRepository->findRunningTime($task);
            if ($runningTime){
                $task->setIsRunning(true);

                $now = new \DateTime();
                $runningSeconds = $now->getTimestamp() - $runningTime->getStartDate()->getTimestamp();

                $savedTime = $timeRepository->sumSavedTimes($task);
                if (!$savedTime){
                    $savedTime = 0;
                }
                $task->setElapsedTime($savedTime + $runningSeconds);

                $task->setLastTimeStartedAt($runningTime->getStartDate());
            }else{
                $savedTime = $timeRepository->sumSavedTimes($task);
                $task->setElapsedTime(0);
                if ($savedTime){
                    $task->setElapsedTime($savedTime);
                }
            }
        }

        return $this->render('task/index.html.twig', ['tasks' => $tasks]);
    }

    /**
     * @Route("/new", name="task_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="task_show", methods="GET")
     */
    public function show(Task $task): Response
    {
        return $this->render('task/show.html.twig', ['task' => $task]);
    }

    /**
     * @Route("/{id}/edit", name="task_edit", methods="GET|POST")
     */
    public function edit(Request $request, Task $task): Response
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('task_edit', ['id' => $task->getId()]);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="task_delete", methods="DELETE")
     */
    public function delete(Request $request, Task $task): Response
    {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($task);
            $em->flush();
        }

        return $this->redirectToRoute('task_index');
    }

    /**
     * @Route("/{id}/start", name="start_timer")
     */
   public function startTimer(Request $request, Task $task){
       /** @var EntityManagerInterface $em */
       $em = $this->getDoctrine()->getManager();

       /** @var TimeRepository $timeRepository */
       $timeRepository = $em->getRepository(Time::class);
       $runningTime = $timeRepository->findRunningTime($task);
       if($runningTime){
           return $this->json([
               "success"    => false,
               "message"    => "Stop running time before start new one"
           ]);
       }

       $time = new Time();
       $time->setTask($task);

       $em->persist($time);
       $em->flush();

       return $this->json([
           "success" => true
       ]);
   }

    /**
     * @Route("/{id}/stop", name="stop_timer")
     */
    public function stopTimer(Request $request, Task $task){
        /** @var EntityManagerInterface $em */
        $em = $this->getDoctrine()->getManager();

        /** @var TimeRepository $timeRepository */
        $timeRepository = $em->getRepository(Time::class);

        $runningTime = $timeRepository->findRunningTime($task);
        if(!$runningTime){
            return $this->json([
                "success"    => false,
                "message"    => "Looks like start request was failed :( I don't know when you started this task. Sucks!"
            ]);
        }

        $runningTime->setFinishDate(new \DateTime('now'));

        $em->persist($runningTime);
        $em->flush();

        return $this->json([
            "success" => true
        ]);
    }
}
