<?php

namespace Controller\Frontend;

use Core\AbstractController;
use Core\HTTPRequest;

class PostController extends AbstractController
{
    public function executePost(HTTPRequest $request)
    {
        $postManager = $this->managers->getManagerOf('Post');
        $slug        = $request->getDataGet('slug') ?: '';
        $post        = $postManager->getBySlug($slug);

        if (!is_object($post)) {
            $this->page->set404();
            $this->page->addVar('content', 'This page does not exists');

            return false;
        }

        $category = $postManager->getCategoryBySlug($post->getCategorySlug());

        $posts = $postManager->getList(-1, -1, $post->getCategoryId());
        shuffle($posts);

        if ($post->getActive()) {
            $this->page->addVar('post', $post);
            $this->page->addVar('posts', $posts);
            $this->page->addVar('category', $category);
        } else {
            $this->page->set404();
            $this->page->addVar('content', 'This blog post does not exists');
        }

    }

    public function executeList(HTTPRequest $request)
    {
        $postManager  = $this->managers->getManagerOf('Post');
        $categorySlug = $request->getDataGet('category');
        if ($categorySlug == 'all') {
            $this->page->addVar('category', ['category_name' => 'All posts']);
            $posts = $postManager->getList();
            $this->page->addVar('posts', $posts);
        } else {
            $category = $postManager->getCategoryBySlug($categorySlug);

            $posts = $postManager->getList(-1, -1, (int)$category->id);

            $this->page->addVar('category', $category);
            $this->page->addVar('posts', $posts);
        }
        $categories = $postManager->getCategories();
        $this->page->addVar('categories', $categories);
    }
}
