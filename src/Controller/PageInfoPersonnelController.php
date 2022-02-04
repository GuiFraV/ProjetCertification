<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageInfoPersonnelController extends AbstractController
{
    /**
     * @Route("/page/info/personnel", name="page_info_personnel")
     */
    public function index(): Response
    {
        return $this->render('page_info_personnel/index.html.twig', [
            'controller_name' => 'PageInfoPersonnelController',
        ]);
    }
}
