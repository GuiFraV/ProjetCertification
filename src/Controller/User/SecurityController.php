<?php
// Le dossier virtuel de la class de ce fichier
namespace App\Controller\User;
// Utilisation de l'abstract controller pour les méthodes qui sont héritées : par exemple, render, renderForm, createFrom etc.
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Utilisation du Response juste pour définir que les fonctions liées aux routes vont retournées une réponse au navigateur 
use Symfony\Component\HttpFoundation\Response;
// Utilisation du Route comme annotation pour rendre accessible une fonction par un client (ex: navigateur)
use Symfony\Component\Routing\Annotation\Route;
// Utilisation de l'AuthenticationUtils qui permet de trouver une correspondance avec le login et la base de donnée
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        // Variable qui permet de sécuriser le login lorsque celui-ci n'a pas de correspondance
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        // Variable qui permet de sécuriser le login lorsque celui-ci trouve une correspondance
        $lastUsername = $authenticationUtils->getLastUsername();
        // Affiche la vue 'security/login.html.twig' avec la variable $lastUsername en cas de connexion et la variable $error en cas d'erreur de login. 
        return $this->render('User/security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        // Permet de se déconnecter 
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
