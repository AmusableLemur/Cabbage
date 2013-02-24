<?php

namespace Cabbage;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

class Image
{
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

    public function getFilePath()
    {
        return $this->filepath;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function getMimeType()
    {
        $finfo = new \finfo(FILEINFO_MIME);

        return $finfo->file($this->filepath);
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
}

