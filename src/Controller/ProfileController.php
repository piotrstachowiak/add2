<?php

namespace Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

use Model\ProfilesModel;
use Model\UsersModel;

use Form\EditProfileForm;
use Form\ChangePasswordForm;


class ProfileController implements ControllerProviderInterface
{
    public function connect (Application $app)
    {
        $adsController = $app['controllers_factory'];
        $adsController->get('/', array($this, 'indexAction'))->bind('profile_index');
        $adsController->get('/ads/', array($this, 'adsAction'))->bind('profile_ads');
        $adsController->match('/edit/', array($this, 'editProfileAction'))->bind('profile_edit');
        $adsController->post('/edit/', array($this, 'editProfileAction'));
        $adsController->match('/change_password/', array($this, 'changePasswordAction'))->bind('profile_change_password');
        $adsController->post('/change_password/', array($this, 'changePasswordAction'));
        return $adsController;
    }

    public function indexAction (Application $app, Request $request)
    {  
        $usersModel = new UsersModel($app);
        $profile = $usersModel->getCurrentUser($app);
        return $app['twig']->render('profile/index.twig', $profile);
    }

    public function adsAction (Application $app)
    {
        $usersModel = new UsersModel($app);
        $id = $usersModel->getCurrentUserId($app);
        $profileModel = new ProfilesModel($app);
        $ads['ads'] = $profileModel->getUsersAds($id);
        return $app['twig']->render('profile/ads.twig', $ads);
    }

    public function editProfileAction (Application $app, Request $request)
    {
        $data = array(
            'login' => 'Login',
            'password' => 'Password',
        );

        $usersModel = new UsersModel($app);
        $user = $usersModel->getCurrentUser($app);
        $id = $user['id'];
        $role = $user['role_id'];
        $data['id'] = $id;
        $data['role'] = $role;

        $form = $app['form.factory']
            ->createBuilder(new EditProfileForm(), $data)->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $data['password'] = $app['security.encoder.digest']->encodePassword($data['password'],'');
            $profileModel = new profilesModel($app);
            $profileModel->updateUser($data);
            $app['session']->getFlashBag()->add(
                'message', array(
                    'type' => 'success', 'content' => $app['translator']->trans(
                        'Profile updated.'
                    )
                )
            );
            return $app->redirect(
                $app['url_generator']->generate('auth_login'), 
                301
            );
        }

        $this->view['form'] = $form->createView();

        return $app['twig']->render('profile/editProfile.twig', $this->view);
    }

    public function changePasswordAction(Application $app, Request $request)
    {
        $usersModel = new UsersModel($app);
        $user = $usersModel->getCurrentUser($app);

        $form = $app['form.factory']
            ->createBuilder(new ChangePasswordForm())->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $data['old_password'] = $app['security.encoder.digest']->encodePassword($data['old_password'],'');
            if ($data['old_password'] == $user['password']) {
                unset($data['old_password']);
                $data['new_password'] = $app['security.encoder.digest']->encodePassword($data['new_password'],'');
                $data['id'] = $user['id'];
                var_dump($data);
                $profileModel = new profilesModel($app);
                $profileModel->updatePassword($data);
                $app['session']->getFlashBag()->add(
                    'message', array(
                        'type' => 'success', 'content' => $app['translator']->trans(
                            'Password updated'
                        )
                    )
                );
                return $app->redirect(
                    $app['url_generator']->generate('profile_index'), 
                    301
                );
            }
        }

        $this->view['form'] = $form->createView();

        return $app['twig']->render('profile/changePassword.twig', $this->view);
    }
/*
    public function deleteAccount()
    {

    }
*/
}