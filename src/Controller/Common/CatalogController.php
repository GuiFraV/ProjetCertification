<?php
// Le dossier virtuel de la class de ce fichier
namespace App\Controller\Common;
// Utilisation de l'entité User pour la liste dans la BDD
use App\Entity\Article;
// Utilisation du modèle du formulaire pour la création d'Article et la lister
use App\Form\Article2Type;
// Utilisation d' ArticleRepository pour récupérer la liste et pour afficher les Details
use App\Repository\ArticleRepository;
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
 * @Route("/catalog")
 */
class CatalogController extends AbstractController
{
    /**
     * @Route("/", name="catalog_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        // Affiche la vue 'catalog/index.html.twig' avec une variable TWIG 'articles'
        // qui pointe vers la liste de toutes les articles en base de données
        return $this->render('catalog/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="catalog_show", methods={"GET"})
     */
    public function show(Article $article): Response
    {
        // Retourne la vue 'catalog/show.html.twig" pour l'article correspondant
        return $this->render('catalog/show.html.twig', [
            'article' => $article,
        ]);
    }


}
