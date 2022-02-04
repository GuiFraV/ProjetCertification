<?php

namespace App\Controller\Seller;

use App\Entity\PaymentRequest;
use App\Form\PaymentRequest1Type;
use App\Repository\PaymentRequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/seller/payment/request")
 */
class SellerPaymentRequestController extends AbstractController
{
    /**
     * @Route("/", name="seller_payment_request_index", methods={"GET"})
     */
    public function index(PaymentRequestRepository $paymentRequestRepository): Response
    {
        return $this->render('Seller/seller_payment_request/index.html.twig', [
            'payment_requests' => $paymentRequestRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="seller_payment_request_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $paymentRequest = new PaymentRequest();
        $form = $this->createForm(PaymentRequest1Type::class, $paymentRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($paymentRequest);
            $entityManager->flush();

            return $this->redirectToRoute('seller_payment_request_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('Seller/seller_payment_request/new.html.twig', [
            'payment_request' => $paymentRequest,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="seller_payment_request_show", methods={"GET"})
     */
    public function show(PaymentRequest $paymentRequest): Response
    {
        return $this->render('Seller/seller_payment_request/show.html.twig', [
            'payment_request' => $paymentRequest,
        ]);
    }


}
