<?php

define('BASE_DIR', 'galleries/');

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
    $galleries = array_filter(scandir(BASE_DIR), function ($name) {
        $directory = BASE_DIR.$name;

        return is_dir($directory) && !in_array($name, array('.', '..'));
    });

    return $app['twig']->render('overview.html.twig', array(
        'galleries' => $galleries
    ));
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

