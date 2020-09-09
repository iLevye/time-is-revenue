<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Receipt;
use App\Entity\User;
use App\Form\ReceiptType;
use App\Repository\ReceiptRepository;
use App\Services\PDF\ReceiptMaker;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/receipt")
 */
class ReceiptController extends Controller
{
    /**
     * @Route("/", name="receipt_index", methods="GET")
     */
    public function index(ReceiptRepository $receiptRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->render('receipt/index.html.twig', ['receipts' => $receiptRepository->listReceipts($user)]);
    }

    /**
     * @Route("/new", name="receipt_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $workspace = $user->getWorkspaces()[0];
        $clients = $this->getDoctrine()->getRepository(Client::class)->findBy(['workspace' => $workspace]);

        $receipt = new Receipt();
        $form = $this->createForm(ReceiptType::class, $receipt, ['clients' => $clients, 'users' => [$user]]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($receipt);
            $em->flush();

            return $this->redirectToRoute('receipt_index');
        }

        return $this->render('receipt/new.html.twig', [
            'receipt' => $receipt,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="receipt_show", methods="GET")
     */
    public function show(Receipt $receipt): Response
    {
        return $this->render('receipt/show.html.twig', ['receipt' => $receipt]);
    }

    /**
     * @Route("/{id}/download", name="download_receipt")
     */
    public function download(ReceiptMaker $receiptMaker, $id){
        $receiptRepository = $this->getDoctrine()->getRepository(Receipt::class);
        //$receiptMaker->make($receiptRepository->find($id));
        return $this->render("receipt/pdf.html.twig", ['receipt' => $receiptRepository->find($id)]);
    }

    /**
     * @Route("/{id}/test-pdf", name="test_pdf")
     */
    public function testpdf(ReceiptMaker $receiptMaker, $id){
        $receiptRepository = $this->getDoctrine()->getRepository(Receipt::class);

        /** @var Receipt $receipt */
        $receipt = $receiptRepository->find($id);
        dump($receiptMaker->make($receipt));
        die;


        return $this->render('receipt/pdf.html.twig', ['receipt' => $receipt]);

    }

    /**
     * @Route("/{id}/edit", name="receipt_edit", methods="GET|POST")
     */
    public function edit(Request $request, Receipt $receipt): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $workspace = $user->getWorkspaces()[0];
        $clients = $this->getDoctrine()->getRepository(Client::class)->findBy(['workspace' => $workspace]);

        $form = $this->createForm(ReceiptType::class, $receipt, ['clients' => $clients, 'users' => [$user]]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('receipt_edit', ['id' => $receipt->getId()]);
        }

        return $this->render('receipt/edit.html.twig', [
            'receipt' => $receipt,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="receipt_delete", methods="DELETE")
     */
    public function delete(Request $request, Receipt $receipt): Response
    {
        if ($this->isCsrfTokenValid('delete'.$receipt->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($receipt);
            $em->flush();
        }

        return $this->redirectToRoute('receipt_index');
    }
}
