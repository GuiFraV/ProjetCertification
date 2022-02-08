<?php

namespace App\Controller\Common;

use App\Entity\Article;
use App\Service\CartService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart_index")
     */
    public function index(CartService $cartService): Response
    {
        $cart = $cartService->get();
        return $this->render('Common/cart/index.html.twig', [
            'controller_name' => 'CartController',
            'cart' => $cart
        ]);

    }

    /**
     * @Route("/cart/add/{id}", name="cart_add")
     */
    public function add(CartService $cartService, Article $article): Response
    {
        $cartService->add($article);
        // dd($article);
        return $this->redirectToRoute('cart_index');
        
    }

    /**
     * @Route("/cart/remove/{id}", name="cart_remove")
     */
    public function remove(CartService $cartService, Article $article): Response
    {
        $cartService->remove($article);
        return $this->redirectToRoute('cart_index');
    }

    
    /**
     * @Route("/cart/clear/{id}", name="cart_clear")
     */
    public function clear(CartService $cartService): Response
    {
        $cartService->clear();
        dd($cartService);
        return $this->redirectToRoute('cart_index');
        
    }

    /**
     * @Route("/cart/clearall/", name="cart_clearall")
     */
    public function clearAll(CartService $cartService): Response
    {
        $cartService->clear();
        return $this->redirectToRoute('cart_index');

    }

}
