<?php
namespace ShoppingFeed\Feed;

class ProductFeedResult
{
    /**
     * @var \DateTimeInterface
     */
    private $startedAt;

    /**
     * @var \DateTimeInterface
     */
    private $finishedAt;

    /**
     * @var \SplFileInfo
     */
    private $info;

    /**
     * @param \DateTimeInterface $startedAt
     * @param \DateTimeInterface $finishedAt
     * @param \SplFileInfo       $info
     */
    public function __construct(\DateTimeInterface $startedAt, \DateTimeInterface $finishedAt, \SplFileInfo $info)
    {
        $this->info       = $info;
        $this->startedAt  = $startedAt;
        $this->finishedAt = $finishedAt;
    }

    /**
     * Return the duration, expressed in seconds
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->finishedAt->getTimestamp() - $this->startedAt->getTimestamp();
    }

    /**
     * @return \SplFileInfo
     */
    public function getFileInfo()
    {
        return $this->info;
    }
}
