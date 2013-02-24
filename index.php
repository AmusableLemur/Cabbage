<?php

define('DB_PATH', __DIR__.'/app.db');
define('ARCHIVES_PATH', 'archives/');
define('GALLERIES_PATH', 'galleries/');
define('THUMBNAIL_PATH', 'thumbnails/');

require_once __DIR__.'/vendor/autoload.php'; 

$app = new Silex\Application();

$app['debug'] = true;

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_sqlite',
        'path'     => DB_PATH,
    ),
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

/**
 * Main view, renders a list of all galleries
 */
$app->get('/', 'Cabbage\Controller::overview');

/**
 * Shows an image from a gallery
 */
$app->get('/view/{gallery}/{hash}', 'Cabbage\Controller::view');

/**
 * Downloads a zipped gallery
 */
$app->get('/download/{gallery}.zip', 'Cabbage\Controller::download');

/**
 * Loads an image
 */
$app->get('/image/{hash}', 'Cabbage\Controller::image');

/**
 * Loads a thumbnail
 */
$app->get('/thumbnail/{hash}', 'Cabbage\Controller::thumbnail');

/**
 * Refreshes the database
 */
$app->get('/refresh', 'Cabbage\Controller::refresh');

$app->run();

