<?php
namespace ShoppingFeed\Feed\Product;

abstract class AbstractProduct
{
    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $gtin;

    /**
     * @var int
     */
    private $quantity = 0;

    /**
     * @var float
     */
    private $price = .0;

    /**
     * @var string
     */
    private $link;

    /**
     * @var array
     */
    private $discounts = [];

    /**
     * @var array
     */
    private $shippings = [];

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var string
     */
    private $mainImage;

    /**
     * @var string[]
     */
    private $additionalImages = [];

    /**
     * @var float
     */
    private $ecotax = .0;

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     *
     * @return $this
     */
    public function setReference($reference)
    {
        $this->reference = trim($reference);

        return $this;
    }

    /**
     * @return string
     */
    public function getGtin()
    {
        return $this->gtin;
    }

    /**
     * @return bool
     */
    public function hasGtin()
    {
        return (bool) $this->gtin;
    }

    /**
     * @param string $gtin
     *
     * @return $this
     */
    public function setGtin($gtin)
    {
        $this->gtin = trim($gtin);

        return $this;
    }


    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     *
     * @return $this
     */
    public function setQuantity($quantity)
    {
        $this->quantity = (int) $quantity;

        return $this;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @param string $link
     *
     * @return $this
     */
    public function setLink($link)
    {
        $this->link = trim($link);

        return $this;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param float $price
     *
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = \ShoppingFeed\Feed\price_format($price);

        return $this;
    }

    /**
     * @param float $value
     *
     * @return $this
     */
    public function addDiscount($value)
    {
        $this->discounts[] = new ProductDiscount($value);

        return $this;
    }

    /**
     * @return ProductDiscount[]
     */
    public function getDiscounts()
    {
        return $this->discounts;
    }

    /**
     * @return bool
     */
    public function hasDiscounts()
    {
        return (bool) $this->discounts;
    }

    /**
     * @param float  $cost
     * @param string $description
     *
     * @return $this
     */
    public function addShipping($cost, $description = '')
    {
        $this->shippings[] = new ProductShipping($cost, $description);

        return $this;
    }

    /**
     * @return ProductShipping[]
     */
    public function getShippings()
    {
        return $this->shippings;
    }

    /**
     * @return bool
     */
    public function hasShippings()
    {
        return (bool) $this->shippings;
    }

    /**
     * @param string $name
     * @param string $value
     *
     * @return $this
     */
    public function setAttribute($name, $value)
    {
        $attributes = new ProductAttribute($name, $value);

        $this->attributes[$attributes->getName()] = $attributes;

        return $this;
    }

    /**
     * @param array $attributes
     *
     * @return $this
     */
    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $name => $attribute) {
            $this->setAttribute($name, $attribute);
        }

        return $this;
    }

    /**
     * @return ProductAttribute[]
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return bool
     */
    public function hasAttributes()
    {
        return (bool) $this->attributes;
    }

    /**
     * @param $url
     *
     * @return $this
     */
    public function setMainImage($url)
    {
        $this->mainImage = trim($url);

        return $this;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setAdditionalImage($url)
    {
        $url = trim($url);

        $this->additionalImages[$url] = $url;

        return $this;
    }

    /**
     * @param array $images
     *
     * @return $this
     */
    public function setAdditionalImages(array $images)
    {
        foreach ($images as $image) {
            $this->setAdditionalImage($image);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasImages()
    {
        return (bool) ($this->mainImage || $this->additionalImages);
    }

    /**
     * @return string
     */
    public function getMainImage()
    {
        return $this->mainImage;
    }

    /**
     * @return string[]
     */
    public function getAdditionalImages()
    {
        return $this->additionalImages;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->reference && isset($this->price);
    }

    /**
     * @return float
     */
    public function getEcotax()
    {
        return $this->ecotax;
    }

    /**
     * @param float $ecotax
     *
     * @return $this
     */
    public function setEcotax($ecotax)
    {
        $this->ecotax = \ShoppingFeed\Feed\price_format($ecotax);

        return $this;
    }
}
