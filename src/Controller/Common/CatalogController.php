<?php
// Le dossier virtuel de la class de ce fichier
namespace App\Controller\Common;

// Utilisation de l'entité User pour la liste dans la BDD

use App\Classe\Search;
use App\Service\CartService;

use Symfony\Component\HttpFoundation\Request;

use App\Entity\Article;

use App\Form\User\SearchFormType;
// Utilisation d' ArticleRepository pour récupérer la liste et pour afficher les Details
use App\Repository\ArticleRepository;

// Utilisation de l'EntityManager pour créer, supprimer et modifier un article
use Doctrine\ORM\EntityManagerInterface;

// Utilisation de l'abstract controller pour les méthodes qui sont héritées : par exemple, render, renderForm, createFrom etc.
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// Utilisation du Response juste pour définir que les fonctions liées aux routes vont retournées une réponse au navigateur 
use Symfony\Component\HttpFoundation\Response;

// Utilisation du Route comme annotation pour rendre accessible une focntion par un client (ex: navigateur)
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/catalog")
 */
class CatalogController extends AbstractController
{
     
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManager = $entityManagerInterface;
    }

    /**
     * @Route("/", name="catalog_index", methods={"GET"})
     */
    public function index(ArticleRepository $articleRepository, Request $request, CartService $cartService): Response
    {
        $cart = $cartService->get();

        $search = new Search();

        $form = $this->createForm(SearchFormType::class, $search);

        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid())
        {
            $article = $articleRepository->findWithSearch($search);
        
        }else{

            $article = $articleRepository->findAll();

        }
       
        // Affiche la vue 'catalog/index.html.twig' avec une variable TWIG 'articles'
        // qui pointe vers la liste de toutes les articles en base de données
        return $this->render('Common/catalog/index.html.twig', [
            'articles' => $article,
            'form' => $form->createView(),
            'cart' => $cart
        ]);
    }

    /**
     * @Route("/{id}", name="catalog_show", methods={"GET"})
     */
    public function show(Article $article,CartService $cartService): Response
    {
        $cart = $cartService->get();
        
        $products = $this->entityManager->getRepository(Article::class)->findByisBest(1);

        // Retourne la vue 'catalog/show.html.twig" pour l'article correspondant
        return $this->render('Common/catalog/show.html.twig', [
            'article' => $article,
            'articles' => $products,
            'cart' => $cart
        ]);
    }


}
