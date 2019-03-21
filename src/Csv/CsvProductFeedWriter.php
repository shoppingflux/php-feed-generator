<?php

namespace ShoppingFeed\Feed\Csv;


use ShoppingFeed\Feed\Product\AbstractProduct;
use ShoppingFeed\Feed\Product\Product;
use ShoppingFeed\Feed\Product\ProductAttribute;
use ShoppingFeed\Feed\Product\ProductBrand;
use ShoppingFeed\Feed\Product\ProductCategory;
use ShoppingFeed\Feed\Product\ProductDescription;
use ShoppingFeed\Feed\Product\ProductDiscount;
use ShoppingFeed\Feed\Product\ProductShipping;
use ShoppingFeed\Feed\Product\ProductVariation;
use ShoppingFeed\Feed\ProductFeedMetadata;
use ShoppingFeed\Feed\ProductFeedWriterInterface;
use ShoppingFeed\Feed\RigidFormatWriterInterface;
use ShoppingFeed\Feed\TempArrayFileWriter;

class CsvProductFeedWriter implements ProductFeedWriterInterface, RigidFormatWriterInterface
{
    const VERSION = '1.0.0';

    const BATCH_MAX_SIZE = 50;

    const EMPTY_VALUE = '';

    const ATTRIBUTE_PREFIX = 'attribute_';

    const IMAGE_PREFIX = 'image';

    private $handle;

    private $uri;

    private $writeBuffer = [];

    /**
     * @var TempArrayFileWriter
     */
    private $tempWriter = null;

    /**
     * @return bool
     */
    public function formatRigid()
    {
        return true;
    }


    /**
     * @var int
     */
    private $maxAdditionalImagesQuantity;


    /**
     * @var int
     */

    private $writeCount = 0;

    /**
     * @var string[] $attributeNames
     */
    private $attributeNames = [];


    /**
     * @var string[] $baseHeaders
     */
    private $baseHeaders = [
        'name', 'reference', 'quantity', 'price', 'link', 'gtin', 'discount.value', 'shipping.amount', 'shipping.label'
    ];


    /**
     * @var string[] $endHeaders
     */
    private $endHeaders = [
        'description.full', 'description.short', 'brand.name', 'brand.link', 'category.name', 'category.link', 'productReference'
    ];


    /**
     * @param int $maxAdditionalImagesQuantity
     */
    public function setMaxAdditionalImagesQuantity($maxAdditionalImagesQuantity)
    {
        $this->maxAdditionalImagesQuantity = $maxAdditionalImagesQuantity;
    }


    /**
     * @return int
     */
    public function getMaxAdditionalImagesQuantity()
    {
        return $this->maxAdditionalImagesQuantity;
    }


    /**
     * @param $name
     * @return void
     */
    public function addAttributeName($name)
    {
        if (is_string($name) && $name !== '' && !in_array($name, $this->attributeNames)) {
            $this->attributeNames[] = $name;
        }
    }

    /**
     * @param string $uri
     * @return void
     */
    public function open($uri)
    {
        $this->uri = $uri;
        $this->prepareFilePointer("w");

    }


    /**
     * @return void
     */
    public function writeHeader()
    {
        $imageHeaders = [];
        $imageHeaders[] = self::IMAGE_PREFIX . '_main';
        for ($i = 1; $i <= $this->maxAdditionalImagesQuantity; $i++) {
            $imageHeaders[] = self::IMAGE_PREFIX . $i;
        }

        $attributeHeaders = [];
        foreach ($this->attributeNames as $attributeName) {
            $attributeHeaders[] = self::ATTRIBUTE_PREFIX . $attributeName;
        }

        $headers = array_merge($this->baseHeaders, $attributeHeaders, $imageHeaders, $this->endHeaders);

        fputcsv($this->handle, $headers);
    }


