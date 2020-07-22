<?php

namespace Controller\Frontend;

use Core\AbstractController;
use Core\HTTPRequest;
use Core\HTTPResponse;
use Core\Mailer;
use Entity\User;

class UserController extends AbstractController
{
    public function executeLogin(HTTPRequest $request, HTTPResponse $response)
    {
        $userManager = $this->managers->getManagerOf('User');
        $redirect    = $request->postExists('redirect') ? $request->getDataPost('redirect') : '/login/';

        // Automatic login from session
        if ($request->sessionExists('UserAuth')) {
            $response->redirect('/admin/');
        }

        // Automatic login from cookie
        if ($request->cookieExists('User')) {
            preg_match('/(.*)\{(\d*)\}/', $request->getCookie('User'), $cookieData);
            if (isset($cookieData[2])) {
                $cookieDataPassword = $cookieData[1];
                $cookieDataId       = $cookieData[2];
                $user               = $userManager->getUnique($cookieDataId);
                if (is_object($user) and $user->getId()) {
                    if ($cookieDataPassword === $user->getPassword()) {
                        session_regenerate_id();
                        $response->setSession('UserAuth', $user->getId());
                        $response->redirect('/admin/');
                    }
                }
            } else {
                $response->redirect('/login/');
            }

        }

        // Login user from login registration form
        if ($request->postExists('login_user')) {
            $nickname = $request->getDataPost('username');
            $user     = $userManager->getByNicknameOrEmail($nickname);

            if (is_object($user) and $user->getPassword() == '') {
                $this->app->setFlash(
                    'error',
                    [
                        'content' => 'Your account has not been verified.<br/> Please check your email inbox 
<strong>or</strong> <a href="/new-activation-link/'.$user->getToken().'">send a new activation link</a> ',
                    ]
                );

                return false;
            }

            if (is_object($user) and password_verify($request->getDataPost('password'), $user->getPassword())) {
                if (!$user->getActive()) {
                    $this->app->setFlash('danger', ['content' => 'Your account has been deactivated']);

                    return false;
                }
                $response->setSession('UserAuth', $user->getId());
                if ($request->postExists('remember')) {
                    $response->setCookie(
                        'User',
                        $user->getPassword().'{'.$user->getId().'}',
                        time() + (30 * 24 * 3600),
                        '/'
                    );
                }
                $this->app->setFlash('success', ['content' => 'You hav successfully logged in.']);
                $response->redirect($redirect);

            } else {
                $this->page->addVar('username', $request->getDataPost('username'));
                $this->app->setFlash('error', ['content' => 'The username or the password is not valid']);
                $response->redirect($redirect);
            }
        }

        $this->page->addVar('title', 'Login');

    }

    public function executeNewActivation(HTTPRequest $request, HTTPResponse $response)
    {
        $userManager = $this->managers->getManagerOf('User');
        $token       = $request->getDataGet('token');
        $user        = $userManager->getByToken($token);

        if (is_object($user)) {
            $token = $this->formManager->setToken();
            $user->setToken($token['token']);
            $user->setTokenValidity($token['validity']);
            $userManager->save($user);

            $mail = new Mailer();
            $mail->setEmailTemplate('new_user.twig');
            $mail->setUserData($user);
            $mail->setEmailSubject('New account');
            $mail->setVars(
                [
                    'BLOGNAME' => SITE_NAME,
                    'TOKEN' => $token['token'],
                    'SITE_URL' => SITE_URL,
                    'TITLE' => 'Account activation',
                    'SUBTITLE' => 'A new account has been created',
                    'MESSAGE' => 'Hi '.$user->getFirstname().'. You requested a new account on '.SITE_NAME.'.
                You can activate it by following this link and set a new password.',
                ]
            );
            $mail->sendEmail();
            $this->app->setFlash(
                'success',
                [
                    'content' => 'A new activation link has been sent to your email address.',
                ]
            );

            $response->redirect('/login/');

        } else {
            $this->page->set404();

            return false;
        }
    }

