<?php

namespace App\Service\Sort;

enum SortType: string {
    case byTitle = 'title';
    case byPrice = 'price';
}