<?php

namespace Cabbage;

class Image
{
    private $file;
    private $hash;

    public function __construct($file, $hash = null)
    {
        $this->hash = $hash === null ? sha1(file_get_contents($file)) : $hash;
        $this->file = explode(DIRECTORY_SEPARATOR, $file);
        $this->file = array_pop($this->file);
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

