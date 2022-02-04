<?php

namespace App\Controller\User;

use App\Entity\PaymentRequest;
use App\Repository\PaymentRequestRepository;
use App\Service\CartService;
use App\Service\PaymentService;
use DateTime;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{
    /**
     * @Route("/payment", name="payment_index")
     */
    public function index(PaymentService $paymentService): Response
    {
        // 1.Création d'une session chez Stripe
        $sessionId = $paymentService->create();
        // 2.Créer un objet à partir du Payment Request en précisant la date et la session chez stripe
        $paymentRequest = new PaymentRequest();
        $paymentRequest->setCreatedAt(new DateTime());
        $paymentRequest->setStripeSessionId($sessionId);
        // 3. Envoi dans la base de donnée
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($paymentRequest);
        $entityManager->flush();

        return $this->render('User/payment/index.html.twig', [
            'sessionId' => $sessionId
        ]);
    }

    /**
     * @Route("/payment/success/{stripeSessionId}", name="payment_success")
     */
    public function success(string $stripeSessionId, PaymentRequestRepository $paymentRequestRepository, CartService $cartService): Response
    {

        $paymentRequest = $paymentRequestRepository->findOneBy([
            'stripeSessionId' => $stripeSessionId
        ]);
        if(!$paymentRequest)
        {
            return $this->redirectToRoute('cart_index');
        }

        $paymentRequest->setValidated(true);
        $paymentRequest->setPaidAt(new DateTime());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        $cartService->clear();

        return $this->render('User/payment/success.html.twig');
    }

    /**
     * @Route("/payment/failure/{stripeSessionId}", name="payment_failure")
     */
    public function failure(string $stripeSessionId, PaymentRequestRepository $paymentRequestRepository, CartService $cartService): Response
    {

        $paymentRequest = $paymentRequestRepository->findOneBy([
            'stripeSessionId' => $stripeSessionId
        ]);
        if(!$paymentRequest)
        {
            return $this->redirectToRoute('cart_index');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($paymentRequest);
        $entityManager->flush();

        return $this->render('User/payment/failure.html.twig');
    }
}
