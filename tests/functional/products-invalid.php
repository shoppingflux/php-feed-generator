<?php
namespace ShoppingFeed\Feed;

use Faker\Factory;
use ShoppingFeed\Feed\Product\Product;

$feed = require __DIR__ . '/bootstrap.php';
$feed->setPlatform('Sf', '2.0.0');
$feed->setValidationFlags(ProductGenerator::VALIDATE_EXCLUDE);

$feed->addMapper(function(array $data, Product $product) {
    $product->setName($data['name']);
});

$generator = function($total) {
    $faker = Factory::create();
    while ($total--) {
        yield [
            'name' => $faker->name,
        ];
    }
};

$feed->write($generator($argv[2] ?? 10));