<?php

define('BASE_DIR', 'galleries/');

require_once __DIR__.'/vendor/autoload.php'; 

$app = new Silex\Application();

$app['debug'] = true;

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'   => 'pdo_sqlite',
        'path'     => __DIR__.'/app.db',
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

$app->run();

