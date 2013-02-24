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
                ident TEXT,
                name TEXT,
                UNIQUE (ident) ON CONFLICT IGNORE
            );

            CREATE TABLE IF NOT EXISTS images (
                id INTEGER PRIMARY KEY,
                galleryId INTEGER,
                hash TEXT,
                filepath TEXT
            );"
        );
    }

    public function findGalleries($terms)
    {
        $terms = explode('+', $terms);
        $qb = $this->db->createQueryBuilder();

        $qb->select('g.ident')->from('galleries', 'g');

        foreach ($terms as $term) {
            $qb->orWhere("g.name LIKE '%".$term."%'");
        }

        $galleries = array();

        foreach ($qb->execute()->fetchAll(\PDO::FETCH_COLUMN) as $ident) {
            $galleries[] = $this->getGallery($ident);
        }

        return $galleries;
    }

    public function getGalleries()
    {
        $galleries = array();
        $result = $this->db->fetchAll("SELECT ident FROM galleries");

        foreach ($result as $row) {
            $galleries[] = $this->getGallery($row['ident']);
        }

        return $galleries;
    }

    public function getGallery($ident)
    {
        $result = $this->db->fetchAssoc(
            "SELECT
                id,
                name
            FROM galleries
            WHERE ident = ?",
            array($ident)
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
                ident,
                name
            ) VALUES (
                :ident,
                :name
            )",
            array(
                'name' => $gallery->getName(),
                'ident' => $gallery->getIdent()
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

