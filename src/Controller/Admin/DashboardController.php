<?php

namespace App\Controller\Admin;

use App\Entity\Categories;
use App\Entity\Products;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(): Response
    {
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();

        return $this->redirect($routeBuilder->setController(ProductsCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Mahasiah');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoRoute('Accueil', 'fas fa-home', 'index');
        yield MenuItem::linkToCrud('Articles', 'fas fa-box-open', Products::class);
        yield MenuItem::linkToCrud('Catégories', 'fas fa-tag', Categories::class);
        yield MenuItem::linkToCrud('Clients', 'fas fa-user', User::class);
        yield MenuItem::linktoRoute('Commandes', 'fas fa-boxes', 'order_index');
    }
}
