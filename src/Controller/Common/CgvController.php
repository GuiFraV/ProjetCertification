<?php

namespace App\Controller\Common;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CgvController extends AbstractController
{
    /**
     * @Route("/cgv", name="cgv")
     */
    public function index(): Response
    {
        return $this->render('Common/cgv/index.html.twig', [
            'controller_name' => 'CgvController',
        ]);
    }
}
