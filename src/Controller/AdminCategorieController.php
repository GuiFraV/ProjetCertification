<?php
// Le dossier virtuel de la class de ce fichier
namespace App\Controller;
// Utilisation de l'entité Categorie pour la liste dans la BDD
use App\Entity\Categorie;
// Utilisation du modèle du formulaire pour la création de categorie et la lister
use App\Form\CategorieType;
// Utilisation de CategorieRepository pour récupérer la liste et pour afficher les categories
use App\Repository\CategorieRepository;
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
 * @Route("/admin/categorie")
 */
class AdminCategorieController extends AbstractController
{
    /**
     * @Route("/", name="admin_categorie_index", methods={"GET"})
     */
    public function index(CategorieRepository $categorieRepository): Response
    {
        // Affiche la vue 'admin_categorie/index.html.twig' avec une variable TWIG 'categories'
        // qui pointe vers la liste de toutes les categories en base de données
        return $this->render('admin_categorie/index.html.twig', [
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin_categorie_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Je créer une variable categorie dans laquelle j'appel la fonction constructeur de la classe Categorie (instanciation)
        $categorie = new Categorie();
        // Je créer la variable form dans laquelle je créer un formulaire à partir du modèle d'CategorieType
        // Le form devra remplir $categorie
        $form = $this->createForm(CategorieType::class, $categorie);
        // Récupération de données saisis par l'utilisateur grace à $request
        $form->handleRequest($request);
        // Si le formulaire à été soumis et si il est valide (correctement remplis)
        if ($form->isSubmitted() && $form->isValid()) {
            // L'entityManager sauvegarde les données contenu dans $article en BDD
            $entityManager->persist($categorie);
            // L'entityManager valide les décisions de sauvegardes
            $entityManager->flush();
            // Redirection du navigateur vers la route interne 'admin_categorie_index'
            // le return stop la fonction
            return $this->redirectToRoute('admin_categorie_index', [], Response::HTTP_SEE_OTHER);
        }
        // Redirection du navigateur vers 'admin_categorie/new.html.twig' si des données n'ont pas été remplis correctement
        // Le return stop la fonction
        return $this->renderForm('admin_categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_categorie_show", methods={"GET"})
     */
    public function show(Categorie $categorie): Response
    {
        // Retourne la vue 'admin_article" pour l'article correspondant
        return $this->render('admin_categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_categorie_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        // Je créer la variable form dans laquelle je créer un formulaire à partir du modèle d'CategorieType
        // Le form devra remplir $categorie
        $form = $this->createForm(CategorieType::class, $categorie);
        // Récupération de données saisis par l'utilisateur grace à $request
        $form->handleRequest($request);
        // Si le formulaire à été soumis et si il est valide (correctement remplis)
        if ($form->isSubmitted() && $form->isValid()) {
            // L'entityManager valide les décisions de sauvegardes
            $entityManager->flush();
            // Redirection du navigateur vers la route interne 'admin_categorie_index'
            // le return stop la fonction
            return $this->redirectToRoute('admin_categorie_index', [], Response::HTTP_SEE_OTHER);
        }
        // Redirection du navigateur vers 'admin_categorie/edit.html.twig' si des données n'ont pas été remplis correctement
        // Le return stop la fonction
        return $this->renderForm('admin_categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_categorie_delete", methods={"POST"})
     */
    public function delete(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        // Vérifications de sécurité CSRF pour éviter des usurpations d'identité
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
        // Demande à l'$entityManager de supprimer l' $article 
            $entityManager->remove($categorie);
            // Validation des changements une fois pour toute
            $entityManager->flush();
        }
        // Redirection du navigateur vers la route interne 'admin_categorie_index'
        return $this->redirectToRoute('admin_categorie_index', [], Response::HTTP_SEE_OTHER);
    }
}
