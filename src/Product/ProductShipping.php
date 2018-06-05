<?php
namespace ShoppingFeed\Feed\Product;

class ProductShipping
{
    /**
     * @var string
     */
    private $cost;

    /**
     * @var string
     */
    private $description;

    /**
     * @param float  $cost
     * @param string $description
     */
    public function __construct($cost, $description = '')
    {
        $this->cost         = \ShoppingFeed\Feed\price_format($cost);
        $this->description  = trim((string) $description);
    }

    /**
     * @return string
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
