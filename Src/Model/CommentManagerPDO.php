<?php

namespace Model;

use Entity\Comment;

class CommentManagerPDO extends CommentManager
{
    public function count()
    {
        return $this->dao->query('SELECT COUNT(*) FROM blog_comment')->fetchColumn();
    }

    public function delete($id)
    {
        $this->dao->exec('DELETE FROM blog_comment WHERE id = '.(int)$id);
    }

    public function getList($start = -1, $limit = -1, $status = null, $postId = null, $authorId = null)
    {
        $sql = "SELECT c.*, post.title AS 'post',
                IF(author.show_full_name, CONCAT(author.firstname, ' ', author.lastname), author.nickname) AS 'author' 
                FROM blog_comment AS c 
                LEFT JOIN blog_user AS author ON c.user_id = author.id 
                LEFT JOIN blog_post AS post ON c.post_id = post.id ";

        if ($status !== null) {
            $sql .= " WHERE c.status = '$status' ";
        }

        if ($postId and is_int($postId)) {
            $sql .= " AND c.post_id = $postId ";
        }
        if ($authorId and is_int($authorId)) {
            $sql .= " AND c.user_id = $authorId ";
        }

        $sql .= " ORDER BY UNIX_TIMESTAMP(c.date_add) DESC ";

        if ($start != -1 || $limit != -1) {
            $sql .= ' LIMIT '.(int)$limit.' OFFSET '.(int)$start;
        }

        $request = $this->dao->query($sql);
        $request->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment');

        $listComments = $request->fetchAll();

        $request->closeCursor();

        return $listComments;
    }

    public function getUnique($id)
    {
        $request = $this->dao->prepare(
            "SELECT c.*, post.title AS 'post',
                IF(author.show_full_name, CONCAT(author.firstname, ' ', author.lastname), author.nickname) AS 'author' 
                FROM blog_comment AS c 
                LEFT JOIN blog_user AS author ON c.user_id = author.id 
                LEFT JOIN blog_post AS post ON c.post_id = post.id
                WHERE c.id = :id"
        );
        $request->bindValue(':id', (int)$id, \PDO::PARAM_INT);
        $request->execute();
        $request->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\Comment');

        if ($comment = $request->fetch()) {
            return $comment;
        }

        return null;
    }

    protected function add(Comment $comment)
    {
        $request = $this->dao->prepare(
            'INSERT INTO blog_comment SET post_id = :post_id,
                                parent_id = :parent_id,
                                user_id = :user_id,
                                message = :message,
                                date_add = :date_add,
                                date_edit = :date_edit,
                                status = :status'
        );

        $request->bindValue(':post_id', $comment->getPostId());
        $request->bindValue(':parent_id', $comment->getParentId());
        $request->bindValue(':user_id', $comment->getUserId());
        $request->bindValue(':message', $comment->getMessage());
        $request->bindValue(':date_add', $comment->getDateAdd());
        $request->bindValue(':date_edit', $comment->getDateEdit());
        $request->bindValue(':status', $comment->getStatus());

        try {
            $request->execute();

            return (int)$this->dao->lastInsertId();
        } catch (\PDOException $e) {
            return $e->getMessage();
        }
    }

    protected function modify(Comment $comment)
    {
        $request = $this->dao->prepare(
            'UPDATE blog_comment SET post_id = :post_id,
                                user_id = :user_id,
                                message = :message,
                                date_add = :date_add,
                                date_edit = :date_edit,
                                status = :status
             WHERE id = :id'
        );
        $request->bindValue(':post_id', $comment->getPostId());
        $request->bindValue(':user_id', $comment->getUserId());
        $request->bindValue(':message', $comment->getMessage());
        $request->bindValue(':date_add', $comment->getDateAdd());
        $request->bindValue(':date_edit', $comment->getDateEdit());
        $request->bindValue(':status', $comment->getStatus());

        $request->bindValue(':id', $comment->getId(), \PDO::PARAM_INT);

        try {
            return $request->execute();
        } catch (\PDOException $e) {
            return $e->getMessage();
        }

    }

}
