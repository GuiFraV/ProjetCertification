<?php
// Le dossier virtuel de la class de ce fichier
namespace App\Controller\Admin;
// Utilisation de l'entité Paiement pour la liste dans la BDD
use App\Entity\Paiement;
// Utilisation du modèle du formulaire pour la création de Paiement et la lister
use App\Form\PaiementType;
// Utilisation de PaiementRepository pour récupérer la liste et pour afficher les Details
use App\Repository\PaiementRepository;
// Utilisation de l'EntityManager pour créer, supprimer et modifier un article
use Doctrine\ORM\EntityManagerInterface;
// Utilisation de l'abstract controller pour les méthodes qui sont héritées : par exemple, render, renderForm, createFrom etc.
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Utilisation du Request pour trouver les informations saisies par l'utilisateur
use Symfony\Component\HttpFoundation\Request;
// Utilisation du Response juste pour définir que les fonctions liées aux routes vont retournées une réponse au navigateur 
use Symfony\Component\HttpFoundation\Response;
// Utilisation du Route comme annotation pour rendre accessible une fonction par un client (ex: navigateur)
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/paiement")
 */
class AdminPaiementController extends AbstractController
{
    /**
     * @Route("/", name="admin_paiement_index", methods={"GET"})
     */
    public function index(PaiementRepository $paiementRepository): Response
    {
        // Affiche la vue 'admin_paiement/index.html.twig' avec une variable TWIG 'commandes'
        // qui pointe vers la liste de toutes les commandes en base de données
        return $this->render('admin_paiement/index.html.twig', [
            'paiements' => $paiementRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin_paiement_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Je créer une variable paiement dans laquelle j'appel la fonction constructeur de la classe paiement (instanciation)
        $paiement = new Paiement();
        // Je créer la variable form dans laquelle je créer un formulaire à partir du modèle de PaiementType
        // Le form devra remplir $paiement        
        $form = $this->createForm(PaiementType::class, $paiement);
        // Récupération de données saisis par l'utilisateur grace à $request
        $form->handleRequest($request);
        // Si le formulaire à été soumis et s'il est valide (correctement remplis)
        if ($form->isSubmitted() && $form->isValid()) {
            // L'entityManager sauvegarde les données contenu dans $paiement en BDD
            $entityManager->persist($paiement);
            // L'entityManager valide les décisions de sauvegardes
            $entityManager->flush();
            // Redirection du navigateur vers la route interne 'admin_paiement_index'
            // le return stop la fonction
            return $this->redirectToRoute('admin_paiement_index', [], Response::HTTP_SEE_OTHER);
        }
        // Redirection du navigateur vers 'admin_paiement/new.html.twig' si des données n'ont pas été remplis correctement
        // Le return stop la fonction
        return $this->renderForm('admin_paiement/new.html.twig', [
            'paiement' => $paiement,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_paiement_show", methods={"GET"})
     */
    public function show(Paiement $paiement): Response
    {
        // Retourne la vue 'admin_paiement/show.html.twig" pour l'article correspondant
        return $this->render('admin_paiement/show.html.twig', [
            'paiement' => $paiement,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_paiement_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Paiement $paiement, EntityManagerInterface $entityManager): Response
    {
        // Je créer la variable form dans laquelle je créer un formulaire à partir du modèle PaiementType
        // Le form devra remplir $paiement
        $form = $this->createForm(PaiementType::class, $paiement);
        // Récupération de données saisis par l'utilisateur grace à $request
        $form->handleRequest($request);
        // Si le formulaire à été soumis et si il est valide (correctement remplis)
        if ($form->isSubmitted() && $form->isValid()) {
            // L'entityManager valide les décisions de sauvegardes
            $entityManager->flush();
            // Redirection du navigateur vers la route interne 'admin_paiement_index'
            // le return stop la fonction
            return $this->redirectToRoute('admin_paiement_index', [], Response::HTTP_SEE_OTHER);
        }
        // Redirection du navigateur vers 'admin_paiement/edit.html.twig' si des données n'ont pas été remplis correctement
        // Le return stop la fonction
        return $this->renderForm('admin_paiement/edit.html.twig', [
            'paiement' => $paiement,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_paiement_delete", methods={"POST"})
     */
    public function delete(Request $request, Paiement $paiement, EntityManagerInterface $entityManager): Response
    {
        // Vérifications de sécurité CSRF pour éviter des usurpations d'identité
        if ($this->isCsrfTokenValid('delete'.$paiement->getId(), $request->request->get('_token'))) {
            // Demande à l'$entityManager de supprimer le $detailCommande 
            $entityManager->remove($paiement);
            // Validation des changements une fois pour toute
            $entityManager->flush();
        }
        // Redirection du navigateur vers la route interne 'admin_paiement_index'
        return $this->redirectToRoute('admin_paiement_index', [], Response::HTTP_SEE_OTHER);
    }
}
