<?php

namespace App\Controller\Admin;

use App\Entity\PaymentRequest;
use App\Form\Admin\PaymentRequestType;
use App\Repository\PaymentRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/payment/request")
 */
class AdminPaymentRequestController extends AbstractController
{
    /**
     * @Route("/", name="admin_payment_request_index", methods={"GET"})
     */
    public function index(PaymentRequestRepository $paymentRequestRepository): Response
    {
        return $this->render('Admin/admin_payment_request/index.html.twig', [
            'payment_requests' => $paymentRequestRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin_payment_request_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $paymentRequest = new PaymentRequest();
        $form = $this->createForm(PaymentRequestType::class, $paymentRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($paymentRequest);
            $entityManager->flush();

            return $this->redirectToRoute('admin_payment_request_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Admin/admin_payment_request/new.html.twig', [
            'payment_request' => $paymentRequest,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_payment_request_show", methods={"GET"})
     */
    public function show(PaymentRequest $paymentRequest): Response
    {
        return $this->render('Admin/admin_payment_request/show.html.twig', [
            'payment_request' => $paymentRequest,
        ]);
    }
    /**
     * @Route("/{id}", name="admin_payment_request_delete", methods={"POST"})
     */
    public function delete(Request $request, PaymentRequest $paymentRequest, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$paymentRequest->getId(), $request->request->get('_token'))) {
            $entityManager->remove($paymentRequest);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_payment_request_index', [], Response::HTTP_SEE_OTHER);
    }
}
