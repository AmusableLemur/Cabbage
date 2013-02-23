<?php

require_once __DIR__.'/vendor/autoload.php'; 

$app = new Silex\Application();

$app['debug'] = true;

$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver'    => 'pdo_mysql',
        'host'      => 'localhost',
        'dbname'    => 'cabbage',
        'user'      => 'root',
        'password'  => '',
        'charset'   => 'utf8'
    )
));

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

/**
 * Main view, renders a list of all galleries
 */
$app->get('/', function() use ($app) {
    return $app['twig']->render('overview.html.twig', array(
        'name' => 'Blarg'
    ));
});

/**
 * POST-request to create a new gallery, redirects to new gallery
 */
$app->post('/', function() use ($app) {
    return $app['twig']->render('overview.html.twig', array(
        'messages' => 'Gallery created'
    ));
});

/**
 * Shows a single image
 */
$app->get('/view/{name}', function($name) use($app) {
    return $app['twig']->render('view.html.twig', array(
        'name' => $name
    ));
});

$app->post('/view/{name}', function() use ($app) {
    return $app['twig']->render('overview.html.twig', array(
        'messages' => 'Image uploaded'
    ));
});

$app->run();

