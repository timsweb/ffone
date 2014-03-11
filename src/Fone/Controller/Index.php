<?php
namespace Fone\Controller;

use Fone\Application;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class Index extends AbstractController
{

    public function homepage(Application $app)
    {
        $nextRound = $app['roundMapper']->getNextRound();
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
        $error = null;
        if ('POST' == $request->getMethod()) {
            $user = $app->user();
            if ($request->request->get('newPassword') === $request->request->get('confirmPassword')) {
                $newPassword = $app->encodePassword($user, $request->request->get('newPassword'));
                $encoder = $app['security.encoder_factory']->getEncoder($user); /*@var $encoder \Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder*/
                if ($encoder->isPasswordValid($user->getPassword(), $request->request->get('currentPassword'), null)) {
                    $db = $this->_getDb($app);
                    $db->update('users', ['password' => $newPassword], ['id' => $user->getId()]);
                    $app['session']->getFlashBag()->add('message', 'Password updated');
                    return $app->redirect($app->path('change_password'));
                } else {
                    $app['session']->getFlashBag()->add('message', 'Wrong password.');
                }
            } else {
                $app['session']->getFlashBag()->add('message', 'Paswords don\'t match.');
            }
            return $app->redirect($app->path('change_password'));
        }
        return $app['twig']->render('password_reset.twig', [
            'error' => $error,
	]);
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