<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Payment;
use App\Entity\User;
use App\Form\PaymentType;
use App\Repository\PaymentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/payment")
 */
class PaymentController extends Controller
{
    /**
     * @Route("/", name="payment_index", methods="GET")
     */
    public function index(PaymentRepository $paymentRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $payments = $paymentRepository->getUserPayments($user->getWorkspaces()[0]);
        return $this->render('payment/index.html.twig', ['payments' => $payments]);
    }

    /**
     * @Route("/new", name="payment_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $workspace = $user->getWorkspaces()[0];
        $clients = $this->getDoctrine()->getRepository(Client::class)->findBy(['workspace' => $workspace]);

        $payment = new Payment();
        $form = $this->createForm(PaymentType::class, $payment, ['clients' => $clients]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $payment->setWorkspace($workspace);
            $em->persist($payment);
            $em->flush();

            return $this->redirectToRoute('payment_index');
        }

        return $this->render('payment/new.html.twig', [
            'payment' => $payment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="payment_show", methods="GET")
     */
    public function show(Payment $payment): Response
    {
        return $this->render('payment/show.html.twig', ['payment' => $payment]);
    }

    /**
     * @Route("/{id}/edit", name="payment_edit", methods="GET|POST")
     */
    public function edit(Request $request, Payment $payment): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $workspace = $user->getWorkspaces()[0];
        $clients = $this->getDoctrine()->getRepository(Client::class)->findBy(['workspace' => $workspace]);

        $form = $this->createForm(PaymentType::class, $payment, ['clients' => $clients]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('payment_edit', ['id' => $payment->getId()]);
        }

        return $this->render('payment/edit.html.twig', [
            'payment' => $payment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="payment_delete", methods="DELETE")
     */
    public function delete(Request $request, Payment $payment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$payment->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($payment);
            $em->flush();
        }

        return $this->redirectToRoute('payment_index');
    }
}
