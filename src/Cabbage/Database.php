<?php

namespace Cabbage;

use Silex\Application;

class Database
{
    private $db;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];

        $this->createStructure();
    }

    private function createStructure()
    {
        $this->db->executeUpdate(
            "CREATE TABLE IF NOT EXISTS galleries (
                galleryId INTEGER PRIMARY KEY,
                name TEXT,
                nameHash TEXT
            );

            CREATE TABLE IF NOT EXISTS images (
                imageId INTEGER PRIMARY KEY,
                galleryId INTEGER,
                imageHash TEXT,
                filename TEXT
            );"
        );
    }

    public function getGalleries()
    {
        $galleries = array();
        $result = $this->db->fetchAll("SELECT galleryId, name FROM galleries");

        foreach ($result as $row) {
            $gallery = new Gallery();
            $gallery->setName($row['name']);

            $images = $this->db->fetchAll(
                "SELECT
                    imageHash AS hash,
                    filename
                FROM images
                WHERE galleryID = ?",
                array($row['galleryId'])
            );

            foreach ($images as $image) {
                $gallery->addImage(new Image($image['filename'], $image['hash']));
            }

            $galleries[] = $gallery;
        }

        return $galleries;
    }

    public function storeGallery(Gallery $gallery)
    {
        $this->db->executeUpdate(
            "INSERT INTO galleries (
                name,
                nameHash
            ) VALUES (
                :name,
                :hash
            )",
            array(
                'name' => $gallery->getName(),
                'hash' => $gallery->getHash()
            )
        );

        $this->db->beginTransaction();

        foreach ($gallery->getImages() as $image) {
            $this->db->executeUpdate(
                "INSERT INTO images (
                    galleryId,
                    imageHash,
                    filename
                ) VALUES (
                    :gallery,
                    :hash,
                    :file
                )",
                array(
                    'gallery' => $this->db->lastInsertId(),
                    'hash' => $image->getHash(),
                    'file' => $image->getFileName()
                )
            );
        }

        $this->db->commit();
    }
}

