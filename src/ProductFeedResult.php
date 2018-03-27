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
     * @param \DateTimeInterface $startedAt
     * @param \DateTimeInterface $finishedAt
     */
    public function __construct(\DateTimeInterface $startedAt, \DateTimeInterface $finishedAt)
    {
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
}
