<?php

namespace Cabbage;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

class Image
{
    private $data;
    private $file;
    private $hash;
    private $filepath;

    public function __construct($filepath, $hash = null)
    {
        $this->hash = $hash === null ? sha1(file_get_contents($filepath)) : $hash;
        $this->file = explode(DIRECTORY_SEPARATOR, $filepath);
        $this->file = array_pop($this->file);
        $this->filepath = $filepath;
    }

    private function generateThumbnail($path)
    {
        $imagine = new Imagine();
        $imagine
            ->open($this->filepath)
            ->thumbnail(new Box(260, 250), ImageInterface::THUMBNAIL_OUTBOUND)
            ->save($path.$this->hash.'.jpg');
    }

    public function getBitDepth()
    {
        return $this->data['bits'];
    }

    public function getChannels()
    {
        if (!isset($this->getData()['channels'])) {
            return null;
        }

        $channels = $this->getData()['channels'];

        switch ($channels) {
            case 3:
                return $channels.' (RGB)';
            case 4:
                return $channels.' (CMYK)';
        }

        return $channels;
    }

    /**
     * Should generate EXIF-data, however EXIF doesn't exist for GIF or PNG
     */
    private function getData()
    {
        if ($this->data === null) {
            $this->data = getimagesize($this->filepath);
        }

        return $this->data;
    }

    public function getFilePath()
    {
        return $this->filepath;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function getHeight()
    {
        return $this->getData()[1];
    }

    public function getMimeType()
    {
        return $this->getData()['mime'];
    }

    public function getRawData()
    {
        return file_get_contents($this->filepath);
    }

    public function getThumbnail()
    {
        $thumbnail = THUMBNAIL_PATH.$this->hash.'.jpg';

        if (!file_exists($thumbnail)) {
            $this->generateThumbnail(THUMBNAIL_PATH);
        }
        
        return file_get_contents($thumbnail);
    }

    public function getWidth()
    {
        return $this->getData()[0];
    }
}