    /**
     * @param Product $product
     * @return void
     */
    public function writeProduct(Product $product)
    {
        $row = [];
        $row[] = $this->getValue($product->getName());
        $this->writeSharedProduct($product, $row);
        /**
         * @var ProductDescription $productDescription
         */
        $productDescription = $product->getDescription();
        if (!empty($productDescription)) {
            array_push($row, $this->getValue($productDescription->getFull()), $this->getValue($productDescription->getShort()));
        } else {
            array_push($row, self::EMPTY_VALUE, self::EMPTY_VALUE);
        }
        $productDescription = null;

        /**
         * @var ProductBrand $productBrand
         */
        $productBrand = $product->getBrand();
        if (!empty($productBrand)) {
            array_push($row, $this->getValue($productBrand->getName()), $this->getValue($productBrand->getLink()));
        } else {
            array_push($row, self::EMPTY_VALUE, self::EMPTY_VALUE);
        }
        $productBrand = null;

        /**
         * @var ProductCategory $productCategory
         */
        $productCategory = $product->getCategory();
        if (!empty($productCategory)) {
            array_push($row, $this->getValue($productCategory->getName()), $this->getValue($productCategory->getLink()));
        } else {
            array_push($row, self::EMPTY_VALUE, self::EMPTY_VALUE);
        }
        $productCategory = null;
        // means is a product
        $row[] = self::EMPTY_VALUE;

        $this->writeBuffer[] = $row;
        $this->writeCount++;


        /**
         * @var ProductVariation[] $productVariations
         */
        $productVariations = $product->getVariations();
        foreach ($productVariations as $productVariation) {
            $row = [];
            // name
            $row[] = self::EMPTY_VALUE;
            $this->writeSharedProduct($productVariation, $row);
            // no description
            array_push($row, self::EMPTY_VALUE, self::EMPTY_VALUE);
            // no brand
            array_push($row, self::EMPTY_VALUE, self::EMPTY_VALUE);
            // no category
            array_push($row, self::EMPTY_VALUE, self::EMPTY_VALUE);
            // means is a variation
            $row[] = $product->getReference();
            $this->writeBuffer[] = $row;
            $this->writeCount++;

        }
        unset($productVariations);

        if ($this->writeCount >= self::BATCH_MAX_SIZE) {
            $this->pushWriteBuffer();
        }

    }

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
    }

    /**
     * @param ProductFeedMetadata $metadata
     * @return void
     */
    public function close(ProductFeedMetadata $metadata)
    {
        $this->pushWriteBuffer();
        $this->closeHandle();
    }

    /**
     * @return void
     */
    private function closeHandle()
    {
        if ($this->handle != null) {
            fclose($this->handle);
            unset($this->handle);
        }
    }

    /**
     * @param $mode
     * @return void
     */
    private function prepareFilePointer($mode)
    {
        $this->closeHandle();
        $this->handle = fopen($this->uri, $mode);

    }


    /**
     * @param Product $product
     */
    private function writeSharedProduct(AbstractProduct $product, &$row)
    {
        $row[] = $this->getValue($product->getReference());
        $row[] = $this->getValue($product->getQuantity());
        $row[] = $this->getValue($product->getPrice());
        $row[] = $this->getValue($product->getLink());
        $row[] = $this->getValue($product->getGtin());

        /**
         * @var ProductDiscount $discount
         */
        $discount = current($product->getDiscounts());
        if (empty($discount) || !$discount instanceof ProductDiscount) {
            $row[] = self::EMPTY_VALUE;
        } else {
            $row[] = $discount->getValue();
        }
        /**
         * @var ProductShipping $shipping
         */
        $shipping = current($product->getShippings());
        if (empty($shipping)) {
            array_push($row, self::EMPTY_VALUE, self::EMPTY_VALUE);
        } else {
            array_push($row, $this->getValue($shipping->getCost()), $this->getValue($shipping->getDescription()));
        }

        /**
         * @var ProductAttribute[] $attributes
         */
        $attributes = $product->getAttributes();

        foreach ($this->attributeNames as $attributeName) {
            $hasAttribute = false;
            foreach ($attributes as $attribute) {
                if ($attribute->getName() == $attributeName) {
                    $row[] = $attribute->getValue();
                    $hasAttribute = true;
                }
            }

            if (!$hasAttribute) {
                $row[] = self::EMPTY_VALUE;
            }
        }


        $row[] = $this->getValue($product->getMainImage());

        /**
         * @var string[] $additionalImages
         */
        $additionalImages = $product->getAdditionalImages();
        $additionalImagesCount = count($product->getAdditionalImages());
        $slotEmpty = $this->maxAdditionalImagesQuantity - $additionalImagesCount;
        foreach ($additionalImages as $additionalImage) {
            $row[] = $additionalImage;
        }
        for ($i = 0; $i < $slotEmpty; $i++) {
            $row[] = self::EMPTY_VALUE;
        }
    }

    /**
     * @param $element
     * @return string
     */
    private function getValue($element)
    {
        return empty($element) ? self::EMPTY_VALUE : $element;
    }

    /**
     * @return void
     */
    private function pushWriteBuffer()
    {
        if (count($this->writeBuffer) > 0) {
            fwrite($this->handle, \ShoppingFeed\Feed\arrayToCSV($this->writeBuffer));
            $this->writeBuffer = [];
            $this->writeCount = 0;
        }

    }

    /**
     * @param $uri
     * @return void
     */
    public function generateTempArrayFileWriter()
    {
        $this->tempWriter = new TempArrayFileWriter();
    }


    /**
     * @return TempArrayFileWriter|null
     */
    public function getTempArrayFileWriter()
    {
        return $this->tempWriter;
    }

    public function writeIntoTemp($item)
    {
        if (empty($this->tempWriter)) {
            return ;
        }
        $this->tempWriter->write($item);
    }

    public function closeTempWriter()
    {
        if (empty($this->tempWriter)) {
            return ;
        }
        $this->tempWriter->close();
    }

    public function getTempFilePath()
    {
        if (empty($this->tempWriter)) {
            return '';
        }
        
        return $this->tempWriter->getTempFilePath();
    }


}