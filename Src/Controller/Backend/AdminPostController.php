<?php

namespace Controller\Backend;

use Core\AbstractController;
use Core\FormManager;
use Core\HTTPRequest;
use Core\HTTPResponse;
use Entity\Post;
use Service\Service;

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

    public function executeDelete(HTTPRequest $request, HTTPResponse $response)
    {
        $form = new FormManager();
        if ($request->getExists('id') and $form->compareCsrfToken()) {
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

    public function executeAdd(HTTPRequest $request, HTTPResponse $response)
    {
        $this->adminOnly();

        $postManager = $this->managers->getManagerOf('Post');
        $userManager = $this->managers->getManagerOf('User');
        $post        = new Post();
        $form = new FormManager();

        if ($request->postExists('add_post') and $form->compareCsrfToken()) {
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

    private function uploadImage($name)
    {
        // Image not mandatory
        if (!$_FILES["blog_image"]["name"]) {
            return false;
        }

        $file_src      = $_FILES["blog_image"]["tmp_name"];
        $target_dir    = APP_DIR."/Public/img/post/";
        $target_file   = $target_dir.basename($_FILES["blog_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $target_final  = $target_dir.$name.'.jpg';

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["blog_image"]["tmp_name"]);
        if ($check === false) {
            $this->app->setFlash(
                'error',
                [
                    'title' => 'Image Upload error',
                    'content' => 'Your file is not an image',
                ]
            );

            return false;
        }

        // Check file size
        if ($_FILES["blog_image"]["size"] > 500000) {
            $this->app->setFlash(
                'error',
                [
                    'title' => 'Image Upload error',
                    'content' => 'Your image is to large ('.$_FILES["blog_image"]["size"].')',
                ]
            );

            return false;
        }

        // Allow only image format
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            $this->app->setFlash(
                'error',
                [
                    'title' => 'Image Upload error',
                    'content' => 'Sorry, only JPG, JPEG, PNG files are allowed.',
                ]
            );

            return false;
        }

        // Convert image to jpg and 16.9 ratio
        $width  = 1400;
        $height = 788;
        list($img_width, $img_height, $type) = getimagesize($file_src);
        $ratio = $img_width / $img_height;

        if ($width / $height > $ratio) {
            $width = floor($height * $ratio);
        } else {
            $height = floor($width / $ratio);
        }

        switch ($type) {
            case 1:
                $img_src = imagecreatefromgif($file_src);
                break;
            case 2:
                $img_src = imagecreatefromjpeg($file_src);
                break;
            case 3:
                $img_src = imagecreatefrompng($file_src);
                break;
        }

        $new_image = imagecreatetruecolor($width, $height);
        imagecopyresampled($new_image, $img_src, 0, 0, 0, 0, $width, $height, $img_width, $img_height);

        if (!imagejpeg($new_image, $target_final)) {
            $this->app->setFlash(
                'error',
                [
                    'title' => 'Image Upload error',
                    'content' => 'Unexpected error. Your file has not been uploaded.',
                ]
            );

            return false;
        }

        return $name.'.jpg';
    }

    public function executeAddCategory(HTTPRequest $request, HTTPResponse $response)
    {
        $postManager = $this->managers->getManagerOf('Post');
        $form = new FormManager();

        if ($request->postExists('category_name')) {
            if ($form->compareCsrfToken()) {
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

    public function executeEdit(HTTPRequest $request, HTTPResponse $response)
    {

        $this->adminOnly();

        $postManager = $this->managers->getManagerOf('Post');
        $userManager = $this->managers->getManagerOf('User');
        $form = new FormManager();

        if ($request->postExists('edit_post') and ($request->getDataPost('edit_post') === $request->getDataGet('id'))) {
            if ($form->compareCsrfToken()) {
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

                // Rename Image if title/slug has changer
                if ($old_slug != $new_slug and !$_FILES["blog_image"]["name"]) {
                    $image_name = $this->setImageName($post->getImageUrl(), $new_slug);
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
                $form->killCsrfToken();
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
            $image = '/img/post/'.$post->getSlug().'.jpg';
            if (file_exists(APP_DIR.'Public/'.$image)) {
                $this->page->addVar('image', $image);
            }
            $this->page->addVar('categories', $categories);
            $this->page->addVar('editPost', $post);
        } else {
            $this->app->setFlash('error', ['content' => 'error']);
        }
    }

    private function setImageName($old_name, $new_name)
    {
        $target   = APP_DIR.'/Public/img/post/';
        $new_name = $new_name.'.jpg';
        if (file_exists($target.$old_name)) {
            rename($target.$old_name, $target.$new_name);
        }

        return $new_name;
    }
}
