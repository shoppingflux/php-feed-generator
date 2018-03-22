<?php
namespace ShoppingFeed\Feed\Product;

class ProductAttribute
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @var bool
     */
    private $isVariation;

    /**
     * @param string $name
     * @param string $value
     * @param bool   $isVariation
     */
    public function __construct($name, $value, $isVariation = false)
    {
        $this->name        = trim($name);
        $this->value       = trim($value);
        $this->isVariation = (bool) $isVariation;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isVariation()
    {
        return $this->isVariation;
    }
}
