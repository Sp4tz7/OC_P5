<?php

namespace Controller\Backend;

use Core\AbstractController;
use Core\HTTPRequest;
use Core\HTTPResponse;
use Core\Mailer;

class AdminCommentController extends AbstractController
{
    public function executeComments()
    {
        $this->adminOnly();
        $commentManager = $this->managers->getManagerOf('Comment');
        $comments       = $commentManager->getList();
        $this->page->addVar('comments', $comments);
    }

    public function executeDelete(HTTPRequest $request, HTTPResponse $response)
    {
        if ($request->getExists('id') and $this->formManager->compareCsrfToken()) {
            $commentManager = $this->managers->getManagerOf('Comment');
            $commentManager->delete($request->getDataGet('id'));
            $this->app->setFlash(
                'success',
                [
                    'title' => 'Comment deleted',
                    'content' => '',
                ]
            );

            $response->redirect($request->getReferrer());
        }
    }

    public function executeApprove(HTTPRequest $request, HTTPResponse $response)
    {
        if ($request->getExists('id') and $this->formManager->compareCsrfToken()) {
            $commentManager = $this->managers->getManagerOf('Comment');
            $postManager    = $this->managers->getManagerOf('Post');
            $userManager    = $this->managers->getManagerOf('User');

            $comment = $commentManager->getUnique($request->getDataGet('id'));
            $comment->setStatus('APPROVED');
            $commentManager->save($comment);
            $this->app->setFlash(
                'success',
                [
                    'title' => 'Comment has benn approved',
                    'content' => '',
                ]
            );

            // Send an email to the comment author if he asked for in his settings
            $user = $userManager->getUnique($comment->getUserId());
            $post = $postManager->getUnique($comment->getPostId());
            if ($user->getSendEmailApprove()) {
                $mail = new Mailer();
                $mail->setEmailTemplate('review_comment.twig');
                $mail->setUserData($user);
                $mail->setEmailSubject('Your comment has been approved');
                $mail->setVars(
                    [
                        'BLOGNAME' => SITE_NAME,
                        'SITE_URL' => SITE_URL.'/post/'.$post->getCategorySlug().'/'.$post->getSlug().'/#comment'.$comment->getId(),
                        'TITLE' => 'Your comment has been approved',
                        'SUBTITLE' => 'Your comment has been approved',
                        'MESSAGE' => 'Hi '.$user->getfirstname().'. You comment on '.SITE_NAME.' has been approved. You can review it here.',
                    ]
                );
                $mail->sendEmail();
            }

            // If the comment is a response to an other comment, we send an email to the initial comment author
            if ($comment->getParentId()) {
                $commentAuthor = $commentManager->getUnique($comment->getParentId());
                $author        = $userManager->getUnique($commentAuthor->getUserId());

                if ($author->getSendEmailReplay()) {
                    $mail = new Mailer();
                    $mail->setEmailTemplate('review_comment.twig');
                    $mail->setUserData($author);
                    $mail->setEmailSubject('Someone replies to your comment');
                    $mail->setVars(
                        [
                            'BLOGNAME' => SITE_NAME,
                            'SITE_URL' => SITE_URL.'/post/'.$post->getCategorySlug().'/'.$post->getSlug().'/#comment'.$comment->getId(),
                            'TITLE' => 'Someone replies to your comment',
                            'SUBTITLE' => 'Someone replies to your comment',
                            'MESSAGE' => 'Hi '.$author->getfirstname().'. Someone replies to your comment on '.SITE_NAME.'. You can review it here.',
                        ]
                    );
                    $mail->sendEmail();
                }
            }


            $response->redirect($request->getReferrer());
        }
    }

    public function executeReject(HTTPRequest $request, HTTPResponse $response)
    {
        if ($request->getExists('id') and $this->formManager->compareCsrfToken()) {
            $commentManager = $this->managers->getManagerOf('Comment');
            $postManager    = $this->managers->getManagerOf('Post');
            $userManager    = $this->managers->getManagerOf('User');

            $comment = $commentManager->getUnique($request->getDataGet('id'));
            $comment->setStatus('REJECTED');
            $commentManager->save($comment);
            $this->app->setFlash(
                'success',
                [
                    'title' => 'Comment has benn rejected',
                    'content' => '',
                ]
            );

            // Send an email to the comment author if he asked for in his settings
            $user = $userManager->getUnique($comment->getUserId());
            $post = $postManager->getUnique($comment->getPostId());
            if ($user->getSendEmailApprove()) {
                $mail = new Mailer();
                $mail->setEmailTemplate('review_comment.twig');
                $mail->setUserData($user);
                $mail->setEmailSubject('Your comment has been rejected');
                $mail->setVars(
                    [
                        'BLOGNAME' => SITE_NAME,
                        'SITE_URL' => SITE_URL.'/post/'.$post->getCategorySlug().'/'.$post->getSlug().'/',
                        'TITLE' => 'Your comment has been rejected',
                        'SUBTITLE' => 'Your comment has been rejected',
                        'MESSAGE' => 'Hi '.$user->getfirstname().'. You comment on '.SITE_NAME.' has been rejected. You can submit a new one here.',
                    ]
                );
                $mail->sendEmail();
            }


            $response->redirect($request->getReferrer());
        }
    }

    public function executeEdit(HTTPRequest $request, HTTPResponse $response)
    {
        $this->adminOnly();
        $commentManager = $this->managers->getManagerOf('Comment');
        $userManager    = $this->managers->getManagerOf('User');
        $postManager    = $this->managers->getManagerOf('Post');

        if ($request->postExists('edit_comment') and ($request->getDataPost('edit_comment') === $request->getDataGet('id'))) {
            if ($this->formManager->compareCsrfToken()) {
                $comment = $commentManager->getUnique($request->getDataPost('edit_comment'));
                $user    = $userManager->getUnique($request->getSession('UserAuth'));

                $comment->setMessage($request->getDataPost('message'));
                $comment->setDateEdit('NOW');

                // If the comment has been edited, we have to re validate it
                if ($user->getRole() == 'SUPERADMIN') {
                    $active = $request->postExists('active') ? 'APPROVED' : 'PENDING';
                    $comment->setStatus($active);
                } else {
                    $comment->setStatus('PENDING');
                }

                $result = $commentManager->save($comment);
                if ($result == 1) {
                    $this->app->setFlash(
                        'success',
                        [
                            'title' => 'Success',
                            'content' => 'The comment has been saved',
                        ]
                    );

                    $response->redirect('/admin/comment/edit/'.$request->getDataPost('edit_comment').'/');
                } else {
                    $this->app->setFlash(
                        'error',
                        [
                            'title' => 'Error',
                            'content' => $result,
                        ]
                    );
                }
                $this->formManager->killCsrfToken();
            } else {
                $this->app->setFlash(
                    'error',
                    [
                        'title' => 'Error',
                        'content' => 'The token is not valid',
                    ]
                );
            }
        }

        $comment = $commentManager->getUnique($request->getDataGet('id'));

        if (is_object($comment) and $comment->getId()) {
            $post = $postManager->getUnique($comment->getPostId());
            $this->page->addVar('post', $post);
            $this->page->addVar('editComment', $comment);
        } else {
            $this->app->setFlash('error', ['content' => 'error']);
        }
    }


}
