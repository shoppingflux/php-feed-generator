<?php
namespace ShoppingFeed\Feed;

use ShoppingFeed\Feed\Product\Product;

interface ProductFeedWriterInterface
{
    /**
     * @param string $uri
     *
     * @return void
     */
    public function open($uri);

    /**
     * @param array $attributes
     *
     * @return void
     */
    public function setAttributes(array $attributes);

    /**
     * @param Product $product
     *
     * @return void
     */
    public function writeProduct(Product $product);

    /**
     * @param ProductFeedMetadata $metadata
     *
     * @return void
     */
    public function close(ProductFeedMetadata $metadata);
}
