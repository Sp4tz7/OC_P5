<?php

namespace Controller\Frontend;

use Core\AbstractController;
use Core\HTTPRequest;

class FrontendController extends AbstractController
{
    public function executeHome(HTTPRequest $request)
    {
        $postManager = $this->managers->getManagerOf('Post');
        $posts       = $postManager->getList();
        $this->page->addVar('posts', $posts);
    }


}
