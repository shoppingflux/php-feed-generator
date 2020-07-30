<?php
namespace ShoppingFeed\Feed\Json;

use ShoppingFeed\Feed\ProductFeedMetadata;
use ShoppingFeed\Feed\Product\Product;
use ShoppingFeed\Feed;

class JsonProductFeedWriter implements Feed\ProductFeedWriterInterface
{
    /**
     * Store output destination path
     * 
     * @var string $outPutPath
     */
    private $outPutPath;

    /**
     * In case of output file: check if the output file already exists
     * If output file exists then delete it
     * 
     * @param string $destination
     */
    public function open($destination)
    {
        $this->outPutPath = $destination;
        if(file_exists($destination)){
            unlink($destination);
        }
    }
    /**
     * @inheritdoc
     */
    public function setAttributes(array $attributes)
    {
        // Not used for JSON format
    }

    /**
     * @param Product $product
     */
    public function writeProduct(Product $product)
    {
        $data = $this->extractProduct($product);
        $this->write($data);
    }

    public function close(ProductFeedMetadata $metadata)
    {
        // Not used for JSON format
    }

   /**
     * Product extraction
     * Returns name, reference, quantity and price of each prodcut
     *
     * @param Feed\Product\AbstractProduct $product
     *
     * @return array
     */
    private function extractProduct(Feed\Product\AbstractProduct $product)
    {
        $data = [
            'name' => $product->getName(),
            'reference' => $product->getReference(),
            'quantity'  => $product->getQuantity(),
            'price'     => floatval($product->getPrice()),
        ];
        
        return $data;
    }

    /**
     * Fill output destination
     * 
     * @param array $elements
     */
    private function write(array $elements)
    {
        file_put_contents($this->outPutPath, json_encode($elements).PHP_EOL, FILE_APPEND);
    }

}