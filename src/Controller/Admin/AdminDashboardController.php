<?php

namespace App\Controller\Admin;

use App\Entity\File;
use App\Entity\Folder;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Files - Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToUrl('Home', 'fa fa-home', $this->generateUrl("site_view"));

        yield MenuItem::section('Entities');
        yield MenuItem::linkToCrud('Users', 'fas fa-list', User::class);
        yield MenuItem::linkToCrud('Folders', 'fas fa-list', Folder::class);
        yield MenuItem::linkToCrud('Files', 'fas fa-list', File::class);
    }
}
