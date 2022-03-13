<?php
// Le dossier virtuel de la class de ce fichier
namespace App\Controller\Common;
// Utilisation de l'entité User pour la liste dans la BDD
use App\Entity\User;
use App\Service\CartService;

use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

use Symfony\Component\Mime\Address;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;

use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

use App\Service\EmailVerificationService;

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

class RegistrationController extends AbstractController
{

    private EmailVerificationService $emailVerifier;

    public function __construct(EmailVerificationService $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager, CartService $cartService): Response
    {
        $cart = $cartService->get();
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

            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                // Création du modèle de l'e-mail de confirmation.
                (new TemplatedEmail())
                    ->from(new Address('ton@gmail.com', 'ton site'))
                    ->to($user->getEmail())
                    ->subject('Veuillez confirmer votre adresse e-mail')
                    ->htmlTemplate('emails/confirm.html.twig')
            );
            

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }
        // Retourne la vue 'registration/register.html.twig'
        return $this->render('Common/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'cart' => $cart
        ]);
    }


    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());

        } catch (VerifyEmailExceptionInterface $exception) {

            $this->addFlash('error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash('success', 'Votre adresse e-mail a bien été vérifiée !');

        return $this->redirectToRoute('home');
    }
}
