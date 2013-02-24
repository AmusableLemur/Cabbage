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

    private function getImageKey($hash)
    {
        foreach ($this->images as $key => $image) {
            if ($image->getHash() === $hash) {
                return $key;
            }
        }
    }

    public function getImages()
    {
        return $this->images;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the image after the one specified
     * @param  string $hash
     * @return Image
     */
    public function getNextImage($hash)
    {
        return $this->images[$this->getImageKey($hash) + 1];
    }

    /**
     * Returns the image before the one specified
     * @param  string $hash
     * @return Image
     */
    public function getPreviousImage($hash)
    {
        return $this->images[$this->getImageKey($hash) - 1];
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}

