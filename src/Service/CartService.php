<?php 
// Le dossier virtuel de la class de ce fichier
// src/Service/FileUploader.php
namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Article;


class CartService
{
    private $sessionInterface;

    // Injection de dépendance pour faire fonctionner le panier 
    public function __construct(SessionInterface $sessionInterface){

        $this->sessionInterface = $sessionInterface;


    }

    public function get() {
        // recherche dans la session interface le panier 'cart'
        return $this->sessionInterface->get('cart',[
            'elements' => [],
            'total' => 0.0
        ]);
    }

    public function add(Article $article){
        // récupération de la fonction get
        $cart = $this->get();
        $articleId = $article->getId();
        if( !isset($cart['elements'][$articleId])){
            $cart['elements'][$articleId]= [
                'article' => $article,
                'quantity'=> 0
            ];
        }
        // Je récupère le total du panier 
        $cart['total'] = $cart['total'] + $article->getPrix();
        $cart['elements'][$articleId]['quantity'] = $cart['elements'][$articleId]['quantity'] + 1;

        $this->sessionInterface->set('cart', $cart);
    }

    public function remove(Article $article)
    {
        // récupère un panier s'il existe
        $cart = $this->get();
        $articleId = $article->getId();

        if(!isset($cart['elements'][$articleId])){
            return;
        }

        $cart['total'] = $cart['total'] - $article->getPrix();
        $cart['elements'][$articleId]['quantity'] = $cart['elements'][$articleId]['quantity'] - 1;

        if($cart['elements'][$articleId]['quantity'] <= 0)
        {
            unset($cart['elements'][$articleId]);
        }

        $this->sessionInterface->set('cart', $cart);
    }


    public function clear(){
        $this->sessionInterface->remove('cart');
    }


}