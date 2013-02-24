<?php

namespace Cabbage;

class Gallery
{
    private $name;
    private $images = array();

    public function addImage(Image $image)
    {
        $this->images[] = $image;
    }

    public function addImages(array $images)
    {
        $this->images += $images;
    }

    public function getCoverImage()
    {
        return reset($this->images);
    }

    public function getHash()
    {
        return sha1($this->name);
    }

    public function getImages()
    {
        return $this->images;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}

