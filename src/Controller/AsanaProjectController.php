<?php

namespace App\Controller;

use App\Entity\AsanaProject;
use App\Form\AsanaProjectType;
use App\Repository\AsanaProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/asana-project")
 */
class AsanaProjectController extends Controller
{

    /**
     * @Route("/{id}", name="asana_project_show", methods="GET")
     */
    public function show(AsanaProject $asanaProject): Response
    {
        return $this->render('asana_project/show.html.twig', ['asana_project' => $asanaProject]);
    }

    /**
     * @Route("/{id}", name="asana_project_delete", methods="DELETE")
     */
    public function delete(Request $request, $id): Response
    {
        $asanaProject = $this->getDoctrine()->getRepository(AsanaProject::class)->find($id);
        if ($this->isCsrfTokenValid('delete'.$asanaProject->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($asanaProject);
            $em->flush();
        }

        return $this->redirectToRoute('asana');
    }
}
