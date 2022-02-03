<?php
// Le dossier virtuel de la class de ce fichier
namespace App\Controller\Common;
// Utilisation de l'entité User pour la liste dans la BDD
use App\Entity\User;
// Utilisation du modèle du formulaire pour la création de User et la lister
use App\Form\Common\RegistrationFormType;
use App\Security\UserAuthenticator;
// Utilisation de l'EntityManager pour créer, supprimer et modifier un article
use Doctrine\ORM\EntityManagerInterface;
// Utilisation de l'abstract controller pour les méthodes qui sont héritées : par exemple, render, renderForm, createFrom etc.
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Utilisation du Request pour trouver les informations saisies par l'utilisateur
use Symfony\Component\HttpFoundation\Request;
// Utilisation du Response juste pour définir que les fonctions liées aux routes vont retournées une réponse au navigateur 
use Symfony\Component\HttpFoundation\Response;
// Utilisation du UserPasswordHasherInterface afin de hasher le mot de passe en BDD
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
// Utilisation du Route comme annotation pour rendre accessible une focntion par un client (ex: navigateur)
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        // Je créer une variable user dans laquelle j'appel la fonction constructeur de la classe paiement (instanciation)
        $user = new User();
        // Je créer la variable form dans laquelle je créer un formulaire à partir du modèle de RegistrationType
        // Le form devra remplir $user        
        $form = $this->createForm(RegistrationFormType::class, $user);
        // Récupération de données saisis par l'utilisateur grace à $request
        $form->handleRequest($request);
        // Si le formulaire à été soumis et s'il est valide (correctement remplis)
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            // Création d'une variable prenant en donnée le role choisi lors de l'inscription
            $role = $form->get('role')->getData();
            // Condition si le role est strictement égale à "ROLE_SELLER"
            if ($role == 'ROLE_SELLER'){
                // Alors tu lui donne comme rôle "ROLE_SELLER"
                $user->setRoles(array ("ROLE_SELLER"));

            } 


            // L'entityManager sauvegarde les données contenu dans $user en BDD
            $entityManager->persist($user);
            // L'entityManager valide les décisions de sauvegardes
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }
        // Retourne la vue 'registration/register.html.twig'
        return $this->render('Common/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
