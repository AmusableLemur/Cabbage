<?php

define('DB_PATH', __DIR__.'/app.db');
define('GALLERIES_PATH', 'galleries/');

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
 * Shows a single image
 */
$app->get('/view/{gallery}/{image}', 'Cabbage\Controller::view');

/**
 * Refreshes the database
 */
$app->get('/refresh', 'Cabbage\Controller::refresh');

$app->run();

