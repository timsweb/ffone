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
                    $app['session']->getFlashBag()->add('success', 'Password updated');
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

    public function myteam(Request $request, Application $app)
    {
        $nextRound = $app['roundMapper']->getNextRound(time() + 86400);
        $drivers = $app['driverMapper']->fetchAll();
        $teams = $app['teamMapper']->fetchAll();
        $currentTeam = $app['userTeamMapper']->getCurrentTeam($app->user()->getId());

        if ('POST' == $request->getMethod()) {
            //TODO: validate & save
            $driverA = $app['driverMapper']->get($request->request->get('driverA'));
            $driverB = $app['driverMapper']->get($request->request->get('driverB'));
            $teamA = $app['teamMapper']->get($request->request->get('teamA'));
            $teamB = $app['teamMapper']->get($request->request->get('teamB'));
            if ($driverA->getCode() == $driverB->getCode()) {
                $app['session']->getFlashBag()->add('message', 'You can\'t have the same driver twice.');
                return $app->redirect($app->path('my_team'));
            }
            if ($teamA->getCode() == $teamB->getCode()) {
                $app['session']->getFlashBag()->add('message', 'You can\'t have the same team twice.');
                return $app->redirect($app->path('my_team'));
            }
            if ($teamA->getCost() + $teamB->getCost() + $driverA->getCost() + $driverB->getCost() > 80) {
                $app['session']->getFlashBag()->add('message', 'You\'re over budget.');
                return $app->redirect($app->path('my_team'));
            }
            $userTeam = new \Fone\Model\UserTeam([
                'driverA' => $driverA->getCode(),
                'driverB' => $driverB->getCode(),
                'teamA' => $teamA->getCode(),
                'teamB' => $teamB->getCode(),
                'effectiveFrom' => $nextRound->getId(),
                'userId' => $app->user()->getId(),
            ]);
            $app['userTeamMapper']->save($userTeam);
            $app['session']->getFlashBag()->add('success', 'Your team has been saved.');
            return $app->redirect($app->path('my_team'));
        }

        return $app['twig']->render('my_team.twig', [
            'current_team' => $currentTeam,
            'all_drivers' => $drivers,
            'all_teams' => $teams,
            'next_round' => $nextRound
	]);
    }

    public function results(Application $app)
    {
        return "results";
    }

    public function rules(Application $app)
    {
        $scores = [];
        for ($i = 1; $i < 22; $i++) {
            $scores[$i] = [
                'race' => \Fone\Model\RoundResult::getRaceScore($i),
                'quali' => \Fone\Model\RoundResult::getQualiScore($i),
            ];
        }
        return $app['twig']->render('rules.twig', [
            'scores' => $scores
        ]);
    }
}