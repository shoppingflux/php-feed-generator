<?php
use ShoppingFeed\Feed\ProductGenerator;

require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(200);
    header('Content-Type: application/xml');
}

return new ProductGenerator(empty($argv[1]) ? 'php://output' : $argv[1]);