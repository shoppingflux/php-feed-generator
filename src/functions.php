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
