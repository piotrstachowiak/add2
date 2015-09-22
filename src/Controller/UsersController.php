<?php

namespace Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

use Model\UsersModel;
use Model\ProlilesModel;

use Form\UserForm;

class UsersController implements ControllerProviderInterface
{

    public function connect(Application $app)
    {
        $usersController = $app['controllers_factory'];
        $usersController->get('/', array($this, 'indexAction'))->bind('users_index');
        $usersController->match('/add', array($this, 'addAction'))->bind('users_add');
        $usersController->post('/add', array($this, 'addAction'));
        $usersController->match('/edit/{id}', array($this, 'editAction'))->bind('users_edit');
        $usersController->post('/edit/{id}', array($this, 'editAction'));
        $usersController->match('/delete/{id}', array($this, 'deleteAction'))->bind('users_delete');
        $usersController->post('/delete/{id}', array($this, 'deleteAction'));
        return $usersController;
    }

    public function indexAction(Application $app)
    {
        $view = array();
        $usersModel = new UsersModel($app);
        $view['users'] = $usersModel->getAll();  
        return $app['twig']->render('users/index.twig', $view);
    }

    public function addAction(Application $app, Request $request)
    {
        $data = array(
            'login' => 'Login',
            'password' => 'Password',
            'role' => 'Role',
            'mail' => 'Mail',
        );

        $usersModel = new UsersModel($app);
        $data['roles'] = $usersModel->getRoles();

        $form = $app['form.factory']
            ->createBuilder(new UserForm(), $data)->getForm();
        $form->remove('id');

        $form->handleRequest($request);

        if ($form->isValid()) {

            $data = $form->getData();
            $data['role_id'] = $data['role'];
            $data['password'] = $app['security.encoder.digest']->encodePassword($data['password'],'');
            unset($data['roles'], $data['role']);

            $userModel = new UsersModel($app);
            $userModel->saveUser($data);
            $app['session']->getFlashBag()->add(
                'message', array(
                    'type' => 'success', 'content' => $app['translator']->trans(
                        'New user added'
                    )
                )
            );
            return $app->redirect(
                $app['url_generator']->generate('admin_index'), 
                301
            );
        }

        $this->view['form'] = $form->createView();

        return $app['twig']->render('users/add.twig', $this->view);
    }

    public function editAction(Application $app, Request $request)
    {   
        $usersModel = new usersModel($app);
        $id = (int)$request->get('id', 0);
        $user = $usersModel->getUser($id);

        $usersModel = new UsersModel($app);
        $user['roles'] = $usersModel->getRoles();

        if (count($user)) {
            $form = $app['form.factory']
               ->createBuilder(new UserForm(), $user)->getForm();
            
            $form->handleRequest($request);

            if ($form->isValid()) {

                $data = $form->getData();
                $data['role_id'] = $data['role'];
                $data['password'] = $app['security.encoder.digest']->encodePassword($data['password'],'');
                unset($data['roles'], $data['role']);
                var_dump($data);

                $usersModel = new usersModel($app);
                $usersModel->saveUser($data);
                $app['session']->getFlashBag()->add(
                    'message', array(
                        'type' => 'success', 'content' => $app['translator']->trans(
                            'User edited'
                        )
                    )
                );
                return $app->redirect(
                    $app['url_generator']->generate('users_index'), 
                    301
                );
            }

            $this->view['id'] = $id;
            $this->view['form'] = $form->createView();

        } else {
            return $app->redirect(
                $app['url_generator']->generate('users_add'), 
                301
            );
        }

        return $app['twig']->render('users/edit.twig', $this->view);
    }

    public function deleteAction(Application $app, Request $request)
    {
        $usersModel = new usersModel($app);
        $id = (int)$request->get('id', 0);
        $user = $usersModel->getUser($id);
        $userId = $user['id'];
        $currentUserId = $usersModel->getCurrentUserId($app);

        if ($userId == $currentUserId) {
            $app['session']->getFlashBag()->add(
                'message', array(
                    'type' => 'success', 'content' => $app['translator']->trans(
                        'You can\'t erase your own account'
                    )
                )
            );
            return $app->redirect(
                $app['url_generator']->generate('admin_index')
            );
        } else if (count($user)) {
            $usersModel->deleteUser($id);
            $app['session']->getFlashBag()->add(
                'message', array(
                    'type' => 'success', 'content' => $app['translator']->trans(
                        'User deleted'
                    )
                )
            );
            return $app->redirect(
                $app['url_generator']->generate('admin_index'),
                301
            );

            $this->view['id'] = $id;

        } else {
            return $app->redirect(
                $app['url_generator']->generate('admin_index')
            );
        }

        return $app['twig']->render('users/delete.twig', $this->view);
    }
}