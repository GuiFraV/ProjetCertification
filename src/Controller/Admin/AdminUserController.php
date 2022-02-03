<?php
// Le dossier virtuel de la class de ce fichier
namespace App\Controller\Admin;
// Utilisation de l'entité User pour la liste dans la BDD
use App\Entity\User;
// Utilisation du modèle du formulaire pour la création de User et la lister
use App\Form\Admin\UserType;
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
 * @Route("/admin/user")
 */
class AdminUserController extends AbstractController
{
    /**
     * @Route("/", name="admin_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        // Affiche la vue 'admin_user/index.html.twig' avec une variable TWIG 'commandes'
        // qui pointe vers la liste de toutes les commandes en base de données
        return $this->render('Admin/admin_user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin_user_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Je créer une variable user dans laquelle j'appel la fonction constructeur de la classe paiement (instanciation)
        $user = new User();
        // Je créer la variable form dans laquelle je créer un formulaire à partir du modèle de UserType
        // Le form devra remplir $user        
        $form = $this->createForm(UserType::class, $user);
        // Récupération de données saisis par l'utilisateur grace à $request
        $form->handleRequest($request);
        // Si le formulaire à été soumis et s'il est valide (correctement remplis)
        if ($form->isSubmitted() && $form->isValid()) {
            // L'entityManager sauvegarde les données contenu dans $user en BDD
            $entityManager->persist($user);
            // L'entityManager valide les décisions de sauvegardes
            $entityManager->flush();
            // Redirection du navigateur vers la route interne 'admin_user_index'
            // le return stop la fonction
            return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
        }
        // Redirection du navigateur vers 'admin_user/new.html.twig' si des données n'ont pas été remplis correctement
        // Le return stop la fonction
        return $this->renderForm('Admin/admin_user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        // Retourne la vue 'admin_user/show.html.twig" pour l'article correspondant
        return $this->render('Admin/admin_user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Je créer la variable form dans laquelle je créer un formulaire à partir du modèle UserType
        // Le form devra remplir $user
        $form = $this->createForm(UserType::class, $user);
        // Récupération de données saisis par l'utilisateur grace à $request
        $form->handleRequest($request);
        // Si le formulaire à été soumis et si il est valide (correctement remplis)
        if ($form->isSubmitted() && $form->isValid()) {
            // L'entityManager valide les décisions de sauvegardes
            $entityManager->flush();
            // Redirection du navigateur vers la route interne 'admin_user_index'
            // le return stop la fonction
            return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
        }
        // Redirection du navigateur vers 'admin_user/edit.html.twig' si des données n'ont pas été remplis correctement
        // Le return stop la fonction
        return $this->renderForm('Admin/admin_user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        // Vérifications de sécurité CSRF pour éviter des usurpations d'identité
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            // Demande à l'$entityManager de supprimer le $detailCommande 
            $entityManager->remove($user);
            // Validation des changements une fois pour toute
            $entityManager->flush();
        }
        // Redirection du navigateur vers la route interne 'admin_user_index'
        return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
