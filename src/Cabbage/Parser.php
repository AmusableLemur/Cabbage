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
        return array_map(
            function ($name) {
                if (!$this->isDirectory($name)) {
                    return null;
                }

                $directory = dir($this->path.$name);
                $image = null;

                while (($file = $directory->read()) !== false) {
                    if ($this->isImage($file)) {
                        $image = $file;

                        break;
                    }
                }

                if ($image === null) {
                    return null;
                }

                $gallery = array();
                $gallery['name'] = $name;
                $gallery['image'] = $image;

                return $gallery;
            },
            scandir($this->path)
        );
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
}

