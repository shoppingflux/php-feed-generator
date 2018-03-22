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
        $this->name = $name;

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
        $variation          = new ProductVariation();
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
     * @return array
     */
    public function getVariations()
    {
        return $this->variations;
    }
}
