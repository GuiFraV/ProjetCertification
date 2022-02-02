<?php
// Le dossier virtuel de la class de ce fichier
namespace App\Controller\Admin;
// Utilisation de l'entité Article pour la liste dans la BDD
use App\Entity\Article;
// Utilisation du modèle du formulaire pour la création d'article et la lister
use App\Form\ArticleType;
// Utilisation d'ArticleRepository pour récupérer la liste et pour afficher le détail d'un article 
use App\Repository\ArticleRepository;
// Utilisation de FileUploader pour ajouter et afficher une image 
use App\Service\FileUploader;
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
 * @Route("/admin/article")
 */
class AdminArticleController extends AbstractController
{
    /**
     * @Route("/", name="admin_article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        // Affiche la vue 'admin_article/index.html.twig' avec une variable TWIG 'articles'
        // qui pointe vers la liste de tous les articles en base de données
        return $this->render('admin_article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="admin_article_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        // Je créer une variable article dans laquelle j'appel la fonction constructeur de la classe Article (instanciation)
        $article = new Article();
        // Je créer la variable form dans laquelle je créer un formulaire à partir du modèle d'ArticleType
        // Le form devra remplir $article
        $form = $this->createForm(ArticleType::class, $article);
        // Récupération de données saisis par l'utilisateur grace à $request
        $form->handleRequest($request);
        // Si le formulaire à été soumis et si il est valide (correctement remplis)
        if ($form->isSubmitted() && $form->isValid()) {
            // Création d'une variable $imageFile qui prend en donnée l'image uploadé dans le formulaire
            $imageFile = $form->get('image')->getData();
            // Si il y a une donnée dans l'image
            if ($imageFile) {
                // Récupération du chemin du fichier uploadé déposé sur le serveur via FileUploader
                $imageFileName = $fileUploader->upload($imageFile);
                // On applique le chemin du fichier uploadé dans le champs ImageFilename
                $article->setImageFilename($imageFileName);
            }
            // L'entityManager sauvegarde les données contenu dans $article en BDD
            $entityManager->persist($article);
            // L'entityManager valide les décisions de sauvegardes
            $entityManager->flush();
            // Redirection du navigateur vers la route interne 'admin_article_index'
            // le return stop la fonction
            return $this->redirectToRoute('admin_article_index', [], Response::HTTP_SEE_OTHER);
        }
        // Redirection du navigateur vers 'admin_article/new.html.twig' si des données n'ont pas été remplis correctement
        // Le return stop la fonction
        return $this->renderForm('admin_article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_article_show", methods={"GET"})
     */
    public function show(Article $article): Response
    {
        // Retourne la vue 'admin_article" pour l'article correspondant
        return $this->render('admin_article/show.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_article_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        // Je créer la variable form dans laquelle je créer un formulaire à partir du modèle d'ArticleType
        // Le form devra remplir $article
        $form = $this->createForm(ArticleType::class, $article);
        // Récupération de données saisis par l'utilisateur grace à $request
        $form->handleRequest($request);
        // Si le formulaire à été soumis et si il est valide (correctement remplis)
        if ($form->isSubmitted() && $form->isValid()) {
            // Création d'une variable $imageFile qui prend en donnée l'image uploadé dans le formulaire
              $imageFile = $form->get('image')->getData();
            // Si il y a une donnée dans l'image
            if ($imageFile) {
                // On applique le chemin du fichier uploadé dans le champs ImageFilename
                $imageFileName = $fileUploader->upload($imageFile);
                // Envoi de l'image dans l'objet article dans la BDD
                $article->setImageFilename($imageFileName);
            }
            // L'entityManager valide les décisions de sauvegardes
            $entityManager->flush();
            // Redirection du navigateur vers la route interne 'admin_article_index'
            // le return stop la fonction
            return $this->redirectToRoute('admin_article_index', [], Response::HTTP_SEE_OTHER);
        }
        // Redirection du navigateur vers 'admin_article/edit.html.twig' si des données n'ont pas été remplis correctement
        // Le return stop la fonction
        return $this->renderForm('admin_article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="admin_article_delete", methods={"POST"})
     */
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        // Vérifications de sécurité CSRF pour éviter des usurpations d'identité
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
        // Demande à l'$entityManager de supprimer l' $article 
            $entityManager->remove($article);
            // Validation des changements une fois pour toute
            $entityManager->flush();
        }
        // Redirection du navigateur vers la route interne 'admin_article_index'
        return $this->redirectToRoute('admin_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
