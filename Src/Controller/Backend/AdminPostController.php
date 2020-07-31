<?php

namespace Controller\Backend;

use Core\AbstractController;
use Core\HTTPRequest;
use Core\HTTPResponse;
use Entity\Post;
use Imagine\Image\Box;
use Service\Service;

/**
 * Class AdminPostController
 * @package Controller\Backend
 */
class AdminPostController extends AbstractController
{
    public function executePosts()
    {
        $this->adminOnly();
        $postManager = $this->managers->getManagerOf('Post');
        $posts       = $postManager->getList();
        $categories  = $postManager->getCategories();
        $this->page->addVar('categories', $categories);
        $this->page->addVar('posts', $posts);
    }

    /**
     * @param HTTPRequest  $request
     * @param HTTPResponse $response
     */
    public function executeDelete(HTTPRequest $request, HTTPResponse $response)
    {
        if ($request->getExists('id') and $this->formManager->compareCsrfToken()) {
            $postManager = $this->managers->getManagerOf('Post');
            $post        = $postManager->getUnique($request->getDataGet('id'));
            unlink(APP_DIR.'Public/img/post/'.$post->getImageUrl());
            $postManager->delete($request->getDataGet('id'));
            $this->app->setFlash(
                'success',
                [
                    'title' => 'Post deleted',
                    'content' => '',
                ]
            );

            $response->redirect('/admin/posts/');
        }
    }

    /**
     * @param HTTPRequest  $request
     * @param HTTPResponse $response
     */
    public function executeAdd(HTTPRequest $request, HTTPResponse $response)
    {
        $this->adminOnly();

        $postManager = $this->managers->getManagerOf('Post');
        $userManager = $this->managers->getManagerOf('User');
        $post        = new Post();

        if ($request->postExists('add_post') and $this->formManager->compareCsrfToken()) {
            $post = new Post();
            $user = $userManager->getUnique($request->getSession('UserAuth'));

            $post->setCategoryId($request->getDataPost('category_id'));
            $post->setTitle($request->getDataPost('title'));
            $post->setSlug($request->getDataPost('title'));
            $post->setabstractContent($request->getDataPost('abstract_content'));
            $post->setContent($request->getDataPost('content'));
            $active = $request->postExists('active') ? 1 : 0;
            $post->setActive($active);
            $post->setCreatedBy($user->getId());
            $post->setDateAdd('now');
            $image_name = $this->uploadImage($post->getSlug());
            if ($image_name) {
                $post->setImageUrl($image_name);
            }

            $postID = $postManager->save($post);
            if (!is_int($postID)) {
                $this->app->setFlash(
                    'error',
                    [
                        'title' => 'Error',
                        'content' => $postID,
                    ]
                );
            } else {
                $this->app->setFlash(
                    'success',
                    [
                        'title' => 'Post created',
                        'content' => 'The new post has been created',
                    ]
                );

                $response->redirect('/admin/post/add/');
            }
        }
        $postManager = $this->managers->getManagerOf('Post');
        $categories  = $postManager->getCategories();
        $this->page->addVar('categories', $categories);
        $this->page->addVar('editPost', $post);
    }

    /**
     * @param $name
     * @return bool|string
     */
    private function uploadImage($name)
    {
        // Image not mandatory
        if (!$this->getApp()->getHttpRequest()->fileExists('blog_image') or
            $this->getApp()->getHttpRequest()->getFileData('blog_image', 'size') == 0
        ) {
            return false;
        }

        $imagine    = new \Imagine\Imagick\Imagine();
        $file_src   = $this->getApp()->getHttpRequest()->getFileData('blog_image', 'tmp_name');
        $target_dir = APP_DIR."Public/img/post/";

        try {
            $image = $imagine->open($file_src);

            // Convert image to 16.9 ratio
            $width  = 1400;
            $height = 788;
            $ratio  = $image->getSize()->getWidth() / $image->getSize()->getHeight();

            if ($width / $height > $ratio) {
                $width = floor($height * $ratio);
            } else {
                $height = floor($width / $ratio);
            }

            $image->resize(new Box($width, $height))->save($target_dir.$name.IMG_EXT);
            $image->resize(new Box(212, 119))->save($target_dir.$name.'_thumb'.IMG_EXT);
        } catch (\Exception $exceptione) {
            $this->app->setFlash(
                'error',
                [
                    'title' => 'Image Upload error',
                    'content' => $exceptione->getMessage(),
                ]
            );
        }

        return $name.IMG_EXT;
    }

