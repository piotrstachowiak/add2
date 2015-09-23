<?php

namespace Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Model\UsersModel;
use Model\AdsModel;
use Model\CategoriesModel;

use Form\AdForm;

class AdsController implements ControllerProviderInterface
{

    public function connect(Application $app)
    {
        $adsController = $app['controllers_factory'];
        $adsController->get('/', array($this, 'indexAction'))->bind('ads_index');
        $adsController->get('/view/{id}', array($this, 'viewAction'))->bind('ads_view');
        $adsController->match('/add', array($this, 'addAction'))->bind('ads_add');
        $adsController->post('/add', array($this, 'addAction'));
        $adsController->match('/edit/{id}', array($this, 'editAction'))->bind('ads_edit');
        $adsController->post('/edit/{id}', array($this, 'editAction'));
        $adsController->match('/delete/{id}', array($this, 'deleteAction'))->bind('ads_delete');
        $adsController->post('/delete/{id}', array($this, 'deleteAction'));
        return $adsController;
    }

    public function indexAction(Application $app)
    {
        $view = array();
        $adsModel = new AdsModel($app);
        $view['ads'] = $adsModel->getAll();        
        return $app['twig']->render('ads/index.twig', $view);
    }

    public function viewAction(Application $app, Request $request)
    {
        try {
            $view = array();
            $id = (int)$request->get('id', null);
            $adsModel =  new AdsModel($app);
            $view['ads'] = $adsModel->getAd($id);
            $categoriesModel = new CategoriesModel($app);
            $category_id = $view['ads']['category_id'];
            $view['category'] = $categoriesModel->getCategory($category_id);
        } catch (\PDOException $e) {
            $app->abort(404, $app['translator']->trans('Ad not found'));
        }
        return $app['twig']->render('ads/view.twig', $view);
    }

    public function addAction(Application $app, Request $request)
    {
        $data = array(
            'title' => 'Title',
            'text' => 'Text',
            'category' => '',
            //'image_name' => '',
        );

        $categoriesModel = new CategoriesModel($app);
        $data['categories'] = $categoriesModel->getAll();

        $form = $app['form.factory']
            ->createBuilder(new AdForm(), $data)->getForm();
        $form->remove('id');

        $form->handleRequest($request);

        if ($form->isValid()) {

            $data = $form->getData();
            $data['category_id'] = $data['category'];
            unset($data['categories'], $data['category']);
            $usersModel = new UsersModel($app);
            $owner = $usersModel->getCurrentUserId($app);
            $data['user_id'] = $owner;
            //var_dump($image);
            //$extension = $form['image_name']->guessClientExtension();
/*
            if (!$extension)
            {
                $extension = 'bin';
            }

            $image_name = rand(1, 999999).'.'.$extension;
            var_dump($image_name);
            unset($data['image']);
*/

            $adModel = new AdsModel($app);
            $adModel->saveAd($data);

            $app['session']->getFlashBag()->add(
                'message', array(
                    'type' => 'success', 'content' => $app['translator']->trans(
                        'New ad added'
                    )
                )
            );
            return $app->redirect(
                $app['url_generator']->generate('ads_index'), 
                301
            );
        }

        $this->view['form'] = $form->createView();

        return $app['twig']->render('ads/add.twig', $this->view);
    }

    public function editAction(Application $app, Request $request)
    {   
        $adsModel = new AdsModel($app);
        $id = (int)$request->get('id', 0);
        $ad = $adsModel->getAd($id);
        $ad_owner = $ad['user_id'];

        $userModel = new UsersModel($app);
        $current_user_id = $userModel->getCurrentUserId($app);
        $current_user_role = $userModel->getUserRoles($current_user_id);
        
        if ($current_user_role=='ROLE_ADMIN' 
        || $current_user_role=='ROLE_MOD'
        || $ad_owner == $current_user_id) {
            $categoriesModel = new CategoriesModel($app);
            $ad['categories'] = $categoriesModel->getAll();

            if (count($ad)) {
                $form = $app['form.factory']
                   ->createBuilder(new AdForm(), $ad)->getForm();
                $form->handleRequest($request);

                if ($form->isValid()) {
                    $data = $form->getData();
                    $data['category_id'] = $data['category'];
                    unset($data['categories'], $data['category']);
                    $adsModel = new AdsModel($app);
                    $adsModel->saveAd($data);
                    $app['session']->getFlashBag()->add(
                        'message', array(
                            'type' => 'success', 'content' => $app['translator']->trans(
                                'Ad updated'
                            )
                        )
                    );
                    return $app->redirect(
                        $app['url_generator']->generate('ads_index'), 
                        301
                    );
                }
                $this->view['id'] = $id;
                $this->view['form'] = $form->createView();

            } else {
                return $app->redirect(
                    $app['url_generator']->generate('ads_add'), 
                    301
                );
            }

        } else {
            throw new ForbiddenException("You lack authority", 403);            
        }

        return $app['twig']->render('ads/edit.twig', $this->view);
    }

    public function deleteAction(Application $app, Request $request)
    {
        $adsModel = new AdsModel($app);
        $id = (int)$request->get('id', 0);
        $ad = $adsModel->getAd($id);

        if (count($ad)) {
            $adsModel->deleteAd($id);
            $app['session']->getFlashBag()->add(
                'message', array(
                    'type' => 'success', 'content' => $app['translator']->trans(
                        'Ad deleted.'
                    )
                )
            );
            return $app->redirect(
                $app['url_generator']->generate('profile_ads'),
                301
            );

            $this->view['id'] = $id;

        } else {
            return $app->redirect(
                $app['url_generator']->generate('ads_index')
            );
        }

        return $app['twig']->render('ads/delete.twig', $this->view);
    }

}
