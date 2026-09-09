<?php

namespace App\Enums;

enum VocAttributeType: string
{
    case Product = 'product';
    case Brand = 'brand';
    case Unit = 'unit';
    case Dietary = 'dietary';
    case PriceIntent = 'price_intent';
    case Exclusion = 'exclusion';
}
