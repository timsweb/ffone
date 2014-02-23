<?php
namespace Fone\Controller;

use Silex\Application;

class Index extends AbstractController
{

    public function homepage(Application $app)
    {
        $db = $app['db']; /*@var $db \Doctrine\DBAL\Connection*/
        //get next round
        return $app['twig']->render('index.twig', array());
    }

    public function login(Applcation $app)
    {
        //handle login
    }

    public function logout(Application $app)
    {

    }

    public function myteam(Applciation $app)
    {

    }
}