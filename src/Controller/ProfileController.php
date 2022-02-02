<?php
// Le dossier virtuel de la class de ce fichier
namespace App\Controller;
// Utilisation de l'entité User pour la liste dans la BDD
use App\Entity\User;
// Utilisation du modèle du formulaire pour la création de User et la lister
use App\Form\User1Type;
// Utilisation de UserRepository pour récupérer la liste et pour afficher les Details
use App\Repository\UserRepository;
// Utilisation de l'EntityManager pour créer, supprimer et modifier un article
use Doctrine\ORM\EntityManagerInterface;
// Utilisation de l'abstract controller pour les méthodes qui sont héritées : par exemple, render, renderForm, createFrom etc.
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Utilisation du Request pour trouver les informations saisies par l'utilisateur
use Symfony\Component\HttpFoundation\Request;
// Utilisation du Response juste pour définir que les fonctions liées aux routes vont retournées une réponse au navigateur 
use Symfony\Component\HttpFoundation\Response;
// Utilisation du Route comme annotation pour rendre accessible une focntion par un client (ex: navigateur)
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController
{

    /**
     * @Route("/", name="profile_show", methods={"GET"})
     */
    public function show(): Response
    {
        // création d'une variable user de l'utilisateur connecté
        $user = $this->getUser();
        // Affiche la vue 'profile/show.html.twig' avec la variable TWIG 'user'
        return $this->render('profile/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/edit", name="profile_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        // création d'une variable user de l'utilisateur connecté
        $user = $this->getUser();
        // Je créer la variable form dans laquelle je créer un formulaire à partir du modèle User1Type
        // Le form devra remplir $user   
        $form = $this->createForm(User1Type::class, $user);
        // Récupération de données saisis par l'utilisateur grace à $request
        $form->handleRequest($request);
        // Si le formulaire à été soumis et si il est valide (correctement remplis)
        if ($form->isSubmitted() && $form->isValid()) {
            // L'entityManager valide les décisions de sauvegardes
            $entityManager->flush();
            // Redirection du navigateur vers la route interne 'profile_show' afin de montrer la modification effectuée
            // le return stop la fonction
            return $this->redirectToRoute('profile_show', [], Response::HTTP_SEE_OTHER);
        }
        // Redirection du navigateur vers 'profile/edit.html.twig' si des données n'ont pas été remplis correctement
        // Le return stop la fonction
        return $this->renderForm('profile/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


}
