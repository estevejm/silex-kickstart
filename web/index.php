<?php

require_once __DIR__ . '/../app/Bootstrap.php';

$app = Bootstrap::execute();

ini_set('display_errors', $app['config']["debug"]);
ini_set('display_startup_errors', $app['config']["debug"]);

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

$app->get('/', function () use ($app) {
    return $app['twig']->render('main.twig');
});

if (!$app['debug']) {
    $app->error(function (\Exception $e, $code) use ($app) {
        switch ($code) {
            case 404:
                return $app['twig']->render(
                    'error.twig',
                    array("message" => "Sorry, the page you are looking for could not be found.")
                );

            default:
                return $app['twig']->render(
                    'error.twig',
                    array("message" => "Unknown error.")
                );
        }
    });
}

$app->run();
