<?php
namespace Fone\Controller;

use Fone\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class Index extends AbstractController
{

    public function homepage(Application $app)
    {
        $db = $app['db']; /*@var $db \Doctrine\DBAL\Connection*/
        $nextRound = $db->fetchAssoc('select * from rounds where racedate > ? order by racedate asc limit 1', [time()]);
        return $app['twig']->render('index.twig', array('next_round' => $nextRound));
    }

    public function login(Request $request, Application $app)
    {
        //handle login
        //this dudes nailed it https://github.com/tmpjr/itaya/blob/master/app/views/login.html
        return $app['twig']->render('login.twig', [
	        'error'         => $app['security.last_error']($request),
	        'last_username' => $app['session']->get('_security.last_username')]);
    }

    public function loginCheck(Request $request, Application $app)
    {
        //http://symfony.com/doc/current/reference/configuration/security.html find login_check. I'm not sure this is even meant to be called.
        /* Ah ok. This should never be called. The firewall should intercept
         * the request and handle authenticating the user accordingly.
         *
         * see
         * http://stackoverflow.com/questions/17406446/how-does-the-login-check-path-route-work-without-default-controller-action
         */
    }

    public function changePassword(Request $request, Application $app)
    {
        if ('POST' == $request->getMethod()) {
           //
           return $app->redirect($app->path('change_password'));
        }
        return $app['twig']->render('password_reset.twig', [
	        'error'         => $app['security.last_error']($request),
	        'last_username' => $app['session']->get('_security.last_username')]);
    }

    public function logout(Application $app)
    {
        return "logout";
    }

    public function myteam(Application $app)
    {
        return "results";
    }

    public function results(Application $app)
    {
        return "results";
    }


}