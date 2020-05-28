<?php

namespace Controller\Frontend;

use Core\BackController;
use Core\HTTPRequest;
use Entity\User;
use FormBuilder\UserFormBuilder;

class UserController extends BackController
{
    public function executeLogin(HTTPRequest $request)
    {
    }

    public function executeLogout(HTTPRequest $request)
    {
    }

    public function executeRegister(HTTPRequest $request)
    {
        $user     = new User();
        $userform = new UserFormBuilder($user);
        $userform->build();
        $form = $userform->getForm();
        $this->page->addVar('form', $form->createView());
    }
}