    /**
     * @param HTTPRequest  $request
     * @param HTTPResponse $response
     */
    public function executeAddCategory(HTTPRequest $request, HTTPResponse $response)
    {
        $postManager = $this->managers->getManagerOf('Post');

        if ($request->postExists('category_name')) {
            if ($this->formManager->compareCsrfToken()) {
                $categoryName = $request->getDataPost('category_name');
                $slug         = Service::slugIt($categoryName);
                $result       = $postManager->addCategory($categoryName, $slug);
                if (is_int($result)) {
                    $this->page->addVar(
                        'response',
                        $response->setJson(
                            [
                                'response' => 'success',
                                'message' => 'New category added.',
                                'category' => ['id' => $result, 'name' => $categoryName],
                            ]
                        )
                    );
                } else {
                    $this->page->addVar(
                        'response',
                        $response->setJson(['response' => 'error', 'message' => $result])
                    );
                }
            } else {
                $this->page->addVar(
                    'response',
                    $response->setJson(['error' => 'error', 'message' => 'The CSRF Token is not valid'])
                );
            }
        }

        $this->page->setContentFile('ajax.twig');
    }

    /**
     * @param HTTPRequest  $request
     * @param HTTPResponse $response
     */
    public function executeEdit(HTTPRequest $request, HTTPResponse $response)
    {
        $this->adminOnly();

        $postManager = $this->managers->getManagerOf('Post');
        $userManager = $this->managers->getManagerOf('User');

        if ($request->postExists('edit_post') and ($request->getDataPost('edit_post') === $request->getDataGet('id'))) {
            if ($this->formManager->compareCsrfToken()) {
                $post = $postManager->getUnique($request->getDataPost('edit_post'));
                $user = $userManager->getUnique($request->getSession('UserAuth'));

                $old_slug = $post->getSlug();

                $post->setCategoryId($request->getDataPost('category_id'));
                $post->setTitle($request->getDataPost('title'));
                $post->setAbstractContent($request->getDataPost('abstract_content'));
                $post->setContent($request->getDataPost('content'));
                $post->setEditedBy($user->getId());
                $post->setDateEdit('now');
                $post->setSlug($request->getDataPost('title'));

                $new_slug = $post->getSlug();

                // Rename Image if title/slug has changed
                if ($old_slug != $new_slug and $this->getApp()->getHttpRequest()->getFileData(
                    'blog_image',
                    'size'
                ) == 0) {
                    $image_name = $this->setImageName($old_slug, $new_slug);
                    var_dump($image_name);

                    $post->setImageUrl($image_name);
                }

                $active = $request->postExists('active') ? 1 : 0;
                $post->setActive($active);
                $image_name = $this->uploadImage($post->getSlug());

                if ($image_name) {
                    $post->setImageUrl($image_name);
                }
                $result = $postManager->save($post);
                if ($result == 1) {
                    $this->app->setFlash(
                        'success',
                        [
                            'title' => 'Success',
                            'content' => 'The post has been saved',
                        ]
                    );

                    $response->redirect('/admin/post/edit/'.$request->getDataPost('edit_post').'/');
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
        $categories = $postManager->getCategories();
        $post       = $postManager->getUnique($request->getDataGet('id'));

        if (is_object($post) and $post->getId()) {
            $image = '/img/post/'.$post->getSlug().'_thumb'.IMG_EXT;
            if (file_exists(APP_DIR.'Public/'.$image)) {
                $this->page->addVar('image', $image);
            }
            $this->page->addVar('categories', $categories);
            $this->page->addVar('editPost', $post);
        } else {
            $this->app->setFlash('error', ['content' => 'error']);
        }
    }

    /**
     * @param $old_name
     * @param $new_name
     * @return string
     */
    private function setImageName($old_name, $new_name)
    {
        $imagine    = new \Imagine\Imagick\Imagine();
        $target_dir = APP_DIR."Public/img/post/";

        try {
            $image = $imagine->open($target_dir.$old_name.IMG_EXT);
            $thumb = $imagine->open($target_dir.$old_name.'_thumb'.IMG_EXT);

            $image->save($target_dir.$new_name.IMG_EXT);
            $thumb->save($target_dir.$new_name.'_thumb'.IMG_EXT);

            unlink($target_dir.$old_name.IMG_EXT);
            unlink($target_dir.$old_name.'_thumb'.IMG_EXT);
        } catch (\Exception $exceptione) {
            $this->app->setFlash(
                'error',
                [
                    'title' => 'Image Upload error',
                    'content' => $exceptione->getMessage(),
                ]
            );
        }

        return $new_name.IMG_EXT;
    }
}
