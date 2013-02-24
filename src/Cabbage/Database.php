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
                id INTEGER PRIMARY KEY,
                name TEXT,
                hash TEXT
            );

            CREATE TABLE IF NOT EXISTS images (
                id INTEGER PRIMARY KEY,
                galleryId INTEGER,
                hash TEXT,
                filepath TEXT
            );"
        );
    }

    public function getGalleries()
    {
        $galleries = array();
        $result = $this->db->fetchAll("SELECT hash FROM galleries");

        foreach ($result as $row) {
            $galleries[] = $this->getGallery($row['hash']);
        }

        return $galleries;
    }

    public function getGallery($hash)
    {
        $result = $this->db->fetchAssoc(
            "SELECT
                id,
                name
            FROM galleries
            WHERE hash = ?",
            array($hash)
        );

        $gallery = new Gallery();

        $gallery->setName($result['name']);

        $images = $this->db->fetchAll(
            "SELECT
                hash,
                filepath
            FROM images
            WHERE galleryId = ?",
            array($result['id'])
        );

        foreach ($images as $image) {
            $gallery->addImage(new Image($image['filepath'], $image['hash']));
        }

        return $gallery;
    }

    public function getImage($hash)
    {
        $filepath = $this->db->fetchColumn(
            "SELECT filepath
            FROM images
            WHERE hash = ?",
            array($hash)
        );

        return new Image($filepath, $hash);
    }

    public function storeGallery(Gallery $gallery)
    {
        $this->db->executeUpdate(
            "INSERT INTO galleries (
                name,
                hash
            ) VALUES (
                :name,
                :hash
            )",
            array(
                'name' => $gallery->getName(),
                'hash' => $gallery->getHash()
            )
        );

        $galleryId = $this->db->lastInsertId();

        $this->db->beginTransaction();

        foreach ($gallery->getImages() as $image) {
            $this->db->executeUpdate(
                "INSERT INTO images (
                    galleryId,
                    hash,
                    filepath
                ) VALUES (
                    :gallery,
                    :hash,
                    :file
                )",
                array(
                    'gallery' => $galleryId,
                    'hash' => $image->getHash(),
                    'file' => $image->getFilePath()
                )
            );
        }

        $this->db->commit();
    }
}

