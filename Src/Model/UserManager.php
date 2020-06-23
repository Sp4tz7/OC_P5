<?php

namespace Model;

use Core\Manager;
use Entity\User;

abstract class UserManager extends Manager
{
    /**
     * Méthode permettant d'enregistrer une news.
     *
     * @param $news News la news à enregistrer
     *
     * @return void
     * @see self::modify()
     * @see self::add()
     */
    public function save(User $user)
    {

        $message = $user->isValid();
        if ($message == 'PASS') {
            return $user->isNew() ? $this->add($user) : $this->modify($user);
        } else {
            return  $message;
        }
    }

    /**
     * Méthode permettant d'ajouter une news.
     *
     * @param $news News La news à ajouter
     *
     * @return void
     */
    abstract protected function add(User $news);

    /**
     * Méthode permettant de modifier une news.
     *
     * @param $news news la news à modifier
     *
     * @return void
     */
    abstract protected function modify(User $user);

    /**
     * Méthode renvoyant le nombre de news total.
     * @return int
     */
    abstract public function count();

    /**
     * Méthode permettant de supprimer une news.
     *
     * @param $id int L'identifiant de la news à supprimer
     *
     * @return void
     */
    abstract public function delete($id);

    /**
     * Méthode retournant une liste de news demandée.
     *
     * @param $debut  int La première news à sélectionner
     * @param $limite int Le nombre de news à sélectionner
     *
     * @return array La liste des news. Chaque entrée est une instance de News.
     */
    abstract public function getList($debut = -1, $limite = -1);

    /**
     * Méthode retournant une news précise.
     *
     * @param $id int L'identifiant de la news à récupérer
     *
     * @return News La news demandée
     */
    abstract public function getUnique($id);

}