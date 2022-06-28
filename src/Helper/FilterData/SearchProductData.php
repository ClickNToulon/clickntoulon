<?php

namespace App\Helper\FilterData;

use App\Domain\Product\ProductType;
use App\Domain\Shop\Shop;

class SearchProductData
{
    public int $page = 1;

    public ?string $q;

    /**
     * @var ProductType[]
     */
    public array $types = [];

    public ?Shop $shop = null;

    public ?int $max;

    public ?int $min;

    public bool $promo = false;
}