<?php
namespace ShoppingFeed\Feed\Product;

class ProductShipping
{
    const DELAY_UNIT_DAY = 'day';

    /**
     * @var string
     */
    private $cost;

    /**
     * @var int
     */
    private $delayValue;

    /**
     * @var string
     */
    private $delayUnit;

    /**
     * @var bool
     */
    private $delayOpening;

    /**
     * @var string
     */
    private $description;

    /**
     * @param float  $cost
     * @param int    $delayInDays
     * @param string $description
     */
    public function __construct($cost, $delayInDays, $description = '')
    {
        $this->cost         = \ShoppingFeed\Feed\price_format($cost);
        $this->delayValue   = (int) $delayInDays;
        $this->description  = (string) $description;
        $this->delayUnit    = self::DELAY_UNIT_DAY;
        $this->delayOpening = false;
    }

    /**
     * @return string
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * @return int
     */
    public function getDelayValue()
    {
        return $this->delayValue;
    }

    /**
     * @return int
     */
    public function getDelayUnit()
    {
        return $this->delayUnit;
    }

    /**
     * @return int
     */
    public function isDelayWithinOpening()
    {
        return $this->delayOpening;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
