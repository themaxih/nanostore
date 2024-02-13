<?php

namespace App\Service\Sort;

class SortBy
{
    public string $column;
    public string $order;

    public function __construct(SortType $type, SortOrder $order = SortOrder::ASC)
    {
        $this->column = $type->value;
        $this->order = $order->name;
    }
}