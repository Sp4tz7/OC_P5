<?php

namespace Controller\Backend;

use Core\AbstractController;
use Core\HTTPRequest;

class BackendController extends AbstractController
{
    public function executeDashboard(HTTPRequest $request)
    {
        $commentManager  = $this->managers->getManagerOf('Comment');
        $id = (int)$request->getSession('UserAuth');
        $activeComments  = $commentManager->getList(-1, -1, 'APPROVED', null, $id);
        $pendingComments = $commentManager->getList(-1, -1, 'PENDING', null, $id);
        $rejectedComments = $commentManager->getList(-1, -1, 'REJECTED', null, $id);

        $this->page->addVar('comments', $activeComments);
        $this->page->addVar('pendingComments', $pendingComments);
        $this->page->addVar('rejectedComments', $rejectedComments);
    }

    public function executeAccessDenied(HTTPRequest $request)
    {
    }

}
