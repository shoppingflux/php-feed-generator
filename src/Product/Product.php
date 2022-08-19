<?php
namespace ShoppingFeed\Feed\Product;

final class Product extends AbstractProduct
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var ProductDescription
     */
    private $description;

    /**
     * @var ProductBrand
     */
    private $brand;

    /**
     * @var ProductCategory
     */
    private $category;

    /**
     * @var array
     */
    private $variations = [];

    /**
     * @var ProductVariation
     */
    private $variationPrototype;

    /**
     * @var float
     */
    private $vat = .0;

    /**
     * @var float
     */
    private $weight = .0;

    public function __construct()
    {
        $this->variationPrototype = new ProductVariation();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = trim($name);

        return $this;
    }

    /**
     * @return ProductDescription|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return bool
     */
    public function hasDescription()
    {
        return (bool) $this->description;
    }

    /**
     * @param string $fullDesc
     * @param string $shortDesc
     *
     * @return Product
     */
    public function setDescription($fullDesc, $shortDesc = '')
    {
        $this->description = new ProductDescription($fullDesc, $shortDesc);

        return $this;
    }

    /**
     * @return ProductBrand|null
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * @return bool
     */
    public function hasBrand()
    {
        return (bool) $this->brand;
    }

    /**
     * @param string $name
     * @param string $link
     *
     * @return Product
     */
    public function setBrand($name, $link = '')
    {
        $this->brand = new ProductBrand($name, $link);

        return $this;
    }

    /**
     * @return ProductCategory|null
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return bool
     */
    public function hasCategory()
    {
        return (bool) $this->category;
    }

    /**
     * @param string $name
     * @param string $link
     *
     * @return Product
     */
    public function setCategory($name, $link = '')
    {
        $this->category = new ProductCategory($name, $link);

        return $this;
    }

    /**
     * @return ProductVariation
     */
    public function createVariation()
    {
        $variation          = clone $this->variationPrototype;
        $this->variations[] = $variation;

        return $variation;
    }

    /**
     * @return bool
     */
    public function hasVariations()
    {
        return (bool) $this->variations;
    }

    /**
     * @return ProductVariation[]
     */
    public function getVariations()
    {
        return $this->variations;
    }

    /**
     * Validation requires that:
     * - A name has been defined
     * - A reference has been defined
     * - A price has been set
     *
     * Variations validation only operates on:
     * - reference
     * - price
     *
     * return bool
     */
    public function isValid()
    {
        if ($this->name && parent::isValid()) {
            foreach ($this->getVariations() as $variation) {
                if (! $variation->isValid()) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    /**
     * @return float
     */
    public function getVat()
    {
        return $this->vat;
    }

    /**
     * @param float $vat
     *
     * @return $this
     */
    public function setVat($vat)
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return (float) $this->weight;
    }

    /**
     * @param float $weight
     *
     * @return $this
     */
    public function setWeight($weight)
    {
        $this->weight = (float) $weight;

        return $this;
    }

}
