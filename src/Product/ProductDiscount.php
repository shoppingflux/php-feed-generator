<?php
namespace ShoppingFeed\Feed\Product;

class ProductDiscount
{
    const TYPE_PRICE = 'price';

    /**
     * @var float
     */
    private $value;

    /**
     * @var string
     */
    private $type;

    /**
     * @param float $value
     */
    public function __construct($value)
    {
        $this->value   = \ShoppingFeed\Feed\price_format($value);
        $this->type    = self::TYPE_PRICE;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
