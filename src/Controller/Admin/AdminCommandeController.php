<?php
// Le dossier virtuel de la class de ce fichier
namespace App\Controller\Admin;
// Utilisation de l'entité Commande pour la liste dans la BDD
use App\Entity\Commande;
// Utilisation du modèle du formulaire pour la création de commande et la lister
use App\Form\Admin\CommandeType;
// Utilisation de CategorieRepository pour récupérer la liste et pour afficher les categories
use App\Repository\CommandeRepository;
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
 * @Route("/admin/commande")
 */
class AdminCommandeController extends AbstractController
{
    /**
     * @Route("/", name="admin_commande_index", methods={"GET"})
     */
    public function index(CommandeRepository $commandeRepository): Response
    {
        // Affiche la vue 'admin_commande/index.html.twig' avec une variable TWIG 'commandes'
        // qui pointe vers la liste de toutes les commandes en base de données
        return $this->render('admin_commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin_commande_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Je créer une variable commande dans laquelle j'appel la fonction constructeur de la classe Commande (instanciation)
        $commande = new Commande();
        // Je créer la variable form dans laquelle je créer un formulaire à partir du modèle CommandeType
        // Le form devra remplir $commande        
        $form = $this->createForm(CommandeType::class, $commande);
        // Récupération de données saisis par l'utilisateur grace à $request
        $form->handleRequest($request);
        // Si le formulaire à été soumis et si il est valide (correctement remplis)
        if ($form->isSubmitted() && $form->isValid()) {
            // L'entityManager sauvegarde les données contenu dans $article en BDD
            $entityManager->persist($commande);
            // L'entityManager valide les décisions de sauvegardes
            $entityManager->flush();
            // Redirection du navigateur vers la route interne 'admin_commande_index'
            // le return stop la fonction
            return $this->redirectToRoute('admin_commande_index', [], Response::HTTP_SEE_OTHER);
        }
        // Redirection du navigateur vers 'admin_commande/new.html.twig' si des données n'ont pas été remplis correctement
        // Le return stop la fonction
        return $this->renderForm('admin_commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_commande_show", methods={"GET"})
     */
    public function show(Commande $commande): Response
    {
        // Retourne la vue 'admin_commande/show.html.twig" pour la commande correspondant
        return $this->render('admin_commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_commande_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        // Je créer la variable form dans laquelle je créer un formulaire à partir du modèle d'CommandeType
        // Le form devra remplir $commande
        $form = $this->createForm(CommandeType::class, $commande);
        // Récupération de données saisis par l'utilisateur grace à $request
        $form->handleRequest($request);
        // Si le formulaire à été soumis et si il est valide (correctement remplis)
        if ($form->isSubmitted() && $form->isValid()) {
            // L'entityManager valide les décisions de sauvegardes
            $entityManager->flush();
            // Redirection du navigateur vers la route interne 'admin_commande_index'
            // le return stop la fonction
            return $this->redirectToRoute('admin_commande_index', [], Response::HTTP_SEE_OTHER);
        }
        // Redirection du navigateur vers 'admin_commande/edit.html.twig' si des données n'ont pas été remplis correctement
        // Le return stop la fonction
        return $this->renderForm('admin_commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_commande_delete", methods={"POST"})
     */
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        // Vérifications de sécurité CSRF pour éviter des usurpations d'identité
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            // Demande à l'$entityManager de supprimer la $commande 
            $entityManager->remove($commande);
            // Validation des changements une fois pour toute
            $entityManager->flush();
        }
        // Redirection du navigateur vers la route interne 'admin_commande_index'
        return $this->redirectToRoute('admin_commande_index', [], Response::HTTP_SEE_OTHER);
    }
}
