<?php

namespace Cabbage;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
    public function image(Request $request, Application $app, $hash)
    {
        $database = new Database($app);
        $image = $database->getImage($hash);
        $response = new Response();

        if ($image === null) {
            $response->setStatusCode(404);
        }
        else {
            $response->setContent($image->getRawData());
            $response->setStatusCode(200);
            $response->headers->set('Content-Type', $image->getMimeType());
        }

        return $response;
    }

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

    public function thumbnail(Request $request, Application $app, $hash)
    {
        $database = new Database($app);
        $image = $database->getImage($hash);
        $response = new Response();

        if ($image === null) {
            $response->setStatusCode(404);
        }
        else {
            $response->setContent($image->getThumbnail());
            $response->setStatusCode(200);
            $response->headers->set('Content-Type', 'image/jpeg');
        }

        return $response;
    }

    public function view(Request $request, Application $app, $gallery, $hash)
    {
        $database = new Database($app);

        return $app['twig']->render('view.html.twig', array(
            'gallery' => 'asdf',
            'image' => $database->getImage($hash)
        ));
    }
}

