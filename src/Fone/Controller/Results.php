<?php
namespace Fone\Controller;

class Results implements \Silex\ControllerProviderInterface
{

    public function connect(Application $app)
    {
        // creates a new controller based on the default route
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function (Application $app) {
            return $app->redirect('/hello');
        });

        return $controllers;
    }
}