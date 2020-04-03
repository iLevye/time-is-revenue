<?php

namespace App\Controller;

use App\Entity\InvoiceSettings;
use App\Form\InvoiceSettingsType;
use App\Form\TelegramSettingsType;
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
        $em = $this->getDoctrine()->getManager();

        $invoiceSettings = $this->getDoctrine()->getRepository(InvoiceSettings::class)->findOneBy(['user' => $user]);
        if($invoiceSettings == null){
            $invoiceSettings = new InvoiceSettings();
            $invoiceSettings->setUser($user);
        }

        $form = $this->createForm(InvoiceSettingsType::class, $invoiceSettings);
        $form->handleRequest($request);

        $updateStatus = false;
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($invoiceSettings);
            $em->flush();
            $updateStatus = true;
        }

        $telegramForm = $this->createForm(TelegramSettingsType::class, $user);
        $telegramForm->handleRequest($request);
        if($telegramForm->isSubmitted() && $telegramForm->isValid()){
            $em->persist($user);
            $em->flush();
            $updateStatus = true;
        }

        return $this->render('settings/index.html.twig', [
            'form' => $form->createView(),
            'telegramForm' => $telegramForm->createView(),
            'updateStatus' => $updateStatus
        ]);
    }
}
