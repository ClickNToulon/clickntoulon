<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Produits - Administration - TouSolidaires')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            IdField::new('shop_id'),
            TextField::new('name'),
            TextareaField::new('description')->setMaxLength(50),
            IntegerField::new('price'),
            IntegerField::new('deal_price'),
            DateTimeField::new('deal_start')->hideOnIndex(),
            DateTimeField::new('deal_end')->hideOnIndex(),
            ImageField::new('image')->setBasePath('/uploads/products/')->setUploadDir('/public/uploads/products/'),
            DateTimeField::new('deletedAt')->hideOnIndex(),
            IntegerField::new('status')->hideOnIndex()
        ];
    }
}
