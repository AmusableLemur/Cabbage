<?php

namespace Cabbage;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Controller
{
    public function overview(Request $request, Application $app)
    {
        $parser = new Parser(BASE_DIR);

        return $app['twig']->render('overview.html.twig', array(
            'galleries' => $parser->getGalleries()
        ));
    }

    public function view(Request $request, Application $app, $gallery, $image)
    {
        return $app['twig']->render('view.html.twig', array(
            'gallery' => $gallery,
            'image' => $image
        ));
    }
}

