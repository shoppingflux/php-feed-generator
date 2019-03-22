<?php
namespace ShoppingFeed\Feed\Csv;

use ShoppingFeed\Feed;
use ShoppingFeed\Feed\Product\Product;
use ShoppingFeed\Feed\ProductFeedMetadata;

class CsvProductFeedWriter implements Feed\ProductFeedWriterInterface
{
    /**
     * The maximum amount of memory (in bytes, default is 2 MB) for the temporary file to use.
     * If the temporary file exceeds this size, it will be moved to a file in the system's temp directory.
     *
     * @var int
     */
    private static $maxMemoryUsage = 2000000;

    /**
     * Final file for content
     *
     * @var \SplFileObject
     */
    private $dataFile;

    /**
     * This file is used to temporary store serialized data for each line.
     *
     * @var \SplTempFileObject
     */
    private $tempFile;

    /**
     * Store header line items
     *
     * @var array
     */
    private $headers = [];

    /**
     * @param int $bytes The memory limit for temporary data, expressed in bytes
     */
    public static function setDefaultMaxMemoryUsage($bytes)
    {
        self::$maxMemoryUsage = (int) $bytes;
    }

    /**
     * @inheritdoc
     */
    public function open($uri)
    {
        $this->tempFile = new \SplTempFileObject(self::$maxMemoryUsage);
        $this->dataFile = new \SplFileObject($uri, 'wb+');
    }

    /**
     * @inheritdoc
     */
    public function setAttributes(array $attributes)
    {
        // CSV format does not supports metadata
    }

    /**
     * Writes elements to destination file.
     * We fill each product line with empty string in case where column does not exists.
     *
     * @inheritdoc
     */
    public function close(ProductFeedMetadata $metadata)
    {
        $this->tempFile->rewind();
        $this->dataFile->rewind();

        $this->dataFile->fputcsv($this->headers);

        foreach ($this->tempFile as $current) {
            if (! $current = unserialize($current)) {
                continue;
            }

            $csvLine = [];
            foreach ($this->headers as $header) {
                $csvLine[] = isset($current[$header]) ? $current[$header] : '';
            }

            $this->dataFile->fputcsv($csvLine);
        }

        $this->tempFile = null;
        $this->dataFile = null;
    }

    /**
     * @inheritdoc
     */
    public function writeProduct(Product $product)
    {
        $data         = $this->extractProduct($product);
        $data['name'] = $product->getName();

        if ($category = $product->getCategory()) {
            $data['category_name'] = $category->getName();
            $data['category_link'] = $category->getLink();
        }

        if ($description = $product->getDescription()) {
            $data['description_short'] = $description->getShort();
            $data['description_full']  = $description->getFull();
        }

        if ($brand = $product->getBrand()) {
            $data['brand_name'] = $brand->getName();
            $data['brand_link'] = $brand->getLink();
        }

        $this->write($data);

        foreach ($product->getVariations() as $variation) {
            $data                      = $this->extractProduct($variation);
            $data['product_reference'] = $product->getReference();
            $this->write($data);
        }
    }

    /**
     * Base product extraction for both parents and variations
     *
     * @param Feed\Product\AbstractProduct $product
     *
     * @return array
     */
    private function extractProduct(Feed\Product\AbstractProduct $product)
    {
        $data = [
            'reference' => $product->getReference(),
            'quantity'  => $product->getQuantity(),
            'link'      => $product->getLink(),
            'gtin'      => $product->getGtin(),
        ];

        foreach ($product->getDiscounts() as $discount) {
            $data['discount_type']  = $discount->getType();
            $data['discount_value'] = $discount->getValue();
            break;
        }

        foreach ($product->getShippings() as $shipping) {
            $data['shipping_description'] = $shipping->getDescription();
            $data['shipping_cost']        = $shipping->getCost();
            break;
        }

        if ($image = $product->getMainImage()) {
            $data['image_main'] = $image;
        }

        $imgCounter = 0;
        foreach ($product->getAdditionalImages() as $image) {
            $data['image_additional_' . ++$imgCounter] = $image;
        }

        foreach ($product->getAttributes() as $attribute) {
            $data['attribute_' . $attribute->getName()] = $attribute->getValue();
        }

        return $data;
    }

    /**
     * This storage supports csv with headers. We do not know in advance headers, because they are dynamically
     * created based on product list. so we add new header item if meet, and keep them sorted based on alphabetical
     * order. The serialized product is also sorted by key, in order to match row completion based on header line.
     *
     * We don't store data into memory, because very large collection processing can require more than memory
     * allowed by the PHP process.
     *
     * @param array $elements
     */
    private function write(array $elements)
    {
        ksort($elements, SORT_NATURAL);

        $diff = array_diff(array_keys($elements), $this->headers);
        foreach ($diff as $header) {
            $this->headers[] = $header;
        }

        if ($diff) {
            sort($this->headers, SORT_NATURAL);
        }

        $this->tempFile->fwrite(serialize($elements) . PHP_EOL);
    }
}
