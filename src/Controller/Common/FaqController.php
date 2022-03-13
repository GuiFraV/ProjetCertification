<?php

namespace App\Controller\Common;

use App\Service\CartService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FaqController extends AbstractController
{
    /**
     * @Route("/faq", name="faq")
     */
    public function index(CartService $cartService): Response
    {
        $cart = $cartService->get();
        return $this->render('Common/faq/index.html.twig', [
            'cart' => $cart
        ]);
    }
}
