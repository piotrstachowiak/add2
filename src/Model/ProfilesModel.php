<?php

namespace Model;

use Doctrine\DBAL\DBALException;
use Silex\Application;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class ProfilesModel
{
    protected $db;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    public function getUsersAds($id)
    {
        if (($id != '') && ctype_digit((string)$id)) {
            $query = 'SELECT id, category_id, title, text FROM ad WHERE user_id= :id';
            $statement = $this->db->prepare($query);
            $statement->bindValue('id', $id, \PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return !$result ? array() : $result;
        } else {
            return array();
        }
    }

    public function updateUser($user)
    {
        if (isset($user['id'])
        && ($user['id'] != '')
        && ctype_digit((string)$user['id'])) {
            $id = $user['id'];
            unset($user['id']);
            return $this->db->update('users', $user, array('id' => $id));
        }
    }

    public function updatePassword($data)
    {
        if (isset($data['id'])
        && ($data['id'] != '')
        && ctype_digit((string)$data['id'])) {
            $query = 'UPDATE users SET password= :password WHERE id= :id';
            $id = $data['id'];
            $password = $data['new_password'];
            $statement = $this->db->prepare($query);
            $statement->bindValue('id', $id, \PDO::PARAM_INT);
            $statement->bindValue('password', $password, \PDO::PARAM_INT);
            $statement->execute();
            return 'ok';
        } else {
            return array();
        }
    }
}