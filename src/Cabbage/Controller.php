<?php

namespace Cabbage;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Controller
{
    public function overview(Request $request, Application $app)
    {
        $database = new Database($app);

        return $app['twig']->render('overview.html.twig', array(
            'galleries' => $database->getGalleries()
        ));
    }

    public function refresh(Request $request, Application $app)
    {
        if (file_exists(DB_PATH)) {
            unlink(DB_PATH);
        }

        $database = new Database($app);
        $parser = new Parser(GALLERIES_PATH);

        $galleries = $parser->getGalleries();

        foreach ($galleries as $gallery) {
            $database->storeGallery($gallery);
        }

        return $app->redirect($request->getBaseUrl());
    }

    public function view(Request $request, Application $app, $gallery, $image)
    {
        return $app['twig']->render('view.html.twig', array(
            'gallery' => $gallery,
            'image' => $image
        ));
    }
}

