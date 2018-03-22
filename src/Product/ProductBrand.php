<?php
namespace ShoppingFeed\Feed\Product;

class ProductBrand
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $link;

    /**
     * @param string $name
     * @param string $link
     */
    public function __construct($name, $link = '')
    {
        $this->name = trim($name);
        $this->link = trim($link);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }
}
