<?php

namespace ShoppingFeed\Feed;


class TempArrayFileWriter
{
    const TEMP_FILEPATH = "temp.txt";
    private $handle = null;

    /**
     * CsvTempFileWriter constructor.
     */
    public function __construct()
    {
        $this->open(self::TEMP_FILEPATH);
    }


    private function open($uri)
    {
        if ($this->handle != null) {
            fclose($this->handle);
        }
        $this->handle = fopen($uri, "w");
    }

    public function close()
    {
        fclose($this->handle);
        $this->removeTempFile();
    }

    private function removeTempFile()
    {
        unlink(self::TEMP_FILEPATH);
    }

    public function write($item)
    {
        if (!is_array($item)) {
            return;
        }
        \ShoppingFeed\Feed\utf8_converter($item);
        fputs($this->handle, json_encode($item) . "\n");
    }
    
    public function getTempFilePath() 
    {
        return self::TEMP_FILEPATH;
    }
}