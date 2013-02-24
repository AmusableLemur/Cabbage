<?php

define('DB_PATH', __DIR__.'/app.db');
define('ARCHIVES_PATH', 'archives/');
define('GALLERIES_PATH', 'galleries/');
define('THUMBNAIL_PATH', 'thumbnails/');

require_once __DIR__.'/vendor/autoload.php';

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

$app = new Application();

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

/**
 * Redirects to real search
 */
$app->post('/search', function(Application $app, Request $request) {
    $terms = $request->get('s');
    $terms = trim(strtolower($terms));

    if (strlen($terms) < 1) {
        return $app->redirect($request->getBaseUrl());
    }
    else {
        return $app->redirect($request->getBaseUrl().'/search/'.str_replace(' ', '+', $terms));
    }
});

/**
 * Search feature
 */
$app->get('/search/{terms}', 'Cabbage\Controller::search');

$app->run();

