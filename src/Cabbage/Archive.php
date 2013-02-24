<?php

namespace Cabbage;

class Archive
{
    private $gallery;

    public function __construct(Gallery $gallery)
    {
        $this->gallery = $gallery;
    }

    public function __toString()
    {
        $filepath = ARCHIVES_PATH.$this->gallery->getIdent().'.zip';

        if (!file_exists($filepath)) {
            $zip = new \ZipArchive;
            
            $zip->open($filepath, \ZipArchive::CREATE);

            foreach ($this->gallery->getImages() as $image) {
                $zip->addFile($image->getFilePath(), basename($image->getFilePath()));
            }

            $zip->close();
        }

        return file_get_contents($filepath);
    }
}

