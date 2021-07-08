<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            'id',
            'shop_id',
            'buyer_id',
            'day',
            'time_start',
            'time_end',
            'products_id',
            'quantity',
            'status',
            'total'
        ];
    }
}
