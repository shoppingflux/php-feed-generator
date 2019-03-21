<?php
namespace ShoppingFeed\Feed;


interface RigidFormatWriterInterface
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

    /**
     * @return void
     */
    public function generateTempArrayFileWriter();

    /**
     * @return TempArrayFileWriter
     */
    public function getTempArrayFileWriter();

    /**
     * @param $item
     * @return void
     */
    public function writeIntoTemp($item);

    /**
     * @return void
     */
    public function closeTempWriter();

    /**
     * @return string
     */
    public function getTempFilePath();
}
