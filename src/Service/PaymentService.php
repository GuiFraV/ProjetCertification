<?php
// Le dossier virtuel de la class de ce fichier
// src/Service/FileUploader.php
namespace App\Service;

use \Stripe\StripeClient;


class PaymentService
{

    private $cartService;
    private $stripe;

    public function __construct(CartService $cartService){

        $this->cartService = $cartService;
        $this->stripe = new StripeClient('sk_test_51KMuKeKNvU2byMIeX8heKgd4iD4JIBRRYt5C7GqTnrAIbZ6qS5gxzog6GcgWLgIhiWsfDWYNMRWJks6uvO69zUKp00SAp3nQiG');


    }

    public function create(): string
    {
        $cart = $this->cartService->get();
        $articles = [];

        foreach($cart['elements'] as $articleId => $element)
        {
            $articles[] = [
                'amount' => $element['article']->getPrix() * 100,
                'quantity' => $element['quantity'],
                'currency' => 'eur',
                'name' => $element['article']->getId()
            ];
        }

        $protocol = $_SERVER['HTTPS'] ? 'https' : 'http';
        $host = $_SERVER['SERVER_NAME'];
        $successUrl = $protocol . '://' . $host .'/payment/success/{CHECKOUT_SESSION_ID}';
        $failureUrl = $protocol . '://' . $host .'/payment/failure/{CHECKOUT_SESSION_ID}';

        $session = $this->stripe->checkout->sessions->create([
            'success_url' => $successUrl,
            'cancel_url' => $failureUrl,
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'line_items' => $articles
        ]);

        return $session->id;


    }


}