    public function executeNewPassword(HTTPRequest $request, HTTPResponse $response)
    {
        $userManager = $this->managers->getManagerOf('User');
        $redirect    = $request->postExists('redirect') ? $request->getDataPost('redirect') : '/login/';

        // Sent from new password from
        if ($request->postExists('reset_password')) {
            $email = $request->getDataPost('email');
            $user  = $userManager->getByNicknameOrEmail($email);
            $token = $this->formManager->setToken();
            if (is_object($user)) {
                if (!$user->getActive()) {
                    $this->app->setFlash('error', ['content' => 'Your account has been deactivated']);

                    return false;
                }
                $mail = new Mailer();
                $mail->setEmailTemplate('new_password.twig');
                $mail->setUserData($user);
                $mail->setEmailSubject('New password');
                $mail->setVars(
                    [
                        'BLOGNAME' => SITE_NAME,
                        'TOKEN' => $token['token'],
                        'SITE_URL' => SITE_URL,
                        'TITLE' => 'New password',
                        'SUBTITLE' => 'You asked for a new password',
                        'MESSAGE' => 'Hi '.$user->getFirstname().'. You requested a new password on '.SITE_NAME.' ',
                    ]
                );

                $user->setToken($token['token']);
                $user->setTokenValidity($token['validity']);
                $userManager->save($user);
                $mail->sendEmail();

            }
            $this->app->setFlash(
                'success',
                [
                    'content' => 'Please Check your emails. We have sent to you a new password request',
                ]
            );
            $response->redirect($redirect);
        }

        // Get from email (url)
        $token = $request->getDataGet('token');
        $user  = $userManager->getByToken($token);

        if (!is_object($user)) {
            $this->page->set404();

            return false;
        }

        $date          = new \DateTime();
        $tokenValidity = new \DateTime($user->getTokenValidity());

        if ($tokenValidity < $date) {
            $this->app->setFlash(
                'error',
                ['content' => 'This link has expired']
            );

            $response->redirect($redirect);
        }

        // Sent from generate password from
        if ($request->postExists('new_password') and $request->postExists('password')) {

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
            $user->setToken('');
            $result = $userManager->save($user);

            if ($result != 1) {
                $this->app->setFlash('error', ['content' => $result]);
            } else {

                $this->app->setFlash('success', ['content' => 'The password has been changed']);
                $response->redirect($redirect);
            }
        }


    }

    public function executeLogout(HTTPRequest $request, HTTPResponse $response)
    {
        if ($request->cookieExists('User')) {
            $response->killKookie('User');
        }
        if ($request->sessionExists('UserAuth')) {
            $response->killSession('UserAuth');
        }
        $this->app->setFlash('success', ['content' => 'You successfully logged out.']);
        $response->redirect('/');
    }

    public function executeRegister(HTTPRequest $request, HTTPResponse $response)
    {
        $redirect = $request->postExists('redirect') ? $request->getDataPost('redirect') : '/login/';
        if ($request->postExists('register_user')) {
            $userManager = $this->managers->getManagerOf('User');
            $nickname    = $request->getDataPost('username');
            $email       = $request->getDataPost('email');
            $password    = $request->getDataPost('password');
            $rPassword   = $request->getDataPost('rpassword');

            $this->page->addVar('form', $request->getAllPost());

            if ($password !== $rPassword) {
                $this->app->setFlash('error', ['content' => 'The passwords does not match']);
                $response->redirect($redirect);

                return false;
            } elseif ($userManager->getByNicknameOrEmail($email)) {
                $this->app->setFlash('error', ['content' => 'This email has already been taken']);
                $response->redirect($redirect);

                return false;
            } elseif ($userManager->getByNicknameOrEmail($nickname)) {
                $this->app->setFlash('error', ['content' => 'The username is not available']);
                $response->redirect($redirect);

                return false;
            }

            // Add new user
            $userManager = $this->managers->getManagerOf('User');
            $user        = new User();
            $token       = $this->formManager->setToken();
            $user->setFirstname($request->getDataPost('firstname'));
            $user->setLastname($request->getDataPost('lastname'));
            $user->setNickname($request->getDataPost('username'));
            $user->setEmail($request->getDataPost('email'));
            $user->setRole('MEMBER');
            $user->setActive(1);
            $user->setToken($token['token']);
            $user->setTokenValidity($token['validity']);
            $user->setSendEmailApprove(0);
            $user->setSendEmailReplay(0);
            $user->setShowFullName(0);

            $mail = new Mailer();
            $mail->setEmailTemplate('new_user.twig');
            $mail->setUserData($user);
            $mail->setEmailSubject('New account');
            $mail->setVars(
                [
                    'BLOGNAME' => SITE_NAME,
                    'TOKEN' => $token['token'],
                    'SITE_URL' => SITE_URL,
                    'TITLE' => 'New account',
                    'SUBTITLE' => 'A new account has been created',
                    'MESSAGE' => 'Hi '.$user->getFirstname().'. You requested a new account on '.SITE_NAME.'. You can activate it by following this link and set a new password.',
                ]
            );

            $result = $userManager->save($user);
            if ($result != 1) {
                $this->app->setFlash('error', $result);
            } else {
                try {
                    $mail->sendEmail();
                    $this->app->setFlash(
                        'success',
                        [
                            'content' => 'Thank you for registering. An email with an activation link has been sent to your email address',
                        ]
                    );
                    $response->redirect($redirect);
                } catch (\Exception $exception) {
                    $this->app->setFlash(
                        'error',
                        [
                            'title' => 'Mail could not been sent',
                            'content' => $exception->getMessage(),
                        ]
                    );

                }
            }
        }
    }
}
