<?php

namespace Controller\Frontend;

use Config\config;
use Core\AbstractController;
use Core\HTTPRequest;
use Core\HTTPResponse;
use Core\Mailer;
use Entity\Comment;

/**
 * Class PostController
 * @package Controller\Frontend
 */
class PostController extends AbstractController
{
    /**
     * @param HTTPRequest $request
     * @return bool
     */
    public function executePost(HTTPRequest $request)
    {
        $postManager    = $this->managers->getManagerOf('Post');
        $commentManager = $this->managers->getManagerOf('Comment');

        $slug = $request->getDataGet('slug') ?: '';
        $post = $postManager->getBySlug($slug);

        if (!is_object($post)) {
            $this->page->set404();
            $this->page->addVar('content', 'This page does not exists');

            return false;
        }

        $category = $postManager->getCategoryBySlug($post->getCategorySlug());

        $activeComments  = $commentManager->getList(-1, -1, 'APPROVED', (int)$post->getId());
        $pendingComments = $commentManager->getList(-1, -1, 'PENDING', (int)$post->getId());
        $posts           = $postManager->getList(-1, -1, $post->getCategoryId());
        shuffle($posts);

        if ($post->getActive()) {
            $this->page->addVar('post', $post);
            $this->page->addVar('posts', $posts);
            $this->page->addVar('category', $category);
            $this->page->addVar('comments', $activeComments);
            $this->page->addVar('pendingComments', $pendingComments);
            $this->page->addVar('title', $post->getTitle());
        } else {
            $this->page->set404();
            $this->page->addVar('content', 'This blog post does not exists');
            $this->page->addVar('title', 'Oups! Post not found! ');
        }
    }

    /**
     * @param HTTPRequest  $request
     * @param HTTPResponse $response
     * @return bool
     * @throws \Exception
     */
    public function executeAddComment(HTTPRequest $request, HTTPResponse $response)
    {
        $redirect = $request->postExists('redirect') ? $request->getDataPost('redirect') : '/';
        if (!$request->sessionExists('UserAuth')) {
            $this->app->setFlash('error', ['content' => 'You have to be logged in to post a comment']);
            $response->redirect($redirect);

            return false;
        }

        if (!$request->postExists('message') and !is_string($request->getDataPost('message'))) {
            $this->app->setFlash('error', ['content' => 'You have an error in your message.']);
            $response->redirect($redirect);

            return false;
        }

        if ($request->getDataPost('id') != $request->getDataGet('id')) {
            $this->app->setFlash('error', ['content' => 'It seems you tried to bypass the ID system']);
            $response->redirect($redirect);

            return false;
        }

        $commentManager = $this->managers->getManagerOf('Comment');
        $userId         = $request->getSession('UserAuth');

        $comment = new Comment();
        $comment->setUserId($userId);
        $comment->setPostId($request->getDataPost('id'));
        $comment->setMessage($request->getDataPost('message'));
        $comment->setDateAdd('now');
        $comment->setStatus('PENDING');

        if ($request->postExists('parent_id') and $request->getDataPost('parent_id')) {
            $comment->setParentId($request->getDataPost('parent_id'));
        }

        $result = $commentManager->save($comment);
        if (is_int($result)) {
            $this->app->setFlash('success', [
                'title' => 'Your comment has been sent',
                'content' => 'It will be visible as soon as we have approve it.',
            ]);
        } else {
            $this->app->setFlash('error', [
                'title' => 'SQL Error',
                'content' => $result,
            ]);
        }

        $config = Config::getSmtpSettings();
        $mail   = new Mailer();
        $mail->setEmailTemplate('new_comment.twig');
        $mail->setEmailTo($config['username']);
        $mail->setEmailSubject('New comment');
        $mail->setVars(
            [
                'BLOGNAME' => SITE_NAME,
                'SITE_URL' => SITE_URL,
                'TITLE' => 'New comment',
                'SUBTITLE' => 'A new comment has been posted',
                'MESSAGE' => 'Hi. A new comment on '.SITE_NAME.' has been posted. You can review it here.',
            ]
        );
        $mail->sendEmail();

        $response->redirect($redirect);
    }

    /**
     * @param HTTPRequest  $request
     * @param HTTPResponse $response
     */
    public function executeId(HTTPRequest $request, HTTPResponse $response)
    {
        $postManager = $this->managers->getManagerOf('Post');
        $post        = $postManager->getUnique($request->getDataGet('id'));

        if (is_object($post)) {
            $url = '/post/'.$post->getCategorySlug().'/'.$post->getSlug().'/';
            $response->redirect($url);
        } else {
            $response->redirect404();
        }

    }

    /**
     * @param HTTPRequest $request
     */
    public function executeList(HTTPRequest $request)
    {
        $postManager  = $this->managers->getManagerOf('Post');
        $categorySlug = $request->getDataGet('category');
        if ($categorySlug == 'all') {
            $this->page->addVar('category', ['category_name' => 'All posts']);
            $posts = $postManager->getList();
            $this->page->addVar('posts', $posts);
            $this->page->addVar('title', 'All posts');
        } else {
            $category = $postManager->getCategoryBySlug($categorySlug);

            $posts = $postManager->getList(-1, -1, (int)$category->id);

            $this->page->addVar('category', $category);
            $this->page->addVar('posts', $posts);
            $this->page->addVar('title', $category->category_name);
        }
        $categories = $postManager->getCategories();
        $this->page->addVar('categories', $categories);

    }
}
