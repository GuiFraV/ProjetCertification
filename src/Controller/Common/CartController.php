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
        $user = $this->getUser();

        $cart = $cartService->get();
        
        // dd($cart["elements"][1]["article"]->getimageFilename());
        return $this->render('Common/cart/index.html.twig', [
            'controller_name' => 'CartController',
            'cart' => $cart,
            'user' => $user
        ]);
       

    }

    public function navbarNotification(CartService $cartService): Response
    {
        $cart = $cartService->get();
        
        // dd($cart["elements"][1]["article"]->getimageFilename());
        return $this->render('nav.html.twig', [
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
     * @Route("/cart/remove/article/{id}", name="cart_remove_article")
     */
    public function removeArticle(CartService $cartService, Article $article): Response
    {
        $cartService->removeArticle($article);
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
