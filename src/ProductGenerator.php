<?php
namespace ShoppingFeed\Feed;

use ShoppingFeed\Feed\Product;
use ShoppingFeed\Feed\Xml;

class ProductGenerator
{
    const VALIDATE_NONE      = 0;
    const VALIDATE_EXCLUDE   = 1;
    const VALIDATE_EXCEPTION = 2;

    /**
     * File destination for the output feed
     *
     * @var string
     */
    private $uri;

    /**
     * Extra Attributes for the generated feed
     *
     * @var array
     */
    private $attributes = [];

    /**
     * @var callable[]
     */
    private $processors = [];

    /**
     * @var callable[]
     */
    private $filters = [];

    /**
     * @var callable[]
     */
    private $mappers = [];

    /**
     * @var ProductFeedMetadata
     */
    private $metadata;

    /**
     * @var bool
     */
    private $validate;

    /**
     * @var string
     */
    private $writer = 'xml';

    /**
     * @var array
     */
    private static $writers = [
        'xml' => Xml\XmlProductFeedWriter::class
    ];

    /**
     * @param string $uri
     */
    public function __construct($uri = 'php://output')
    {
        $this->setUri($uri);
        $this->metadata = new ProductFeedMetadata();
        $this->validate = self::VALIDATE_NONE;
    }

    /**
     * @param string $uri
     *
     * @return ProductGenerator
     */
    public function setUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * @param string $name  The attribute name
     * @param mixed  $value The attribute value
     *
     * @return $this
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * Determine if every product must be validated.
     * Possible values are:
     * 
     * - self::VALIDATE_NONE      : No validation at all, invalid products may be written to the feed
     * - self::VALIDATE_EXCLUDE   : Validated and excluded from the final result if invalid. No error reported
     * - self::VALIDATE_EXCEPTION : An exception is thrown when the first invalid product is met
     *
     * @param int $flags
     *
     * @return ProductGenerator
     */
    public function setValidationFlags($flags)
    {
        $this->validate = (int) $flags;

        return $this;
    }

    /**
     * @param string $platformName
     * @param string $platformVersion
     *
     * @return $this
     */
    public function setPlatform($platformName, $platformVersion)
    {
        $this->metadata->setPlatform($platformName, $platformVersion);

        return $this;
    }

    /**
     * @param callable $callback
     *
     * @return $this
     */
    public function addFilter(callable $callback)
    {
        $this->filters[] = $callback;

        return $this;
    }

    /**
     * @param callable $callback
     *
     * @return $this
     */
    public function addProcessor(callable $callback)
    {
        $this->processors[] = $callback;

        return $this;
    }

    /**
     * @param callable $callback
     *
     * @return $this
     */
    public function addMapper(callable $callback)
    {
        $this->mappers[] = $callback;

        return $this;
    }

    /**
     * @param \Traversable|array $iterable
     *
     * @return ProductFeedResult
     * @throws \Exception
     */
    public function write($iterable)
    {
        if (! $iterable instanceof \Traversable && ! is_array($iterable)) {
            throw new \Exception(sprintf('cannot iterates over %s', gettype($iterable)));
        }

        $metadata = $this->metadata;
        $metadata->setStartedAt(new \DateTimeImmutable());

        $writer = $this->createWriter();
        $writer->open($this->uri);
        $writer->setAttributes($this->attributes);

        $prototype = new Product\Product();
        foreach ($iterable as $item) {
            // Apply processors
            foreach ($this->processors as $processor) {
                $item = $processor($item);
            }

            // Apply filters
            foreach ($this->filters as $processor) {
                if (false === $processor($item)) {
                    $metadata->incrFiltered();
                    continue 2;
                }
            }

            // Apply mappers
            $product = clone $prototype;
            foreach ($this->mappers as $mapper) {
                $mapper($item, $product);
            }

            // The product does not match expected validation rules
            if ($this->validate && false === $product->isValid()) {
                if ($this->validate === self::VALIDATE_EXCEPTION) {
                    throw new Product\InvalidProductException(
                        sprintf('Invalid product found at index %d, aborting', $metadata->getTotalCount())
                    );
                }

                $metadata->incrInvalid();
                continue;
            }

            $writer->writeProduct($product);
            $metadata->incrWritten();
        }

        $metadata->setFinishedAt(new \DateTimeImmutable());
        $writer->close($metadata);

        return new ProductFeedResult(
            $metadata->getStartedAt(),
            $metadata->getFinishedAt()
        );
    }

    /**
     * @return ProductFeedWriterInterface
     */
    private function createWriter()
    {
        $writerClass = self::$writers[$this->writer];

        return new $writerClass();
    }
}
