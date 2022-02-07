<?php

namespace App\Controller\User;

use DateTime;
use App\Entity\Commande;
use App\Service\CartService;
use App\Entity\DetailCommande;
use App\Entity\PaymentRequest;
use App\Service\PaymentService;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\FournisseurRepository;
use App\Repository\PaymentRequestRepository;
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
    public function success(string $stripeSessionId, PaymentRequestRepository $paymentRequestRepository, CartService $cartService, ArticleRepository $ar, EntityManagerInterface $em): Response
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
        $cart = $cartService->get();

        foreach ($cart['elements'] as $element) {
            $fournisseurId = $element['article']->getAuteur()->getId();
            if (!isset($panierParFournisseur[$fournisseurId])) {
                $panierParFournisseur[$fournisseurId] = []; // on crée $panierParFournisseur[$fournisseurId]
            }
    
            $panierParFournisseur[$fournisseurId][] = $element; // on ajoute $element à $panierParFournisseur[$fournisseurId]
        }
    
        foreach ($panierParFournisseur as $fournisseurId => $elements) {

            $commande = new Commande();
            $commande->setFournisseur($element['article']->getAuteur()->getId());
            $commande->setReference(strval(rand(0, 9999999)));
            $commande->setCreation(new DateTime());
            $commande->setAcheteur($this->getUser());
    
            $em->persist($commande);
    
            foreach ($elements as $element) {
                $articleId = $element['article']->getId();
                $quantity = $element['quantity'];
    
                $detail = new DetailCommande();
                $detail->setArticle($ar->find($articleId));
                $detail->setQuantity($quantity);
                $detail->setCommande($commande);
    
                $em->persist($detail);
            }
        }

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
