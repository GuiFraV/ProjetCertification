<?php

namespace App\Controller\Common;

use App\Service\CartService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PageInfoPersonnelController extends AbstractController
{
    /**
     * @Route("/page/info/personnel", name="page_info_personnel")
     */
    public function index(CartService $cartService): Response
    {
        $cart = $cartService->get();
        return $this->render('Common/page_info_personnel/index.html.twig', [
            'cart' => $cart
        ]);
    }
}
