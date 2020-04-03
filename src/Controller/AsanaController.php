<?php

namespace App\Controller;

use App\Entity\AsanaProject;
use App\Entity\InvoiceSettings;
use App\Entity\Project;
use App\Entity\User;
use App\Form\AsanaAccessTokenType;
use App\Form\InvoiceSettingsType;
use App\Services\Asana;
use Asana\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AsanaController extends AbstractController
{

    /**
     * @Route("/asana", name="asana")
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();


        $form = $this->createForm(AsanaAccessTokenType::class, $user);
        $form->handleRequest($request);

        $updateStatus = false;
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $updateStatus = true;
        }

        $workspaces = [];
        if($user->getAsanaAccessToken() != ''){
            $asana = new Asana($user->getAsanaAccessToken());
            $workspaces = $asana->getWorkspaces();
        }

        $asanaProjects = $this->getDoctrine()->getRepository(AsanaProject::class)->findBy([
            'user' => $user
        ]);

        return $this->render('asana/index.html.twig', [
            'form' => $form->createView(),
            'updateStatus' => $updateStatus,
            'workspaces' => $workspaces,
            'asanaProjects' => $asanaProjects
        ]);
    }

    /**
     * @Route("/asana/workspaces", name="list_workspaces")
     */
    public function workspaces()
    {
        $user = $this->getUser();
        $asana = new Asana($user->getAsanaAccessToken());
        $workspaces = $asana->getWorkspaces();
        return $this->render('asana/workspaces.html.twig', [
            'workspaces' => $workspaces
        ]);
    }

    /**
     * @Route("/asana/workspaces/{workspaceId}/projects", name="list_projects")
     */
    public function projects($workspaceId)
    {
        $projectRepository = $this->getDoctrine()->getRepository(Project::class);
        $user = $this->getUser();
        $asana = new Asana($user->getAsanaAccessToken());
        $projects = $asana->getProjects($workspaceId);
        $workspace = $user->getWorkspaces()[0];
        return $this->render('asana/projects.html.twig', [
            'projects' => $projects,
            'timerProjects' => $projectRepository->findBy(['workspace' => $workspace])
        ]);
    }


    /**
     * @Route("/asana/projects/{projectId}/tasks", name="list_tasks")
     */
    public function tasks($projectId)
    {
        $user = $this->getUser();
        $asana = new Asana($user->getAsanaAccessToken());
        $asanaProject = $asana->getProject($projectId);
        $items = $asana->getTasks($projectId);
        $tasks = [];

        foreach($items as $item){
            $item->project_name = $asanaProject->name;
            $tasks[] = $item;
        }

        return $this->render('asana/tasks.html.twig', [
            'tasks' => $tasks
        ]);
    }

    /**
     * @Route("/asana/projects/{asanaProjectId}/pin/{projectId}", name="pin_project")
     */
    public function pinProject($asanaProjectId, $projectId)
    {
        $user = $this->getUser();
        $asana = new Asana($user->getAsanaAccessToken());


        $asanaProjectRepository = $this->getDoctrine()->getRepository(AsanaProject::class);
        $asanaProject = $asanaProjectRepository->findOneBy([
            'asanaId' => $asanaProjectId,
            'project' => $projectId,
            'user' => $user
        ]);

        if(!$asanaProject){
            $asanaProject = new AsanaProject();
        }

        $projectOnAsana = $asana->getProject($asanaProjectId);
        $asanaProject->setAsanaWorkspaceId($projectOnAsana->workspace->gid);
        $asanaProject->setName($projectOnAsana->name);
        $asanaProject->setAsanaId($asanaProjectId);
        $asanaProject->setIsPinned(true);
        $project = $this->getDoctrine()->getRepository(Project::class)->find($projectId);
        $asanaProject->setProject($project);
        $asanaProject->setUser($user);

        $em = $this->getDoctrine()->getManager();
        $em->persist($asanaProject);
        $em->flush();

        return $this->redirectToRoute('task_index');

    }
}
