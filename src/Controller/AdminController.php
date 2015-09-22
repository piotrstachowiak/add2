<?php

namespace Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

use Model\UsersModel;
use Model\AdsModel;

class AdminController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $mainController = $app['controllers_factory'];
        $mainController->get('/', array($this, 'indexAction'))->bind('admin_index');
        return $mainController;
    }

    public function indexAction(Application $app)
    {
        $view = array();
        $usersModel = new UsersModel($app);
        $view['users'] = $usersModel->getAll();
        $adsModel = new AdsModel($app);
        $view['ads'] = $adsModel->getAll();
        return $app['twig']->render('admin/index.twig', $view);
    }
}