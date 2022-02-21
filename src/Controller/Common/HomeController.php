<?php
// Le dossier virtuel de la class de ce fichier
namespace App\Controller\Common;
// Utilisation de l'abstract controller pour les méthodes qui sont héritées : par exemple, render, renderForm, createFrom etc.

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Commande;

use App\Entity\Article;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Utilisation du Response juste pour définir que les fonctions liées aux routes vont retournées une réponse au navigateur 
use Symfony\Component\HttpFoundation\Response;
// Utilisation du Route comme annotation pour rendre accessible une focntion par un client (ex: navigateur)
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->entityManager = $entityManagerInterface;
    }


    /**
     * @Route("/home", name="home")
     */
    public function index(): Response
    {
        $products = $this->entityManager->getRepository(Article::class)->findByisBest(1);

        // dd($products);

        // Affiche la vue 'home/index.html.twig'
        return $this->render('Common/home/index.html.twig', [
            'articles' => $products,
        ]);
    }
}
