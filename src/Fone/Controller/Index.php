<?php
namespace Fone\Controller;

use Silex\Application;

class Index implements AbstractController
{

    public function homepage(Application $app)
    {
        $db = $app['db']; /*@var $db \Doctrine\DBAL\Connection*/
        //get next round
        return $app['twig']->render('index.html', array());
    }

    public function login(Applcation $app)
    {
        //handl login form
    }

    public function logout(Application $app)
    {

    }

    public function myteam(Applciation $app)
    {

    }
}