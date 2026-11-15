<?php

namespace App\Models;

class CatalogueOptions
{
    public const CATEGORIES = [
        'Mug' => [
            'sizes' => ['11 oz', '15 oz'],
            'finishes' => ['Gloss', 'Matte'],
            'methods' => ['Sublimation' => ['Ceramic'], 'UV printing' => ['Ceramic', 'Steel']],
        ],
        'Poster' => [
            'sizes' => ['A4', 'A3', 'A2'],
            'finishes' => ['Gloss', 'Matte', 'Uncoated'],
            'methods' => ['Digital printing' => ['Cardstock', 'Photo paper']],
        ],
        'T-shirt' => [
            'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
            'finishes' => ['Standard'],
            'methods' => ['Screen printing' => ['Cotton', 'Polyester'], 'DTG' => ['Cotton']],
        ],
    ];

    public static function supports(string $category, array $variant): bool
    {
        $options = self::CATEGORIES[$category] ?? null;

        return $options !== null
            && in_array($variant['size'], $options['sizes'], true)
            && in_array($variant['finish'], $options['finishes'], true)
            && in_array($variant['material'], $options['methods'][$variant['method']] ?? [], true);
    }
}
