<?php

namespace Cabbage;

/**
 * Takes care of parsing galleries from the file system.
 */
class Parser
{
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Loads galleries. Index 'name' is the name of the gallery i.e. the folder
     * name. Index 'image' is the cover photo i.e. the first image found in the
     * folder.
     * @return array
     */
    public function getGalleries()
    {
        $galleries = array_map(
            function ($name) {
                if (!$this->isDirectory($name)) {
                    return null;
                }

                $images = $this->loadImagesFromDirectory($this->path.$name);

                if (count($images) < 1) {
                    return null;
                }

                $gallery = new Gallery();

                $gallery->setName($name);
                $gallery->addImages($images);

                return $gallery;
            },
            scandir($this->path)
        );

        return array_filter($galleries);
    }

    /**
     * Makes sure that the file is a folder
     * @param  string  $file Path to the file
     * @return boolean       True if file is a valid folder
     */
    private function isDirectory($file)
    {
        return is_dir($this->path.$file) || !in_array($file, array('.', '..'));
    }

    /**
     * Makes sure that the file is an image
     * @param  string  $file Path to the file
     * @return boolean       True if file appears to be an image
     */
    private function isImage($file)
    {
        $extension = explode('.', $file);
        $extension = array_pop($extension);
        $extension = strtolower($extension);

        return in_array($extension, array('jpg', 'png', 'gif'));
    }

    /**
     * Finds images in a given folder and returns them in an array as instances
     * of Image
     * @param  string $directory The directory to search in
     * @return array             Array of Image
     */
    private function loadImagesFromDirectory($directory)
    {
        $dir = dir($directory);
        $images = array();

        while (($file = $dir->read()) !== false) {
            if ($this->isImage($file)) {
                $images[] = new Image($directory.DIRECTORY_SEPARATOR.$file);
            }
        }

        return $images;
    }
}

