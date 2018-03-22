<?php

namespace ShoppingFeed\Feed\Product;

class ProductDescription
{
    /**
     * @var string
     */
    private $full;

    /**
     * @var string
     */
    private $short;

    /**
     * @param string $full
     * @param string $short
     */
    public function __construct($full, $short = '')
    {
        $this->full  = trim($full);
        $this->short = trim($short);
    }

    /**
     * @return string
     */
    public function getFull()
    {
        return $this->full;
    }

    /**
     * @return string
     */
    public function getShort()
    {
        return $this->short;
    }
}
