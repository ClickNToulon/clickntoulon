<?php

namespace App\Helper\FilterData;

use App\Domain\Shop\Tag;

class SearchShopData
{

    public int $page = 1;

    public ?string $q;

    public ?string $city;

    public ?string $postalCode;

    /**
     * @var Tag[]
     */
    public array $tag = [];
}