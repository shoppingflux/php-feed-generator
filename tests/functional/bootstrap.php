<?php
use ShoppingFeed\Feed\ProductGenerator;

require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(200);
    header('Content-Type: application/xml');
}

if (empty($argv[1])) {
    $file    = 'php://output';
    $writer  = 'xml';
} else {
    $file   = $argv[1];
    $writer = substr(strrchr($argv[1], '.'), 1);
}

return new ProductGenerator($file, $writer);