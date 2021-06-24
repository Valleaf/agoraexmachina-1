<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class KeywordController extends AbstractController
{
    /**
     * @Route("/keyword", name="keyword")
     */
    public function index(): Response
    {
        return $this->render('keyword/index.html.twig', [
            'controller_name' => 'KeywordController',
        ]);
    }
}
