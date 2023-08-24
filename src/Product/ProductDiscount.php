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
     * @var string
     */
    private $startDateTime;

    /**
     * @var string
     */
    private $endDateTime;

    /**
     * @param float $value
     */
    public function __construct($value, $startDateTime = '', $endDateTime = '')
    {
        $this->value         = \ShoppingFeed\Feed\price_format($value);
        $this->type          = self::TYPE_PRICE;
        $this->startDateTime = $startDateTime;
        $this->endDateTime   = $endDateTime;
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

    /**
     * @return string
     */
    public function getStartDateTime()
    {
        return $this->startDateTime;
    }

    /**
     * @return string
     */
    public function getEndDateTime()
    {
        return $this->endDateTime;
    }
}
