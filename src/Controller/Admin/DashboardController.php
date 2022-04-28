<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Carousel;
use App\Entity\Categorie;
use App\Repository\UserRepository;
use App\Repository\ArticleRepository;
use App\Repository\CommandeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard as HtmlDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
// Permet l'ajout de la sécurité IsGranted("ROLE_ADMIN");
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DashboardController extends AbstractDashboardController
{

    private $userRepository;
    private $commandeRepository;
    private $articleRepository;

    public function __construct(
        UserRepository $userRepository,
        CommandeRepository $commandeRepository,
        ArticleRepository $articleRepository
    )
    {
        $this->userRepository = $userRepository;
        $this->commandeRepository = $commandeRepository;
        $this->articleRepository = $articleRepository;
    }

    /**
     * @Route("/admin/dashboard", name="admin_dashboard")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(): Response
    {
        return $this->render('Bundles/EasyAdminBundle/welcome.html.twig', [
            'allArticle'=> $this->articleRepository->countArticle(),
            'allUser' => $this->userRepository->countUser(),            
            'allCommande' => $this->commandeRepository->countCommande(),

        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('SummerField');
            
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Dashboard', 'fa fa-home', 'home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-seedling', Categorie::class);
        yield MenuItem::linkToCrud('Articles', 'fas fa-pepper-hot', Article::class);
        yield MenuItem::linkToCrud('Carrousel', 'fas fa-desktop', Carousel::class);
    }
}
