<?php

namespace App\Controller\Common;

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
        return $this->render('Common/page_info_personnel/index.html.twig');
    }
}
