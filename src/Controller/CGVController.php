<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CGVController extends AbstractController
{
    /**
     * @Route("/CGV", name="cgv")
     */
    public function index(): Response
    {
        return $this->render('cgv/index.html.twig', [
            'controller_name' => 'CGVController',
        ]);
    }
}
