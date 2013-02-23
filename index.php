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
    $galleries = array_map(
        function ($name) {
            $directory = BASE_DIR.$name;

            // Make sure we are actually dealing with a folder
            if (!is_dir($directory) || in_array($name, array('.', '..'))) {
                return null;
            }

            $directory = dir($directory);
            $image = null;

            // Load all files in the folder one by one, store it and break the
            // loop if it's an image.
            while (($file = $directory->read()) !== false) {
                $extension = explode('.', $file);
                $extension = array_pop($extension);
                $extension = strtolower($extension);

                if (in_array($extension, array('jpg', 'png', 'gif'))) {
                    $image = $file;

                    break;
                }
            }

            // If no image was found the folder is not a gallery
            if ($image === null) {
                return null;
            }

            $gallery = array();
            $gallery['name'] = $name;
            $gallery['image'] = $image;

            return $gallery;
        },
        scandir(BASE_DIR)
    );

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

