<?php
namespace ShoppingFeed\Feed;

use ShoppingFeed\Feed\Product\Product;

interface ProductFeedWriterInterface
{
    /**
     * Given the $uri location, this method allows you to make the necessary operations
     * that will allow you to write to this location.
     * This could mean, for example, creating a file and opening it for writing.
     *
     * @param string $uri Location of where the feed will be written.
     *
     * @return void
     */
    public function open($uri);

    /**
     * This is the equivalent of defining metadata for a file.
     * It allows users to define optional information, but information that might be valuable nonetheless.
     *
     * Examples could include : name or URL of the store for which we are generating the feed
     *
     * If the generated format of the feed has a metadata / comment section,
     * we can inject this information there.
     *
     * @param array $attributes
     *
     * @return void
     */
    public function setAttributes(array $attributes);

    /**
     * The writer will loop through all the given products and, after having transformed
     * the product using the different processors, filters and mappers,
     * it will attempt to write it using this method.
     * This could mean, for example, actually writing a line that contains the product,
     * in the previously opened file.
     *
     * @param Product $product
     *
     * @return void
     */
    public function writeProduct(Product $product);

    /**
     * Do any necessary operations to finish the writing process.
     * - The $metadata gives you access to important information which we can use
     * to either generate a report or include some of the information as metadata, in the file.
     * - You can close the file that was previously opened, for example
     *
     * @param ProductFeedMetadata $metadata
     *
     * @return void
     */
    public function close(ProductFeedMetadata $metadata);
}
