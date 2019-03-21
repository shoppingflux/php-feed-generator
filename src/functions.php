<?php
namespace ShoppingFeed\Feed;

/**
 * @param float $value
 *
 * @return string
 */
function price_format($value) {
    return number_format((float) $value, 2, '.', '');
}

/**
 * @see https://www.w3.org/TR/REC-xml/#charsets for allowed utf-8 chars in xml
 *
 * @param string $value
 *
 * @return null|string
 */
function xml_utf8_clean($value) {
    return preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', $value);
}


function arrayToCSV($arr)
{
    $f = fopen('php://memory', 'rw');
    foreach ($arr as $row) {
        fputcsv($f, $row);
    }
    rewind($f);
    $csv = stream_get_contents($f);
    fclose($f);

    return $csv;
}

function utf8_converter($array)
{
    array_walk_recursive($array, function(&$item, $key){
        if(!mb_detect_encoding($item, 'utf-8', true)){
            $item = utf8_encode($item);
        }
    });

    return $array;
}


function read($file)
{
    $fp = fopen($file, 'rb');

    while(($line = fgets($fp)) !== false)
        yield rtrim($line, "\r\n");

    fclose($fp);
}
