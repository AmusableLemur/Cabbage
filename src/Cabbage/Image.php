<?php

namespace Cabbage;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;

class Image
{
    private $file;
    private $hash;
    private $path;

    public function __construct($file, $hash = null)
    {
        $this->hash = $hash === null ? sha1(file_get_contents($file)) : $hash;
        $this->file = explode(DIRECTORY_SEPARATOR, $file);
        $this->file = array_pop($this->file);
        $this->path = $file;
    }

    public function generateThumbnail($path)
    {
        $imagine = new Imagine();
        $imagine
            ->open($this->path)
            ->thumbnail(new Box(260, 250), ImageInterface::THUMBNAIL_OUTBOUND)
            ->save($path.$this->hash.'.jpg');
    }

    public function getFileName()
    {
        return $this->file;
    }

    public function getHash()
    {
        return $this->hash;
    }
}

