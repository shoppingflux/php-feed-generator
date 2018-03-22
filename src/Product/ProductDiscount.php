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
     * @var bool
     */
    private $isSale;

    /**
     * @var \DateTimeInterface
     */
    private $startAt;

    /**
     * @var \DateTimeInterface
     */
    private $endAt;

    /**
     * @var string
     */
    private $type;

    public function __construct(
        $value,
        $iSale = false,
        \DateTimeInterface $startAt = null,
        \DateTimeInterface $endAt = null)
    {
        $this->value   = \ShoppingFeed\Feed\price_format($value);
        $this->type    = self::TYPE_PRICE;
        $this->isSale  = (bool) $iSale;
        $this->startAt = $startAt;
        $this->endAt   = $endAt;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isSale()
    {
        return $this->isSale;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
