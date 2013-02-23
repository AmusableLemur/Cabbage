<?php

namespace Cabbage;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Controller
{
    public function overview(Request $request, Application $app)
    {
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
    }
}

