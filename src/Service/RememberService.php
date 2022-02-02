<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class RememberService {
    // Création d'une propriété $coeur en privée
    private $coeur;
    // Création d'un constructeur avec une injection de dépendance de SessionInterface
    // L'idée : on injecte une dépendance pour pouvoir l'utiliser dans l'objet courant 
    public function __construct(SessionInterface $sessionInterface) 
    {
        // Assemblage de la SessionInterface dans l'objet courant 
        // Afin de manipuler le $session sans passer par des superglobales
        $this->coeur = $sessionInterface;
    }
    // Création de la méthode en utilisant l'objet courant coeur 
    // Qui contient la fonction set()
    // Permet de se souvenir des paramètre $nom et $valeur (string)
    public function seSouvenir(string $nom, string $valeur):void {

        $this->coeur->set($nom, $valeur);
    }

    // Pas de paramètres
    // Méthode qui permet d'effacer (par exemple un input)
    public function toutOublier():void {
        // Avec l'objet courant -> utilisation de la fonction clear() 
        $this->coeur->clear();
    }

    public function donneMoi(string $nom): ?string {
        return $this->coeur->get($nom);
    }


}