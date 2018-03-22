<?php
namespace ShoppingFeed\Feed;

use ShoppingFeed\Feed\Product\Product;
use ShoppingFeed\Feed\Xml;

class ProductFeed
{
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
     * @var string
     */
    private $writer = 'xml';

    /**
     * @var array
     */
    private $writers = [
        'xml' => Xml\XmlProductFeedWriter::class
    ];

    /**
     * @param string $uri
     */
    public function __construct($uri = 'php://output')
    {
        $this->setUri($uri);
        $this->metadata = new ProductFeedMetadata();
    }

    /**
     * @param string $uri
     *
     * @return ProductFeed
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

        $prototype = new Product();
        foreach ($iterable as $item) {
            foreach ($this->processors as $processor) {
                $item = $processor($item);
            }

            foreach ($this->filters as $processor) {
                if (false === $processor($item)) {
                    $metadata->incrFiltered();
                    continue 2;
                }
            }

            $product = clone $prototype;
            foreach ($this->mappers as $hydrator) {
                $hydrator($item, $product);
            }

            $writer->writeProduct($product);
            $metadata->incrWritten();
        }

        $metadata->setFinishedAt(new \DateTimeImmutable());
        $writer->close($metadata);

        return new ProductFeedResult(
            $metadata->getStartedAt(),
            $metadata->getFinishedAt(),
            new \SplFileInfo($this->uri)
        );
    }

    /**
     * @return ProductFeedWriterInterface
     */
    private function createWriter()
    {
        return new $this->writers[$this->writer]();
    }
}
