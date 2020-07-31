<?php

namespace Controller\Backend;

use Core\AbstractController;
use Core\HTTPRequest;

/**
 * Class BackendController
 * @package Controller\Backend
 */
class BackendController extends AbstractController
{
    /**
     * @param HTTPRequest $request
     */
    public function executeDashboard(HTTPRequest $request)
    {
        $commentManager   = $this->managers->getManagerOf('Comment');
        $userID           = (int)$request->getSession('UserAuth');
        $activeComments   = $commentManager->getList(-1, -1, 'APPROVED', null, $userID);
        $pendingComments  = $commentManager->getList(-1, -1, 'PENDING', null, $userID);
        $rejectedComments = $commentManager->getList(-1, -1, 'REJECTED', null, $userID);

        $this->page->addVar('comments', $activeComments);
        $this->page->addVar('pendingComments', $pendingComments);
        $this->page->addVar('rejectedComments', $rejectedComments);
    }

    /**
     *
     */
    public function executeAccessDenied()
    {
    }

}
