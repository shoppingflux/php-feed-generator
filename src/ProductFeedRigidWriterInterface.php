<?php

namespace ShoppingFeed\Feed;


interface ProductFeedRigidWriterInterface
{
    /**
     * @param string $uri
     *
     * @return void
     */
    public function formatRigid();

    /**
     * @param $name
     * @return void
     */
    public function addAttributeName($name);

    /**
     * @param $quantity
     * @return void
     */
    public function setMaxAdditionalImagesQuantity($quantity);


    /**
     * @return int
     */
    public function getMaxAdditionalImagesQuantity();

    /**
     * @return void
     */
    public function writeHeader();

}
