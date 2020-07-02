<?php

namespace Model;

use Entity\Post;

class PostManagerPDO extends PostManager
{
    public function count()
    {
        return $this->dao->query('SELECT COUNT(*) FROM post')->fetchColumn();
    }

    public function getCategories()
    {
        return $this->dao->query('SELECT * FROM post_category')->fetchAll();
    }

    public function delete($id)
    {
        $this->dao->exec('DELETE FROM post WHERE id = '.(int)$id);
    }

    public function getList($start = -1, $limit = -1, $categoryId = null)
    {
        $sql = "SELECT p.*, pc.category_name, pc.category_slug, COUNT(c.id) AS 'nb_comments',
                IF(author.show_full_name, CONCAT(author.firstname, ' ', author.lastname), author.nickname) AS 'author', 
                IF(editor.show_full_name, CONCAT(editor.firstname, ' ', editor.lastname), editor.nickname) AS 'editor' 
                FROM post AS p 
                LEFT JOIN post_category AS pc ON p.category_id = pc.id
                LEFT JOIN comment AS c ON p.id = c.post_id AND c.status = 'APPROVED'
                LEFT JOIN user AS author ON p.created_by = author.id 
                LEFT JOIN user AS editor ON p.edited_by = editor.id ";

        if (is_int($categoryId)) {
            $sql .= " WHERE p.category_id = $categoryId ";
        }
        $sql .= ' GROUP BY p.id';
        $sql .= " ORDER BY UNIX_TIMESTAMP(p.date_add) DESC ";

        if ($start != -1 || $limit != -1) {
            $sql .= ' LIMIT '.(int)$limit.' OFFSET '.(int)$start;
        }


        $request = $this->dao->query($sql);
        $request->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Post');

        $listPosts = $request->fetchAll();

        $request->closeCursor();

        return $listPosts;
    }

    public function getUnique($id)
    {
        $request = $this->dao->prepare(
            "SELECT p.*, pc.category_name, pc.category_slug, 
                IF(author.show_full_name, CONCAT(author.firstname, ' ', author.lastname), author.nickname) AS 'author', 
                IF(editor.show_full_name, CONCAT(editor.firstname, ' ', editor.lastname), editor.nickname) AS 'editor' 
                FROM post AS p 
                LEFT JOIN post_category AS pc ON p.category_id = pc.id
                LEFT JOIN user AS author ON p.created_by = author.id 
                LEFT JOIN user AS editor ON p.edited_by = editor.id 
                WHERE p.id = :id"
        );
        $request->bindValue(':id', (int)$id, \PDO::PARAM_INT);
        $request->execute();
        $request->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Post');

        if ($post = $request->fetch()) {
            return $post;
        }

        return null;
    }

    public function getCategoryBySlug($slug)
    {
        $request = $this->dao->prepare(
            "SELECT * FROM post_category WHERE category_slug = :slug"
        );
        $request->bindValue(':slug', $slug, \PDO::PARAM_STR);
        $request->execute();

        if ($category = $request->fetch(\PDO::FETCH_OBJ)) {
            return $category;
        }

        return null;
    }

    public function getBySlug($slug)
    {
        $request = $this->dao->prepare(
            "SELECT p.*, pc.category_name, pc.category_slug, 
                IF(author.show_full_name, CONCAT(author.firstname, ' ', author.lastname), author.nickname) AS 'author', 
                IF(editor.show_full_name, CONCAT(editor.firstname, ' ', editor.lastname), editor.nickname) AS 'editor' 
                FROM post AS p 
                LEFT JOIN post_category AS pc ON p.category_id = pc.id
                LEFT JOIN user AS author ON p.created_by = author.id 
                LEFT JOIN user AS editor ON p.edited_by = editor.id 
                WHERE p.slug = :slug"
        );
        $request->bindValue(':slug', $slug, \PDO::PARAM_STR);
        $request->execute();
        $request->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Post');

        if ($post = $request->fetch()) {
            return $post;
        }

        return null;
    }

    public function addCategory($name, $slug)
    {
        $request = $this->dao->prepare('INSERT INTO post_category 
                                            SET category_name = :category_name, category_slug = :category_slug ');
        $request->bindValue(':category_name', $name);
        $request->bindValue(':category_slug', $slug);

        try {
            $request->execute();

            return (int)$this->dao->lastInsertId();
        } catch (\PDOException $e) {
            return $e->getMessage();
        }

    }

    protected function add(Post $post)
    {
        $request = $this->dao->prepare(
            'INSERT INTO post SET active = :active,
                                category_id = :category_id,
                                title = :title,
                                slug = :slug,
                                abstract_content = :abstract_content,
                                content = :content,  
                                image_url = :image_url,  
                                date_add = :date_add,
                                created_by = :created_by'
        );

        $request->bindValue(':active', $post->getActive());
        $request->bindValue(':category_id', $post->getCategoryId());
        $request->bindValue(':title', $post->getTitle());
        $request->bindValue(':slug', $post->getSlug());
        $request->bindValue(':abstract_content', $post->getAbstractContent());
        $request->bindValue(':content', $post->getContent());
        $request->bindValue(':image_url', $post->getImageUrl());
        $request->bindValue(':date_add', $post->getDateAdd());
        $request->bindValue(':created_by', $post->getCreatedBy());

        try {
            $request->execute();

            return (int)$this->dao->lastInsertId();
        } catch (\PDOException $e) {
            return $e->getMessage();
        }
    }

    protected function modify(Post $post)
    {
        $request = $this->dao->prepare(
            'UPDATE post SET active = :active,
                                category_id = :category_id,
                                title = :title,
                                slug = :slug,
                                abstract_content = :abstract_content,
                                content = :content,  
                                image_url = :image_url,  
                                date_edit = :date_edit,
                                edited_by = :edited_by
             WHERE id = :id'
        );
        $request->bindValue(':active', $post->getActive());
        $request->bindValue(':category_id', $post->getCategoryId());
        $request->bindValue(':title', $post->getTitle());
        $request->bindValue(':slug', $post->getSlug());
        $request->bindValue(':abstract_content', $post->getAbstractContent());
        $request->bindValue(':content', $post->getContent());
        $request->bindValue(':image_url', $post->getImageUrl());
        $request->bindValue(':date_edit', $post->getDateEdit());
        $request->bindValue(':edited_by', $post->getEditedBy());

        $request->bindValue(':id', $post->getId(), \PDO::PARAM_INT);

        try {
            return $request->execute();
        } catch (\PDOException $e) {
            return $e->getMessage();
        }

    }


}