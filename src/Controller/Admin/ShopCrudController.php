<?php

namespace App\Controller\Admin;

use App\Entity\Shop;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ShopCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Shop::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Boutiques - Administration - TouSolidaires')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            IdField::new('owner_id')->hideOnIndex(),
            TextField::new('name')->setMaxLength(40),
            TextareaField::new('description')->hideOnIndex(),
            TextareaField::new('address'),
            IntegerField::new('postalCode'),
            TextField::new('city'),
            TelephoneField::new('phone'),
            EmailField::new('email'),
            DateTimeField::new('created_at')->hideOnIndex(),
            DateTimeField::new('updated_at')->hideOnIndex(),
            ImageField::new('cover')->setBasePath('/uploads/shops/')->setUploadDir('/public/uploads/shops/'),
            SlugField::new('slug')->setTargetFieldName('name')->hideOnIndex(),
            BooleanField::new('banned')->hideOnIndex(),
            BooleanField::new('isVerified')->hideOnIndex(),
            IntegerField::new('status')->hideOnIndex()
        ];
    }
}
