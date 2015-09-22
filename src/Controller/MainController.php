<?php

namespace Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Model\UsersModel;

use Form\RegisterForm;
use Form\UpForm;

class MainController implements ControllerProviderInterface
{

    public function connect(Application $app)
    {
        $mainController = $app['controllers_factory'];
        $mainController->get('/', array($this, 'indexAction'))->bind('main_index');
        $mainController->match('/register/', array($this, 'registerAction'))->bind('main_register');
        $mainController->post('/register/', array($this, 'registerAction'));
        //$mainController->match('/up/', array($this, 'upAction'))->bind('main_up');
        //$mainController->post('/up/', array($this, 'upAction'));
        return $mainController;
    }

    public function indexAction(Application $app)
    {
        return $app['twig']->render('main/index.twig');
    }

    public function registerAction(Application $app, Request $request)
    {
        $data = array(
            'login' => 'Login',
            'password' => 'Password',
            'mail' => 'Email',
            'role_id' => '3',
        );

        $form = $app['form.factory']
            ->createBuilder(new RegisterForm(), $data)->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $data['password'] = $app['security.encoder.digest']->encodePassword($data['password'],'');
            $usersModel = new usersModel($app);
            $usersModel->saveUser($data);
            $app['session']->getFlashBag()->add(
                'message', array(
                    'type' => 'success', 'content' => $app['translator']->trans(
                        'Welcome, you!'
                    )
                )
            );
            return $app->redirect(
                $app['url_generator']->generate('main_index'), 
                301
            );
        }

        $this->view['form'] = $form->createView();

        return $app['twig']->render('main/register.twig', $this->view);
    }

/*
    public function upAction(Application $app, Request $request)
    { 

        $ada = new \stdClass();
        $ada->guessExtension();
        $form = $app['form.factory']
            ->createBuilder(new UpForm())->getForm();

        $form->handleRequest($request);



        if ($form->isValid()) {

            $form->getData();
            $file = $form['image'];
            var_dump($file);
            
            //$extension = $file->guessExtension();
            //var_dump($extension);

        }
        $this->view['form'] = $form->createView();

        return $app['twig']->render('main/up.twig', $this->view);
    }

    */
}