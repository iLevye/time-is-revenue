<?php

namespace App\Controller;

use App\Entity\AsanaProject;
use App\Entity\Project;
use App\Entity\Task;
use App\Entity\Time;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\TimeRepository;
use App\Services\Asana;
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
        /** @var User $user */
        $user = $this->getUser();
        $workspace = $user->getWorkspaces()[0];
        $taskRepository->setWorkspace($workspace);

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        /** @var TimeRepository $timeRepository */
        $timeRepository = $em->getRepository(Time::class);


        $tasks = $taskRepository->getLastTasks();
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

        $asanaTasks = [];
        if($user->getAsanaAccessToken()){
            $asanaProjectRepository = $this->getDoctrine()->getRepository(AsanaProject::class);
            $asanaProjects = $asanaProjectRepository->findBy([
                'user' => $user,
                'isPinned' => true
            ]);

            if(count($asanaProjects) > 0){
                $asana = new Asana($user->getAsanaAccessToken());
                foreach($asanaProjects as $asanaProject){
                    $items = $asana->getTasks($asanaProject->getAsanaId());
                    foreach($items as $item){

                        $item->project_name = $asanaProject->getProject()->getName();
                        dump($item);
                        $asanaTasks[] = $item;
                    }
                }
            }
        }

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'asanaTasks' => $asanaTasks,
            'asanaProjects' => $asanaProjects
        ]);
    }

    /**
     * @Route("/new", name="task_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $workspace = $user->getWorkspaces()[0];
        $projects = $this->getDoctrine()->getRepository(Project::class)->findBy(['workspace' => $workspace]);

        $task = new Task();
        $form = $this->createForm(TaskType::class, $task, ['projects' => $projects]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setWorkspace($workspace);
            $em = $this->getDoctrine()->getManager();
            $em->persist($task);

            $time = new Time();
            $time->setTask($task);
            $em->persist($time);

            $em->flush();

            return $this->redirectToRoute('task_index');
        }

        return $this->render('task/new.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
            'projects' => $projects
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
        /** @var User $user */
        $user = $this->getUser();
        $workspace = $user->getWorkspaces()[0];
        $projects = $this->getDoctrine()->getRepository(Project::class)->findBy(['workspace' => $workspace]);

        $form = $this->createForm(TaskType::class, $task, ['projects' => $projects]);
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

            /** @var TimeRepository $timeRepository */
            $timeRepository = $this->getDoctrine()->getRepository(Time::class);
            $times = $timeRepository->findBy([
                "task" => $task
            ]);
            if(count($times) > 0){
                foreach($times as $time){
                    $em->remove($time);
                }
            }

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

       if($task->getReceipt() != null){
           return $this->json([
               "success"    => false,
               "message"    => "This task invoiced. Can't start."
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
