<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClickAndCollectController extends AbstractController
{
    /**
     * @Route("/clickandcollect", name="clickandcollect")
     */
    public function index(): Response
    {
        return $this->render('click_and_collect/index.html.twig', [
            'controller_name' => 'ClickAndCollectController',
        ]);
    }
}
