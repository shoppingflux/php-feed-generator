<?php
namespace ShoppingFeed\Feed;

class ProductFeedMetadata
{
    /**
     * @var string
     */
    private $platform;

    /**
     * @var string
     */
    private $agent;

    /**
     * @var string
     */
    private $module;

    /**
     * @var \DateTimeInterface
     */
    private $startedAt;

    /**
     * @var \DateTimeInterface
     */
    private $finishedAt;

    /**
     * @var int
     */
    private $filtered;

    /**
     * @var int
     */
    private $invalid;

    /**
     * @var int
     */
    private $written;

    public function __construct()
    {
        $this->setAgent('shopping-feed-generator', '1.0.0');
        $this->setPlatform('Unknown', 'Unknown');
        $this->setModule('Unknown', 'Unknown');
        $this->filtered = 0;
        $this->written  = 0;
        $this->invalid  = 0;
    }

    /**
     * Define the host platform that provide feed data
     *
     * @param string $platformName      The name of the platform. IE: Magento, Prestashop...etc
     * @param string $platformVersion   The platform version. IE: 1.0.2
     *
     * @return $this
     */
    public function setPlatform($platformName, $platformVersion)
    {
        $this->platform = sprintf('%s:%s', $platformName, $platformVersion);

        return $this;
    }

    /**
     * Define the host platform that provide feed data
     *
     * @param string $agentName      The name of the platform. IE: Magento, Prestashop...etc
     * @param string $agentVersion   The platform version. IE: 1.0.2
     *
     * @return $this
     */
    private function setAgent($agentName, $agentVersion)
    {
        $this->agent = sprintf('%s:%s', $agentName, $agentVersion);

        return $this;
    }

    /**
     * @return string
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @return string
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * @deprecated No longer used. Will be dropped in a future version.
     *
     * @return \DateTimeInterface
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * @deprecated No longer used. Will be dropped in a future version.
     *
     * @param \DateTimeInterface $startedAt
     */
    public function setStartedAt(\DateTimeInterface $startedAt)
    {
        $this->startedAt = $startedAt;
    }

    /**
     * @deprecated No longer used. Will be dropped in a future version.
     *
     * @return \DateTimeInterface
     */
    public function getFinishedAt()
    {
        return $this->finishedAt;
    }

    /**
     * @deprecated No longer used. Will be dropped in a future version.
     *
     * @param \DateTimeInterface $finishedAt
     */
    public function setFinishedAt(\DateTimeInterface $finishedAt)
    {
        $this->finishedAt = $finishedAt;
    }

    public function incrFiltered()
    {
        $this->filtered += 1;
    }

    public function incrInvalid()
    {
        $this->invalid += 1;
    }

    public function incrWritten()
    {
        $this->written += 1;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->invalid + $this->filtered + $this->written;
    }

    /**
     * @deprecated No longer used. Will be dropped in a future version.
     *
     * @return int
     */
    public function getFilteredCount()
    {
        return $this->filtered;
    }

    /**
     * @deprecated No longer used. Will be dropped in a future version.
     *
     * @return int
     */
    public function getWrittenCount()
    {
        return $this->written;
    }

    /**
     * @deprecated No longer used. Will be dropped in a future version.
     *
     * @return int
     */
    public function getInvalidCount()
    {
        return $this->invalid;
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param string $name
     * @param string $version
     *
     * @return self
     */
    public function setModule($name, $version)
    {
        $this->module = sprintf('%s:%s', $name, $version);

        return $this;
    }
}
