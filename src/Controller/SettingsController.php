<?php

namespace App\Controller;

use App\Entity\InvoiceSettings;
use App\Form\InvoiceSettingsType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SettingsController extends AbstractController
{
    /**
     * @Route("/settings", name="settings")
     */
    public function index(Request $request)
    {
        $user = $this->getUser();
        $invoiceSettings = $this->getDoctrine()->getRepository(InvoiceSettings::class)->findOneBy(['user' => $user]);
        if($invoiceSettings == null){
            $invoiceSettings = new InvoiceSettings();
            $invoiceSettings->setUser($user);
        }

        $form = $this->createForm(InvoiceSettingsType::class, $invoiceSettings);
        $form->handleRequest($request);

        $updateStatus = false;
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($invoiceSettings);
            $em->flush();

            $updateStatus = true;
        }


        return $this->render('settings/index.html.twig', [
            'form' => $form->createView(),
            'updateStatus' => $updateStatus
        ]);
    }
}
