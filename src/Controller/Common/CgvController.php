<?php

namespace App\Controller\Common;

use App\Service\CartService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CgvController extends AbstractController
{
    
    /**
     * @Route("/cgv", name="cgv")
     */
    public function index(CartService $cartService): Response
    {
        $cart = $cartService->get();
        return $this->render('Common/cgv/index.html.twig', [
            'cart' => $cart
        ]);
    }
}
