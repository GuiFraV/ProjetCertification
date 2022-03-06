<?php 
// Virtual file
namespace App\Service;

// Use SessionInterface
use Symfony\Component\HttpFoundation\Session\SessionInterface;

// Use Article Entity
use App\Entity\Article;

class CartService
{
    private $sessionInterface;

    // Dependence injection of SessionInterface 
    public function __construct(SessionInterface $sessionInterface){

        // SessionInterface in variable $sessionInterface ready to use
        $this->sessionInterface = $sessionInterface;
    }

    public function get() {

        // Return of cart session interface with 
        // elements contain an array
        // total and totalQ contain an integer at 0
        return $this->sessionInterface->get('cart',[
            'elements' => [],
            'total' => 0.0,
            'totalQ' => 0,
        ]);
    }

    public function add(Article $article){
        
        // Put in variable object cart in construction
        $cart = $this->get();

        // Put in variable article ID
        $articleId = $article->getId();

        // Add article ID in cart elements to the article and quantity
        // If article in cart elements isn't empty
        if( !isset($cart['elements'][$articleId])){
            $cart['elements'][$articleId]= [
                'article' => $article,
                'quantity'=> 0,
            ];
        }

        // Put in variable quantity of article id in cart element
        $quantity = $cart['elements'][$articleId]['quantity'];

        // Put in variable stock of article 
        $stock = $cart['elements'][$articleId]["article"]->getStock(); 

        // Recover total cart
        $cart['total'] = $cart['total'] + $article->getPrix();

        // Add total quantity
        $cart['totalQ'] = $cart['totalQ'] + 1;
 
        // Check up : the quantity should not be greater than stock :
        // Otherwise the quantity cannot be higher than available stock
        if($quantity < $stock) {
            $cart['elements'][$articleId]['quantity'] = $cart['elements'][$articleId]['quantity'] + 1;
        }else{
            $cart['elements'][$articleId]['quantity'] = $cart['elements'][$articleId]['quantity'];
        }
        
        // Set a sessioninterface with $cart
        $this->sessionInterface->set('cart' , $cart);
    }

    public function remove(Article $article)
    {
        // Put in variable object cart in construction
        $cart = $this->get();

        // Put in variable article ID
        $articleId = $article->getId();

        // If cart elements contain an article ID return this.
        // Avoid error $cart['elements'][$articleId] is not indexed when this object is empty
        if(!isset($cart['elements'][$articleId])){
            return;
        }

        // Remove in cart['total'] the select price of articleid 
        $cart['total'] = $cart['total'] - $article->getPrix();

        // Remove the one quantity of the select article 
        $cart['elements'][$articleId]['quantity'] = $cart['elements'][$articleId]['quantity'] - 1;

        // Remove one quantity of the cart['totalQ']
        $cart['totalQ'] = $cart['totalQ'] - 1;

        // If quantity of select article in cart['element'] is less than or equal to 0
        // Remove it of cart['element']
        if($cart['elements'][$articleId]['quantity'] <= 0)
        {
            unset($cart['elements'][$articleId]);
        }

        // set a sessioninterface with $cart
        $this->sessionInterface->set('cart', $cart);
    }

    public function removeArticle(Article $article)
    {
        // Put in variable object cart in construction
        $cart = $this->get();

        // Put in variable article ID
        $articleId = $article->getId();

        // Put in variable quantity of article id in cart element
        $quantity = $cart['elements'][$articleId]['quantity'];

        // If cart elements contain an article ID return this.
        // Avoid error $cart['elements'][$articleId] is not indexed when this object is empty
        if(!isset($cart['elements'][$articleId])){
            return;
        }

        // Put in variable the price of the select article
        $articlePrice = $article->getPrix();

        // Put in variable the select price article multiply by quantity
        $totalPrice = $articlePrice * $quantity;

        // Put in variable the cart['total'] less the total price ($articlePrice * quantity)
        $result = $cart['total'] - $totalPrice;

        // Put in cart ['total'] the result 
        $cart['total'] = $result;

        // Put the quantity of the select article in cart['elements'] at 0
        $cart['elements'][$articleId]['quantity'] = 0;

        // Put the $cart['totalQ'] equal to $cart['totalQ'] less quantity
        $cart['totalQ'] = $cart['totalQ'] - $quantity;

        // If quantity of select article in cart['element'] is less than or equal to 0
        // Remove it of cart['element']
        if($cart['elements'][$articleId]['quantity'] <= 0)
        {
            unset($cart['elements'][$articleId]);

        }

        // set a sessioninterface with $cart
        $this->sessionInterface->set('cart', $cart);
    }

    public function clear(){

        // remove the variable cart to the sessionInterface
        $this->sessionInterface->remove('cart');
    }


}