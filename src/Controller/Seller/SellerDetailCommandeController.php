<?php
// Le dossier virtuel de la class de ce fichier
namespace App\Controller\Seller;
// Utilisation de l'entité DetailCommande pour la liste dans la BDD
use App\Entity\DetailCommande;
// Utilisation du modèle du formulaire pour la création de Detail1Commande et la lister
use App\Form\Seller\DetailCommande1Type;
// Utilisation de DetailCommandeRepository pour récupérer la liste et pour afficher les Details
use App\Repository\DetailCommandeRepository;
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
 * @Route("/seller/detail/commande")
 */
class SellerDetailCommandeController extends AbstractController
{
    /**
     * @Route("/", name="seller_detail_commande_index", methods={"GET"})
     */
    public function index(DetailCommandeRepository $detailCommandeRepository): Response
    {
        // Affiche la vue 'seller_detail_commande/index.html.twig' avec une variable TWIG 'detail_commandes'
        // qui pointe vers la liste de toutes les detailscommandes en base de données
        return $this->render('seller_detail_commande/index.html.twig', [
            'detail_commandes' => $detailCommandeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="seller_detail_commande_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Je créer une variable detailcommande dans laquelle j'appel la fonction constructeur de la classe DetailCommande (instanciation)
        $detailCommande = new DetailCommande();
        // Je créer la variable form dans laquelle je créer un formulaire à partir du modèle de Commande1Type
        // Le form devra remplir $DetailCommande        
        $form = $this->createForm(DetailCommande1Type::class, $detailCommande);
        // Récupération de données saisis par l'utilisateur grace à $request
        $form->handleRequest($request);
        // Si le formulaire à été soumis et si il est valide (correctement remplis)
        if ($form->isSubmitted() && $form->isValid()) {
            // L'entityManager sauvegarde les données contenu dans $detailCommande en BDD
            $entityManager->persist($detailCommande);
            // L'entityManager valide les décisions de sauvegardes
            $entityManager->flush();
            // Redirection du navigateur vers la route interne 'seller_detail_commande_index'
            // le return stop la fonction
            return $this->redirectToRoute('seller_detail_commande_index', [], Response::HTTP_SEE_OTHER);
        }
        // Redirection du navigateur vers 'seller_detail_commande/new.html.twig' si des données n'ont pas été remplis correctement
        // Le return stop la fonction
        return $this->renderForm('seller_detail_commande/new.html.twig', [
            'detail_commande' => $detailCommande,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="seller_detail_commande_show", methods={"GET"})
     */
    public function show(DetailCommande $detailCommande): Response
    {
        // Retourne la vue 'seller_detail_commande/show.html.twig" pour le detailCommande correspondant
        return $this->render('seller_detail_commande/show.html.twig', [
            'detail_commande' => $detailCommande,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="seller_detail_commande_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, DetailCommande $detailCommande, EntityManagerInterface $entityManager): Response
    {
        // Je créer la variable form dans laquelle je créer un formulaire à partir du modèle DetailCommande1Type
        // Le form devra remplir $detailCommande
        $form = $this->createForm(DetailCommande1Type::class, $detailCommande);
        // Récupération de données saisis par l'utilisateur grace à $request
        $form->handleRequest($request);
        // Si le formulaire à été soumis et si il est valide (correctement remplis)
        if ($form->isSubmitted() && $form->isValid()) {
            // L'entityManager valide les décisions de sauvegardes
            $entityManager->flush();
            // Redirection du navigateur vers la route interne 'seller_detail_commande_index'
            // le return stop la fonction
            return $this->redirectToRoute('seller_detail_commande_index', [], Response::HTTP_SEE_OTHER);
        }
        // Redirection du navigateur vers 'seller_detail_commande/edit.html.twig' si des données n'ont pas été remplis correctement
        // Le return stop la fonction
        return $this->renderForm('seller_detail_commande/edit.html.twig', [
            'detail_commande' => $detailCommande,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="seller_detail_commande_delete", methods={"POST"})
     */
    public function delete(Request $request, DetailCommande $detailCommande, EntityManagerInterface $entityManager): Response
    {
        // Vérifications de sécurité CSRF pour éviter des usurpations d'identité
        if ($this->isCsrfTokenValid('delete'.$detailCommande->getId(), $request->request->get('_token'))) {
            // Demande à l'$entityManager de supprimer le $detailCommande 
            $entityManager->remove($detailCommande);
            // Validation des changements une fois pour toute
            $entityManager->flush();
        }
        // Redirection du navigateur vers la route interne 'seller_detail_commande_index'
        return $this->redirectToRoute('seller_detail_commande_index', [], Response::HTTP_SEE_OTHER);
    }
}
