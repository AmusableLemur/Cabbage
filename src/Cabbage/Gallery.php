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

    public function getIdent()
    {
        return strtolower(str_replace(' ', '_', $this->name));
    }

    public function getImage($hash)
    {
        foreach ($this->images as $image) {
            if ($image->getHash() === $hash) {
                return $image;
            }
        }

        return null;
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

