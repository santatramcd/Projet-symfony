<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Menu;
use App\Entity\User;
use App\Entity\Media;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator
    ){}
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator->setController(ArticleCrudController::class)->generateUrl();
        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Symfony CMS');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Dashboard', 'fa fa-home', 'app_home');
    
        if($this->isGranted('ROLE_ADMIN')){
            yield MenuItem::submenu('Menus', 'fas fa-list')->setSubItems([
                MenuItem::linkToCrud('Pages', 'fas fa-file', Menu::class)
                    ->setQueryParameter('submenuIndex', MenuCrudController::MENU_PAGES),
                MenuItem::linkToCrud('Articles', 'fas fa-newspaper', Menu::class)
                    ->setQueryParameter('submenuIndex', MenuCrudController::MENU_ARTICLES),
                MenuItem::linkToCrud('Liens personnalisés', 'fas fa-link', Menu::class)
                    ->setQueryParameter('submenuIndex', MenuCrudController::MENU_LINKS),
                MenuItem::linkToCrud('Catégories', 'fab fa-delicious', Menu::class)
                    ->setQueryParameter('submenuIndex', MenuCrudController::MENU_CATEGORIES),
            ]);
        }

        if($this->isGranted('ROLE_AUTHOR')){
            yield MenuItem::subMenu('Articles', 'fas fa-newspaper')->setSubItems([
                MenuItem::linkToCrud('Tous les articles', 'fas fa-newspaper', Article::class),
                MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Article::class)->setAction(Crud::PAGE_NEW),
                MenuItem::linkToCrud('Categories', 'fas fa-list', Category::class)
            ]);

            yield MenuItem::subMenu('Médias', 'fas fa-photo-video')->setSubItems([
                MenuItem::linkToCrud('Médiathèque', 'fas fa-photo-video', Media::class),
                MenuItem::linkToCrud('Ajouter', 'fas fa-plus', Media::class)->setAction(Crud::PAGE_NEW),
            ]);
        }

        if($this->isGranted('ROLE_ADMIN')){
            yield MenuItem::linkToCrud('commentaires', 'fas fa-comment', Comment::class);

            yield MenuItem::subMenu('Comptes', 'fas fa-user')->setSubItems([
                MenuItem::linkToCrud('Tous les comptes', 'fas fa-user-friend', User::class),
                MenuItem::linkToCrud('Ajouter', 'fas fa-plus', User::class)->setAction(Crud::PAGE_NEW)
            ]);
            
        }
    }
}
