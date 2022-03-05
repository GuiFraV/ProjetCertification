<?php 
// Le dossier virtuel de la class de ce fichier
// src/Service/FileUploader.php
namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Article;


class CartService
{
    private $sessionInterface;

    // Dependence injection of SessionInterface 
    public function __construct(SessionInterface $sessionInterface){

        $this->sessionInterface = $sessionInterface;

    }

    public function get() {
        // Return of cart session interface with elements and total
        return $this->sessionInterface->get('cart',[
            'elements' => [],
            'total' => 0.0,
        ]);

    }

    
    public function add(Article $article){
       
        $cart = $this->get();
        $articleId = $article->getId();
        

        // Add article ID in cart elements to the article and quantity
        if( !isset($cart['elements'][$articleId])){
            $cart['elements'][$articleId]= [
                'article' => $article,
                'quantity'=> 0,
            ];
        }

        $quantity = $cart['elements'][$articleId]['quantity'];
        $stock = $cart['elements'][$articleId]["article"]->getStock(); 
        
        // Recover total cart
        $cart['total'] = $cart['total'] + $article->getPrix();

        


        // Check up : the quantity should not be greater than stock :
        if($quantity < $stock) {
            $cart['elements'][$articleId]['quantity'] = $cart['elements'][$articleId]['quantity'] + 1;
        }else{
            $e = "erreur";
            dd($e);
        }
        

        $this->sessionInterface->set('cart' , $cart);
    }

    public function remove(Article $article)
    {
        
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

    public function removeArticle(Article $article)
    {
        
        $cart = $this->get();
        $articleId = $article->getId();

        if(!isset($cart['elements'][$articleId])){
            return;
        }

        $test = $article->getPrix();
        $test2 = $cart['elements'][$articleId]['quantity'];
        $test3 = $test * $test2;
        $test4 = $cart['total'] - $test3;

        // dd($test4);

        $cart['total'] = $test4;
        $cart['elements'][$articleId]['quantity'] = 0;


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