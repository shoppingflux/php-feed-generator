<?php
namespace ShoppingFeed\Feed\Xml;

use ShoppingFeed\Feed\ProductFeedMetadata;
use ShoppingFeed\Feed\Product\Product;
use ShoppingFeed\Feed;

class XmlProductFeedWriter implements Feed\ProductFeedWriterInterface
{
    const VERSION = '1.0.0';

    /**
     * @var \XMLWriter
     */
    private $writer;

    /**
     * @param string $destination
     */
    public function open($destination)
    {
        $this->writer = new \XMLWriter();
        $this->writer->openUri($destination);
        $this->writer->setIndent(true);

        $this->writer->startDocument('1.0', 'utf-8');
        $this->writer->startElement('catalog');
        $this->writer->startElement('products');
    }

    /**
     * @param ProductFeedMetadata $metadata
     */
    public function close(ProductFeedMetadata $metadata)
    {
        $writer = $this->writer;
        $writer->endElement(); // products

        $writer->startElement('metadata');
        $writer->writeElement('platform', $metadata->getPlatform());
        $writer->writeElement('agent', $metadata->getAgent());
        $writer->writeElement('startedAt', $metadata->getStartedAt()->format('c'));
        $writer->writeElement('finishedAt', $metadata->getFinishedAt()->format('c'));
        $writer->writeElement('invalid', $metadata->getInvalidCount());
        $writer->writeElement('ignored', $metadata->getFilteredCount());
        $writer->writeElement('written', $metadata->getWrittenCount());
        $writer->endElement();

        $writer->endElement(); // catalog
        $writer->flush();

        // unlink object reference
        unset($this->writer);
    }

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->writer->writeAttribute('version', self::VERSION);
        foreach ($attributes as $name => $value) {
            $this->writer->writeAttribute($name, $value);
        }
    }

    /**
     * @param Product $product
     */
    public function writeProduct(Product $product)
    {
        $writer = $this->writer;
        $writer->startElement('product');

        $this->writeElement('name', $product->getName());
        $this->writeSharedProduct($product);

        if ($ecotax = $product->getEcotax()) {
            $this->writeElement('ecotax', $ecotax);
        }

        if ($vat = $product->getVat()) {
            $this->writeElement('vat', $vat);
        }

        if ($weight = $product->getWeight()) {
            $this->writeElement('weight', $weight);
        }

        if ($product->hasDescription()) {
            $description = $product->getDescription();
            $writer->startElement('description');
            $this->writeCdataElement('full', $description->getFull());
            if ($short = $description->getShort()) {
                $this->writeCdataElement('short', $short);
            }
            $writer->endElement();
        }

        if ($product->hasBrand()) {
            $brand = $product->getBrand();
            $writer->startElement('brand');
            $this->writeElement('name', $brand->getName());
            $this->writeCdataElement('link', $brand->getLink());
            $writer->endElement();
        }

        if ($product->hasCategory()) {
            $category = $product->getCategory();
            $writer->startElement('category');
            $this->writeElement('name', $category->getName());
            $this->writeCdataElement('link', $category->getLink());
            $writer->endElement();
        }

        if ($product->hasVariations()) {
            $writer->startElement('variations');
            foreach ($product->getVariations() as $variation) {
                $writer->startElement('variation');
                $this->writeSharedProduct($variation);
                $writer->endElement();
            }
            $writer->endElement();
        }

        $writer->endElement();
        $writer->flush();
    }

    private function writeSharedProduct(Feed\Product\AbstractProduct $product)
    {
        $writer = $this->writer;

        $this->writeElement('reference', $product->getReference());
        $this->writeElement('quantity', $product->getQuantity());
        $this->writeElement('price', $product->getPrice());
        if ($link = $product->getLink()) {
            $this->writeCdataElement('link', $link);
        }
        if ($product->hasGtin()) {
            $this->writeElement('gtin', $product->getGtin());
        }
        if ($product->hasDiscounts()) {
            $writer->startElement('discounts');
            foreach ($product->getDiscounts() as $discount) {
                $writer->startElement('discount');
                $writer->writeAttribute('type', $discount->getType());
                $writer->writeRaw($discount->getValue());
                $writer->endElement();
            }
            $writer->endElement();
        }

        if ($product->hasShippings()) {
            $writer->startElement('shippings');
            foreach ($product->getShippings() as $shipping) {
                $writer->startElement('shipping');
                $writer->writeElement('amount', $shipping->getCost());
                if ($description = $shipping->getDescription()) {
                    $writer->writeElement('label', $shipping->getDescription());
                }
                $writer->endElement(); // shipping
            }
            $writer->endElement();
        }

        if ($product->hasAttributes()) {
            $writer->startElement('attributes');
            foreach ($product->getAttributes() as $attribute) {
                $writer->startElement('attribute');
                $this->writeElement('name', $attribute->getName());
                $this->writeCdataElement('value', $attribute->getValue());
                $writer->endElement();
            }
            $writer->endElement();
        }

        if ($product->hasImages()) {
            $writer->startElement('images');
            if ($main = $product->getMainImage()) {
                $writer->startElement('image');
                $writer->writeAttribute('type', 'main');
                $this->writeCdata($main);
                $writer->endElement();
            }
            foreach ($product->getAdditionalImages() as $image) {
                $this->writeCdataElement('image', $image);
            }
            $writer->endElement();
        }
    }

    /**
     * Prevent nested Cdata be escaping all ']]>' strings into the content.
     * The replacement can be decomposed:
     * keep first part ]]
     * then close current cdata and open a new one
     * write last part >
     *
     * @param string $content
     */
    private function writeCdata($content)
    {
        $this->writer->writeCdata(Feed\xml_utf8_clean($content));
    }

    /**
     * @param string $name
     * @param string $content
     */
    private function writeCdataElement($name, $content)
    {
        $this->writer->startElement($name);
        $this->writeCdata($content);
        $this->writer->endElement();
    }

    /**
     * Force empty strings to be written as self closing tags.
     * Example: writeElement('foo', '') will originally write <foo></foo>
     *          Now it writes <foo/>
     *
     * @param string $name
     * @param string $content
     *
     * @return bool
     */
    private function writeElement($name, $content)
    {
        if ('' === $content) {
            $content = null;
        }
        if (null !== $content) {
            $content = Feed\xml_utf8_clean($content);
        }

        return $this->writer->writeElement(trim($name), trim($content));
    }
}
