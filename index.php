<?php

require_once __DIR__.'/vendor/autoload.php'; 

$app = new Silex\Application();

$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->get('/', function() use ($app) {
    return $app['twig']->render('overview.html.twig', array(
        'name' => 'Blarg'
    ));
});

$app->get('/view/{name}', function($name) use($app) {
    return $app['twig']->render('view.html.twig', array(
        'name' => $name
    ));
});

$app->run();

