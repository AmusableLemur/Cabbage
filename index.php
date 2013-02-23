<?php

require_once __DIR__.'/vendor/autoload.php'; 

$app = new Silex\Application();

$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

/**
 * Main view, renders a list of all galleries
 */
$app->get('/', function() use ($app) {
    return $app['twig']->render('overview.html.twig');
});

/**
 * Shows a single image
 */
$app->get('/view/{gallery}/{image}', function($gallery, $image) use($app) {
    return $app['twig']->render('view.html.twig', array(
        'image' => $image
    ));
});

$app->run();

