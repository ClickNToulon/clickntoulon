<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Utilisateurs - Administration - TouSolidaires')
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            'id',
            'username',
            ArrayField::new('roles'),
            'email',
            'created_at',
            'updated_at',
            'banned',
            'isVerified'
        ];
    }
}
