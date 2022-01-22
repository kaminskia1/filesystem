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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class AdminDashboardController
 *
 * @isGranted("ROLE_ADMIN")
 * @package App\Controller\Admin
 */
class AdminDashboardController extends AbstractDashboardController
{
    /**
     * Admin Dashboard Route
     *
     * @isGranted("ROLE_ADMIN")
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Files');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToUrl('Home', 'fa fa-home', $this->generateUrl("site_view"));

        yield MenuItem::section('Entities');
        yield MenuItem::linkToCrud('Users', 'fas fa-list', User::class);
    }
}
