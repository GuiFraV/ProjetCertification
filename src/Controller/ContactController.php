<?php
// Le dossier virtuel de la class de ce fichier
namespace App\Controller;
// Utilisation du modèle du formulaire pour la création de Contact et la lister
use App\Form\ContactType;
use App\Service\EmailService;
use App\Service\RememberService;
// Utilisation du Request pour trouver les informations saisies par l'utilisateur
use Symfony\Component\HttpFoundation\Request;
// Utilisation du Response juste pour définir que les fonctions liées aux routes vont retournées une réponse au navigateur 
use Symfony\Component\HttpFoundation\Response;
// Utilisation du Route comme annotation pour rendre accessible une focntion par un client (ex: navigateur)
use Symfony\Component\Routing\Annotation\Route;
// Utilisation de l'abstract controller pour les méthodes qui sont héritées : par exemple, render, renderForm, createFrom etc.
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */
    public function index(Request $request, EmailService $emailService, RememberService $rememberService): Response
    {
        // Je créer la variable form dans laquelle je créer un formulaire à partir du modèle ContactType
        // Le form devra remplir $user
        $form = $this->createForm(ContactType::class, [
            'prenom' => $rememberService->donneMoi('prenomSaisie'),
            'email' => $rememberService->donneMoi('emailSaisie'),
            'objet' => $rememberService->donneMoi('objetSaisie'),
            'texte' => $rememberService->donneMoi('messageSaisie'),

        ]);
        // Récupération de données saisis par l'utilisateur grace à $request
        $form -> handleRequest($request);
        // Si le formulaire à été soumis
        if($form->isSubmitted()){
            // création d'une variable data qui contient les données saisie dans le formulaire
            $data = $form->getData();
            // création d'une variable prenom qui contient la donnée prénom
            $prenom = $data['prenom'];
            // création d'une variable qui contient la donnée email
            $email = $data['email'];
            // création d'une variable qui contient la donnée objet
            $objet = $data['objet'];
            // création d'une variable qui contient la donnée texte
            $message = $data['texte'];

            // Je rentre dans l'autowire Rememberservice
            // J'utilise la méthode seSouvenir et donneMoi
            // Je paramètre mes fonctions (de la manière définit dans le Service) 
            $rememberService->seSouvenir('emailSaisie', $email);
            $rememberService->seSouvenir('prenomSaisie', $prenom);
            $rememberService->seSouvenir('objetSaisie', $objet);
            $rememberService->seSouvenir('messageSaisie', $message);
           
            $destinataire = 'admin@summerfiel.com';

            // 1. le mail de contact du visiteur -> l'administrateur
            $emailService->envoyer(['html5' => $message],'emails/signout.html.twig',$email,$destinataire,$objet);

            // 2. Le mail d'accusé de réception de l'administrateur -> au visiteur
            $emailService->envoyer((['to' => $email ]),'emails/signup.html.twig',$destinataire,$email,"Demande de contact avec succès");

            // Redirection vers 'contact/success.html.twig' avec les variable TWIG prenom et email
            return $this->render('contact/success.html.twig', [
                'prenom' => $prenom,
                'email' => $email,
            ]);

        }else{
            // Sinon retourne la vue 'contact/index.html.twig' et affiche le formualire contenue dans la variable TWIG 'formulaireContact'
            return $this->renderForm('contact/index.html.twig', [
                'formulaireContact' => $form,
            ]);

        }


    }
}
