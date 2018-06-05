<?php
namespace ShoppingFeed\Feed;

use Faker\Factory;
use ShoppingFeed\Feed\Product\Product;

$feed = $feed = require __DIR__ . '/bootstrap.php';
$feed->setPlatform('Sf', '2.0.0');

/**
 * Hardcode shipping information
 */
$feed->addProcessor(function(array $data) {
    $data['shipping_cost'] = 12;
    $data['shipping_time'] = 'delivered in 2 days';

    return $data;
});

/**
 * Add filter that exclude products when prices greater than 5
 */
$feed->addFilter(function(array $data) {
    return $data['price'] > 5;
});

$feed->addMapper(function(array $data, Product $product) {
    $product
        ->setReference($data['sku'])
        ->setGtin($data['ean'])
        ->setName($data['name'])
        ->setQuantity($data['quantity'])
        ->setPrice($data['price'])
        ->setDescription($data['description_full'], $data['description_short'])
        ->setBrand($data['brand_name'], $data['brand_link'])
        ->setCategory($data['category_name'], $data['category_link'])
        ->addDiscount($data['old_price'])
        ->addShipping($data['shipping_cost'], $data['shipping_time'])
        ->setAttributes(['color' => 'value1', 'material' => 'metal'])
        ->setMainImage($data['image_main'])
        ->setAdditionalImages([$data['image1'], $data['image2']])
    ;
});
$feed->addMapper(function(array $data, Product $product) {
   foreach ($data['variations'] as $item) {
       $product
            ->createVariation()
            ->setReference($item['sku'])
            ->setPrice($item['price'])
            ->setQuantity($item['quantity'])
            ->setMainImage($data['image_main'])
       ;
   }
});

$generator = function($total) {
    $faker = Factory::create();

    while ($total--) {
        $variartionCount = $argv[3] ?? 5;
        $variations      = [];

        while ($variartionCount--) {
            $variations[] = [
                'sku'       => $faker->ean13,
                'price'     => $faker->randomFloat(2, 0, 200),
                'quantity'  => $faker->numberBetween(0, 100)
            ];
        }
        yield [
            'name'              => $faker->name,
            'sku'               => $faker->ean13,
            'ean'               => $faker->ean13,
            'quantity'          => $faker->numberBetween(0, 100),
            'price'             => $faker->randomFloat(2, 0, 200),
            'old_price'         => $faker->randomFloat(2, 0, 200),
            'description_short' => $faker->text(30),
            'description_full'  => $faker->randomHtml(1, 1),
            'brand_name'        => $faker->company,
            'brand_link'        => $faker->url,
            'category_name'     => $faker->company,
            'category_link'     => $faker->url,
            'image_main'        => $faker->imageUrl(),
            'image1'            => $faker->imageUrl(),
            'image2'            => $faker->imageUrl(),
            'variations'        => $variations
        ];
    }
};

$feed->write($generator($argv[2] ?? 10));