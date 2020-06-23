<?php

namespace Model;

use Entity\User;

class UserManagerPDO extends UserManager
{
    public function count()
    {
        return $this->dao->query('SELECT COUNT(*) FROM news')->fetchColumn();
    }

    public function delete($id)
    {
        $this->dao->exec('DELETE FROM news WHERE id = '.(int)$id);
    }

    public function getList($start = -1, $limit = -1)
    {
        $sql = 'SELECT * FROM user ORDER BY id, lastname ASC';

        if ($start != -1 || $limit != -1) {
            $sql .= ' LIMIT '.(int)$limit.' OFFSET '.(int)$start;
        }

        $request = $this->dao->query($sql);
        $request->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\User');

        $listeUsers = $request->fetchAll();


        $request->closeCursor();

        return $listeUsers;
    }

    public function getUnique($id)
    {
        $request = $this->dao->prepare(
            'SELECT * FROM user WHERE id = :id'
        );
        $request->bindValue(':id', (int)$id, \PDO::PARAM_INT);
        $request->execute();
        $request->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\User');

        if ($user = $request->fetch()) {
            return $user;
        }

        return null;
    }

    public function getByNicknameOrEmail($nickname)
    {
        $request = $this->dao->prepare('SELECT * FROM user WHERE nickname = :nickname OR email = :nickname');
        $request->bindValue(':nickname', $nickname, \PDO::PARAM_STR);
        $request->execute();

        $request->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\User');

        if ($user = $request->fetch()) {
            return $user;
        }

    }
    public function getByToken($token)
    {
        $date = new \DateTime();
        $request = $this->dao->prepare('SELECT * FROM user WHERE token = :token');

        $request->bindValue(':token', $token, \PDO::PARAM_STR);
        $request->execute();
        $request->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, '\Entity\User');

        if ($user = $request->fetch()) {
            return $user;
        }

    }

    protected function add(User $user)
    {
        $requete = $this->dao->prepare(
            'INSERT INTO user SET active = :active,
                                email = :email,
                                firstname = :firstname,
                                lastname = :lastname,
                                nickname = :nickname,
                                password = :password, 
                                token = :token, 
                                token_validity = :token_validity, 
                                role = :role,
                                send_email_approve = :send_email_approve,
                                send_email_replay = :send_email_replay,
                                show_full_name = :show_full_name'
        );

        $requete->bindValue(':active', $user->getActive());
        $requete->bindValue(':email', $user->getEmail());
        $requete->bindValue(':firstname', $user->getFirstname());
        $requete->bindValue(':lastname', $user->getLastname());
        $requete->bindValue(':nickname', $user->getNickname());
        $requete->bindValue(':password', $user->getPassword());
        $requete->bindValue(':token', $user->getToken());
        $requete->bindValue(':token_validity', $user->getTokenValidity());
        $requete->bindValue(':role', $user->getRole());
        $requete->bindValue(':send_email_approve', $user->getSendEmailApprove());
        $requete->bindValue(':send_email_replay', $user->getSendEmailReplay());
        $requete->bindValue(':show_full_name', $user->getShowFullName());

        try {
            return $requete->execute();
        } catch (\PDOException $e){
            return $e->getMessage();
        }
    }

    protected function modify(User $user)
    {
        $requete = $this->dao->prepare(
            'UPDATE user SET active = :active,
                                email = :email,
                                firstname = :firstname,
                                lastname = :lastname,
                                nickname = :nickname,
                                password = :password, 
                                token = :token, 
                                token_validity = :token_validity, 
                                role = :role,
                                send_email_approve = :send_email_approve,
                                send_email_replay = :send_email_replay,
                                show_full_name = :show_full_name
             WHERE id = :id'
        );
        $requete->bindValue(':active', $user->getActive());
        $requete->bindValue(':email', $user->getEmail());
        $requete->bindValue(':firstname', $user->getFirstname());
        $requete->bindValue(':lastname', $user->getLastname());
        $requete->bindValue(':nickname', $user->getNickname());
        $requete->bindValue(':password', $user->getPassword());
        $requete->bindValue(':token', $user->getToken());
        $requete->bindValue(':token_validity', $user->getTokenValidity());
        $requete->bindValue(':role', $user->getRole());
        $requete->bindValue(':send_email_approve', $user->getSendEmailApprove());
        $requete->bindValue(':send_email_replay', $user->getSendEmailReplay());
        $requete->bindValue(':show_full_name', $user->getShowFullName());
        $requete->bindValue(':id', $user->getId(), \PDO::PARAM_INT);

        try {
            return $requete->execute();
        } catch (\PDOException $e){
            return $e->getMessage();
        }

    }


}