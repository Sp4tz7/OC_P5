<?php

namespace Controller\Backend;

use Core\AbstractController;
use Core\HTTPRequest;
use Core\HTTPResponse;
use Core\Mailer;
use Entity\User;

class AdminUserController extends AbstractController
{
    public function executeMyAccount(HTTPRequest $request, HTTPResponse $response)
    {
        $userManager = $this->managers->getManagerOf('User');
        $user        = $userManager->getUnique($request->getSession('UserAuth'));

        // Change password
        if ($request->postExists('change_password') and $this->app->compareCsrfToken()) {

            $password  = $request->getDataPost('password');
            $rPassword = $request->getDataPost('rpassword');
            if ($password !== $rPassword) {
                $this->app->setFlash('error', ['content' => 'The passwords does not match']);

                return false;
            } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
                $this->app->setFlash(
                    'error',
                    ['content' => 'The passwords should be min '.PASSWORD_MIN_LENGTH.' character length']
                );

                return false;
            }

            $user->setPassword($password);
            $userManager->save($user);

            $this->app->setFlash(
                'success',
                ['content' => 'The passwords has been changed']
            );

            $response->redirect('/admin/my-account/');

        }

        // Change privacy settings
        if ($request->postExists('edit_user_settings') and $this->app->compareCsrfToken()) {
            $user->setSendEmailApprove($request->getDataPost('send_email_approve'));
            $user->setSendEmailReplay($request->getDataPost('send_email_reply'));
            $user->setShowFullName($request->getDataPost('show_name'));
            $userManager->save($user);

            $this->app->setFlash(
                'success',
                ['content' => 'The settings has been updated']
            );

            $response->redirect('/admin/my-account/');

        }

        // Update user personal information
        if ($request->postExists('edit_user') and $this->app->compareCsrfToken()) {
            $user->setFirstname($request->getDataPost('firstname'));
            $user->setLastname($request->getDataPost('lastname'));
            $user->setNickname($request->getDataPost('username'));
            $user->setEmail($request->getDataPost('email'));

            $result = $userManager->save($user);
            if ($result != 1) {
                $this->app->setFlash(
                    'error',
                    [
                        'title'   => 'Error',
                        'content' => $result,
                    ]
                );
            } else {
                $this->app->setFlash(
                    'success',
                    ['content' => 'You personal data has been updated']
                );
            }
            $response->redirect('/admin/my-account/');

        }
    }

    public function executeUsers(HTTPRequest $request, HTTPResponse $response)
    {
        $userManager = $this->managers->getManagerOf('User');
        $this->page->addVar('users', $userManager->getList());

    }

    public function executeAdd(HTTPRequest $request, HTTPResponse $response)
    {
        $userManager = $this->managers->getManagerOf('User');
        $user        = new User();
        if ($request->postExists('edit_user') and $this->app->compareCsrfToken()) {
            $token = $this->app->setToken();
            $user->setFirstname($request->getDataPost('firstname'));
            $user->setLastname($request->getDataPost('lastname'));
            $user->setNickname($request->getDataPost('username'));
            $user->setEmail($request->getDataPost('email'));
            $user->setRole($request->getDataPost('role'));
            $user->setActive($request->getDataPost('active'));
            $user->setToken($token['token']);
            $user->setTokenValidity($token['validity']);
            $user->setSendEmailApprove(0);
            $user->setSendEmailReplay(0);
            $user->setShowFullName(0);

            $mail = new Mailer();
            $mail->setEmailTemplate('new_user.twig');
            $mail->setEmailData($user);
            $mail->setEmailSubject('New account');
            $mail->setVars(
                [
                    'BLOGNAME' => SITE_NAME,
                    'TOKEN'    => $token['token'],
                    'SITE_URL' => SITE_URL,
                    'TITLE'    => 'New account',
                    'SUBTITLE' => 'A new account has been created for you',
                    'MESSAGE'  => 'Hi '.$user->getLastname().'. A new account has been set for you by an administrator.
                You can access it by following this link and set a new password.',
                ]
            );

            $result = $userManager->save($user);
            if ($result != 1) {
                $this->app->setFlash(
                    'error',
                    [
                        'title'   => 'Error',
                        'content' => $result,
                    ]
                );
            } else {
                if ($mail->sendEmail()) {
                    $this->app->setFlash(
                        'success',
                        [
                            'title'   => 'User created',
                            'content' => 'The new user has been created and an email is sent to the user',
                        ]
                    );

                    $response->redirect('/admin/user/add/');
                }
            }
        }
        $this->page->addVar('editUser', $user);

    }

    public function executeEdit(HTTPRequest $request, HTTPResponse $response)
    {
        $this->adminOnly();

        $userManager = $this->managers->getManagerOf('User');
        if ($request->postExists('edit_user') and ($request->getDataPost('edit_user') === $request->getDataGet('id'))) {
            if ($this->app->compareCsrfToken()) {
                $user = $userManager->getUnique($request->getDataPost('edit_user'));
                $user->setFirstname($request->getDataPost('firstname'));
                $user->setLastname($request->getDataPost('lastname'));
                $user->setNickname($request->getDataPost('username'));
                $user->setEmail($request->getDataPost('email'));
                $user->setRole($request->getDataPost('role'));
                $user->setActive($request->getDataPost('active'));
                $user->setSendEmailApprove(0);
                $user->setSendEmailReplay(0);
                $user->setShowFullName(0);

                $result = $userManager->save($user);
                if ($result == 1) {
                    $this->app->setFlash(
                        'success',
                        [
                            'title'   => 'Success',
                            'content' => 'The user has been saved',
                        ]
                    );
                } else {
                    $this->page->addVar(
                        'error',
                        [
                            'title'   => 'Error',
                            'content' => $result,
                        ]
                    );
                }
                $this->app->killCsrfToken();
            } else {
                $this->app->setFlash(
                    'error',
                    [
                        'title'   => 'Error',
                        'content' => 'The token is not valid',
                    ]
                );
            }
        }
        $user = $userManager->getUnique($request->getDataGet('id'));
        if (is_object($user) and $user->getId()) {
            $this->page->addVar('editUser', $user);
        } else {
            $this->app->setFlash('error', ['This user does not exist']);
        }
    }

}
