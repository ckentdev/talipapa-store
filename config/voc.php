<?php

return [

    'stopwords' => [
        'na', 'ng', 'sa', 'ang', 'mga', 'nga', 'nang', 'ay', 'at',
        'the', 'a', 'an', 'of', 'from', 'and', 'or', 'for', 'to', 'in', 'on',
        'with', 'without', 'not', 'ko', 'og', 'kay', 'ra', 'lang', 'po',
        'yung', 'ka', 'mo', 'my', 'is', 'it', 'be', 'dili', 'hindi', 'walay',
        'that', 'this', 'nga',
    ],

    'brands' => [
        'bear brand',
        'coca-cola',
        'coca cola',
        'del monte',
        'lucky me',
        'nestle',
        'alaska',
        'magnolia',
        'selecta',
        'coke',
        'sprite',
        'pepsi',
        'nissin',
    ],

    'price_cheap' => ['barato', 'mura', 'cheap', 'affordable', 'budget', 'mura ra'],

    'price_premium' => ['premium', 'mahal', 'expensive', 'imported'],

    'dietary' => [
        'vegan' => ['vegan'],
        'plant-based' => ['plant-based', 'plant based', 'oat', 'soy', 'almond'],
        'non-cow' => ['non-cow', 'goat', 'kambing'],
        'non-dairy' => ['non-dairy', 'dairy-free', 'dairy free'],
    ],

    'canonical_products' => [
        'dried herring' => 'tuyo',
        'dried fish' => 'tuyo',
        'tuyo' => 'tuyo',
        'daing' => 'tuyo',
        'gatas' => 'milk',
        'milk' => 'milk',
        'bugas' => 'rice',
        'humay' => 'rice',
        'kan-on' => 'rice',
        'kanon' => 'rice',
        'rice' => 'rice',
        'mantika' => 'oil',
        'lana' => 'oil',
        'itlog' => 'egg',
        'egg' => 'egg',
        'eggs' => 'egg',
        'manok' => 'chicken',
        'chicken' => 'chicken',
        'baboy' => 'pork',
        'pork' => 'pork',
        'isda' => 'fish',
        'fish' => 'fish',
        'tinapay' => 'bread',
        'bread' => 'bread',
        'kape' => 'coffee',
        'coffee' => 'coffee',
        'tubig' => 'water',
        'water' => 'water',
        'coke' => 'soda',
        'cola' => 'soda',
        'soda' => 'soda',
    ],

    'exclusion_aliases' => [
        'baka' => 'cow',
        'cow' => 'cow',
        'dairy' => 'cow',
    ],

    'cow_dairy_terms' => [
        'cow',
        'baka',
        'fresh milk',
        'full cream',
        'full-cream',
        'whole milk',
        'fresh cow',
    ],

    'plant_milk_terms' => [
        'vegan',
        'oat',
        'soy',
        'almond',
        'plant',
        'coconut',
        'goat',
        'kambing',
        'non-dairy',
        'dairy-free',
        'dairy free',
        'plant-based',
    ],

];
