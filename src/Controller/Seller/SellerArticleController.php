<?php
// Le dossier virtuel de la class de ce fichier
namespace App\Controller\Seller;
// Utilisation de l'entité Article pour la liste dans la BDD
use App\Entity\Article;
// Utilisation du modèle du formulaire pour la création d'article et la lister
use App\Form\Seller\Article1Type;
// Utilisation de FileUploader pour ajouter et afficher une image 
use App\Service\FileUploader;
// Utilisation d'ArticleRepository pour récupérer la liste et pour afficher le détail d'un article 
use App\Repository\ArticleRepository;
// Utilisation de l'EntityManager pour créer, supprimer et modifier un article
use Doctrine\ORM\EntityManagerInterface;
// Utilisation du Request pour trouver les informations saisies par l'utilisateur
use Symfony\Component\HttpFoundation\Request;
// Utilisation du Response juste pour définir que les fonctions liées aux routes vont retournées une réponse au navigateur 
use Symfony\Component\HttpFoundation\Response;
// Utilisation du Route comme annotation pour rendre accessible une focntion par un client (ex: navigateur)
use Symfony\Component\Routing\Annotation\Route;
// Utilisation de l'abstract controller pour les méthodes qui sont héritées : par exemple, render, renderForm, createFrom etc.
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/seller/article")
 */
class SellerArticleController extends AbstractController
{
    /**
     * @Route("/", name="seller_article_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        // Affiche la vue 'seller_article/index.html.twig' avec une variable TWIG 'articles'
        // qui pointe vers la liste de tous les articles en base de données
        return $this->render('Seller/seller_article/index.html.twig', [
            'articles' => $articleRepository->findBy([
                'auteur' => $this->getUser()
            ]),
        ]);
    }

    /**
     * @Route("/new", name="seller_article_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        // Je créer une variable article dans laquelle j'appel la fonction constructeur de la classe Article (instanciation)
        $article = new Article();
        // Je créer la variable form dans laquelle je créer un formulaire à partir du modèle d'Article1Type
        // Le form devra remplir $article
        $form = $this->createForm(Article1Type::class, $article);
        // Récupération de données saisis par l'utilisateur grace à $request
        $form->handleRequest($request);
        // Si le formulaire à été soumis et si il est valide (correctement remplis)
        if ($form->isSubmitted() && $form->isValid()) {
            // Création d'une variable $imageFile qui prend en donnée l'image uploadé dans le formulaire
            $imageFile = $form->get('image')->getData();
            // Si il y a une donnée dans l'image
            if ($imageFile) {
                // Alors création d'une variable qui upload l'image
                $imageFileName = $fileUploader->upload($imageFile);
                // Envoi de l'image dans l'objet article dans la BDD
                $article->setImageFilename($imageFileName);
            }
            $article->setAuteur($this->getUser());
            // L'entityManager sauvegarde les données contenu dans $article en BDD
            $entityManager->persist($article);
            // L'entityManager valide les décisions de sauvegardes
            $entityManager->flush();
            // Redirection du navigateur vers la route interne 'seller_article_index'
            // le return stop la fonction
            return $this->redirectToRoute('seller_article_index', [], Response::HTTP_SEE_OTHER);
        }
        // Redirection du navigateur vers 'seller_article/new.html.twig' si des données n'ont pas été remplis correctement
        // Le return stop la fonction
        return $this->renderForm('Seller/seller_article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="seller_article_show", methods={"GET"})
     */
    public function show(Article $article, Request $request): Response
    {
        if($article->getAuteur() == $this->getUser()){
        // Retourne la vue 'seller_article" pour l'article correspondant
            return $this->render('Seller/seller_article/show.html.twig', [
                'article' => $article,
            ]);
        }else{
            return $this->redirectToRoute('seller_article_index');
        }
    }

    /**
     * @Route("/{id}/edit", name="seller_article_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        // Je créer la variable form dans laquelle je créer un formulaire à partir du modèle d'Article1Type
        // Le form devra remplir $article
        $form = $this->createForm(Article1Type::class, $article);
        // Récupération de données saisis par l'utilisateur grace à $request
        $form->handleRequest($request);
        // Si le formulaire à été soumis et si il est valide (correctement remplis)
        if ($form->isSubmitted() && $form->isValid()) {
            // Création d'une variable $imageFile qui prend en donnée l'image uploadé dans le formulaire
            $imageFile = $form->get('image')->getData();
            // Si il y a une donnée dans l'image
            if ($imageFile) {
                // Alors création d'une variable qui upload l'image
                $imageFileName = $fileUploader->upload($imageFile);
                // Envoi de l'image dans l'objet article dans la BDD
                $article->setImageFilename($imageFileName);
            }
            // L'entityManager valide les décisions de sauvegardes
            $entityManager->flush();
            // Redirection du navigateur vers la route interne 'seller_article_index'
            // le return stop la fonction
            return $this->redirectToRoute('seller_article_index', [], Response::HTTP_SEE_OTHER);
        }
        // Redirection du navigateur vers 'seller_article/edit.html.twig' si des données n'ont pas été remplis correctement
        // Le return stop la fonction
        return $this->renderForm('Seller/seller_article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="seller_article_delete", methods={"POST"})
     */
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        // Vérifications de sécurité CSRF pour éviter des usurpations d'identité
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
        // Demande à l'$entityManager de supprimer l' $article 
            $entityManager->remove($article);
            // Validation des changements une fois pour toute
            $entityManager->flush();
        }
        // Redirection du navigateur vers la route interne 'admin_article_index'
        return $this->redirectToRoute('seller_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
