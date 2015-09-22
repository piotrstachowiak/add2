<?php

namespace Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Form\LoginForm;

class AuthController implements ControllerProviderInterface
{

    protected $view = array();

    public function connect(Application $app)
    {
        $authController = $app['controllers_factory'];
        $authController->match('/login', array($this, 'loginAction'))
            ->bind('auth_login');
        $authController->get('/logout', array($this, 'logoutAction'))
            ->bind('auth_logout');
        return $authController;
    }


    public function loginAction(Application $app, Request $request)
    {
        $user = array(
            'login' => $app['session']->get('_security.last_username')
        );

        $form = $app['form.factory']->createBuilder(new LoginForm(), $user)
            ->getForm();

        $this->view = array(
            'form' => $form->createView(),
            'error' => $app['security.last_error']($request)
        );

        return $app['twig']->render('auth/login.twig', $this->view);
    }

    public function logoutAction(Application $app, Request $request)
    {
        $app['session']->clear();
        return $app['twig']->render('auth/logout.twig', $this->view);
    }
}