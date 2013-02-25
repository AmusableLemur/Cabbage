<?php

namespace Cabbage;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
    public function download(Request $request, Application $app, $gallery)
    {
        $database = new Database($app);
        $gallery = $database->getGallery($gallery);
        $response = new Response();

        if ($gallery === null) {
            $response->setStatusCode(404);
        }
        else {
            $response->setContent($gallery->getArchive());
            $response->setStatusCode(200);
            $response->headers->set('Content-Type', 'application/octet-stream');
            $response->headers->set('Content-Disposition', 'attachment; filename='.
                $gallery->getIdent().'.zip');
            $response->headers->set('Content-Transfer-Encodinf', 'binary');
        }

        return $response;
    }

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
            $response->headers->set('Cache-Control', 'max-age=2592000');
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
        set_time_limit(60 * 60 * 24);
        
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

    public function search(Request $request, Application $app, $terms)
    {
        $database = new Database($app);
        $galleries = $database->findGalleries($terms);
        $messages = array();

        if (count($galleries) < 1) {
            $messages[] = "Nothing found :(";
        }

        return $app['twig']->render('overview.html.twig', array(
            'title' => 'Search results for '.$terms,
            'galleries' => $galleries,
            'messages' => $messages
        ));
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
            $response->headers->set('Cache-Control', 'max-age=2592000');
        }

        return $response;
    }

    public function view(Request $request, Application $app, $gallery, $hash)
    {
        $database = new Database($app);
        $gallery = $database->getGallery($gallery);
        $image = $gallery->getImage($hash);
        $response = new Response();

        if ($gallery === null || $image === null) {
            $response->setStatusCode(404);
        }
        else {
            $response->setContent(
                $app['twig']->render('view.html.twig', array(
                    'gallery' => $gallery,
                    'image' => $image
                ))
            );
            $response->setStatusCode(200);
        }

        return $response;
    }
}

