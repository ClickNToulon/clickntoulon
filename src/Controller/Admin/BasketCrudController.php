<?php

namespace App\Controller\Admin;

use App\Entity\Basket;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class BasketCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Basket::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Paniers - Administration - TouSoldiares')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            'id',
            'shop_id',
            'owner_id',
            'products_id',
            'quantity'
        ];
    }
}
