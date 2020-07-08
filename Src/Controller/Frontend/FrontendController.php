<?php

namespace Controller\Frontend;

use Config\config;
use Core\AbstractController;
use Core\FormManager;
use Core\HTTPRequest;
use Core\HTTPResponse;
use Core\Mailer;
use Core\SiteMapsManager;

class FrontendController extends AbstractController
{
    public function executeHome(HTTPRequest $request)
    {
        $postManager = $this->managers->getManagerOf('Post');
        $posts       = $postManager->getList();
        $this->page->addVar('posts', $posts);
    }

    public function executeContact(HTTPRequest $request, HTTPResponse $response)
    {
        if ($request->postExists('contact_form')) {
            $recaptcha = new \ReCaptcha\ReCaptcha(Config::ReCaptchaSecret);
            $resp      = $recaptcha->verify($request->getDataPost('g-recaptcha-response'));
            if (!$resp->isSuccess()) {
                $error = $resp->getErrorCodes();
                $this->app->setFlash(
                    'error',
                    ['title' => 'Form error', 'content' => 'The ReCaptcha is not valid: '.$error[0]]
                );

                return false;
            }
            $form = new FormManager();
            if (!$form->compareCsrfToken()) {
                $this->app->setFlash('error', ['title' => 'Form error', 'content' => 'Invalid Token']);

                return false;
            }
            if (!$form->postNotEmpty($_POST)) {
                $this->app->setFlash('error', ['title' => 'Form error', 'content' => 'All fields are required']);

                return false;
            }
            if (!$form->isEmail($request->getDataPost('email'))) {
                $this->app->setFlash('error', ['title' => 'Form error', 'content' => 'The email is not valid']);

                return false;
            }

            $mail = new Mailer();
            $mail->setEmailTemplate('contact.twig');
            $mail->setEmailSubject('New message');
            $mail->setEmailTo(config::getSmtpSettings()['username']);
            $mail->setVars(
                [
                    'BLOGNAME' => SITE_NAME,
                    'SITE_URL' => SITE_URL,
                    'TITLE' => 'New message',
                    'SUBTITLE' => 'A new message from '.SITE_NAME.' has been posted',
                    'MESSAGE' => "Hi. The following visitor left this message.\n".
                        "Name: ".$request->getDataPost('name')."\n".
                        "Email: ".$request->getDataPost('email')."\n".
                        "Phone: ".$request->getDataPost('phone')."\n".
                        "Message: \n".$request->getDataPost('message')."\n",
                ]
            );
            if ($mail->sendEmail()) {
                $this->app->setFlash(
                    'success',
                    [
                        'title' => 'Thank you',
                        'content' => 'Your message has been sent!',
                    ]
                );

                $response->redirect('/contact/');
            }
        }
    }

    public function executeSitemaps()
    {
        $sitemap = new SiteMapsManager();
        $sitemap->createSiteMap();
        $urls = $sitemap->getUrls();
        $this->page->addVar('urls', $urls);

    }
}
