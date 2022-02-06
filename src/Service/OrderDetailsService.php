<?php

use App\Entity\Commande;
use App\Service\CartService;
use App\Entity\DetailCommande;
use Doctrine\ORM\EntityManager;
use App\Repository\ArticleRepository;
use App\Repository\FournisseurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// autowire : FournisseurRepository $fr, ArticleRepository $ar, EntityManager $em

class OrderDetails extends AbstractController
{

    private $panierParFournisseur = [];


    public function addOrder(CartService $cart,FournisseurRepository $fr, ArticleRepository $ar, EntityManager $em){

        foreach ($cart['elements'] as $element) {
            $fournisseurId = $element['article']->getAuteur()->getId();
    
            if (!isset($panierParFournisseur[$fournisseurId])) {
                $panierParFournisseur[$fournisseurId] = []; // on crée $panierParFournisseur[$fournisseurId]
            }
    
            $panierParFournisseur[$fournisseurId][] = $element; // on ajoute $element à $panierParFournisseur[$fournisseurId]
        }
    
        foreach ($panierParFournisseur as $fournisseurId => $elements) {
            $commande = new Commande();
            $commande->setFournisseur($fr->getId($fournisseurId));
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
    
        $em->flush();

    }


}