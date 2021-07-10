<?php

namespace App\Controller\Admin;

use App\Entity\Basket;
use App\Entity\Category;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Shop;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin-easyadmin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Administration - TouSolidaires')
            ->setFaviconPath('images/logo.svg')
            ->setTranslationDomain('admin')
            ->disableUrlSignatures()
            ->generateRelativeUrls();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Accueil', 'bi-house-fill');
        yield MenuItem::linkToCrud('Users', 'bi-people-fill', User::class);
        yield MenuItem::linkToCrud('Shops', 'bi-shop-window', Shop::class);
        yield MenuItem::linkToCrud('Products', 'bi-box-seam', Product::class);
        yield MenuItem::linkToCrud('Categories', 'bi-tags-fill', Category::class);
        yield MenuItem::linkToCrud('Baskets', 'bi-cart-fill', Basket::class);
        yield MenuItem::linkToCrud('Orders', 'bi-truck', Order::class);
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        // Usually it's better to call the parent method because that gives you a
        // user menu with some menu items already created ("sign out", "exit impersonation", etc.)
        // if you prefer to create the user menu from scratch, use: return UserMenu::new()->...
        return parent::configureUserMenu($user)
            // use the given $user object to get the user name
            ->setName($user->getUsername())
            // use this method if you don't want to display the name of the user
            ->displayUserName(true)

            // you can return an URL with the avatar image
            ->setAvatarUrl('http://tousolidaires.qbtl.fr/uploads/users/' . $user->getAvatar())
            // use this method if you don't want to display the user image
            ->displayUserAvatar(true);
    }
}
