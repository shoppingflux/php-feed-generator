<?php
namespace ShoppingFeed\Feed;

require_once __DIR__ . '/vendor/autoload.php';

$generator = new ProductGenerator('php://output', 'json');

// in order for the generator to work, we need at least one mapper
// see the documentation for more details
$generator->addMapper(function(array $item, Product\Product $product) {
    $product
        ->setName($item['title'])
        ->setReference($item['sku'])
        ->setPrice($item['price'])
        ->setQuantity($item['quantity']);
});

// hardcode the data of your products here (or you can import it from an external file, if you wish)
$items[0] = ['sku' => 1, 'title' => 'Product 1', 'price' => 5.99, 'quantity' => 3];
$items[1] = ['sku' => 2, 'title' => 'Product 2', 'price' => 12.99, 'quantity' => 6];

// now generate the feed, which will output the file
// in the specified format and at the specified location
$generator->write($items);