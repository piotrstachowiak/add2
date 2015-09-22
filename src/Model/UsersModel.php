<?php

namespace Model;

use Doctrine\DBAL\DBALException;
use Silex\Application;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UsersModel
{

    protected $db;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    public function getAll()
    {
        $query = 'SELECT id, login, mail FROM users';
        $result = $this->db->fetchAll($query);
        return !$result ? array() : $result;
    }

    public function loadUserByLogin($login)
    {
        $user = $this->getUserByLogin($login);
        if (!$user || !count($user)) {
            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $login)
            );
        }
        $roles = $this->getUserRoles($user['id']);
        if (!$roles || !count($roles)) {
            throw new UsernameNotFoundException(
                sprintf('Username "%s" does not exist.', $login)
            );
        }
        return array(
            'login' => $user['login'],
            'password' => $user['password'],
            'roles' => $roles
        );
    }

    public function getUserByLogin($login)
    {
        try {
            $query = '
              SELECT
                `id`, `login`, `password`, `role_id`
              FROM
                `users`
              WHERE
                `login` = :login
            ';
            $statement = $this->db->prepare($query);
            $statement->bindValue('login', $login, \PDO::PARAM_STR);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return !$result ? array() : current($result);
        } catch (\PDOException $e) {
            return array();
        }
    }

    public function getUserRoles($userId)
    {
        $roles = array();
        try {
            $query = '
                SELECT
                    `roles`.`name` as `role`
                FROM
                    `users`
                INNER JOIN
                    `roles`
                ON `users`.`role_id` = `roles`.`id`
                WHERE
                    `users`.`id` = :user_id
                ';
            $statement = $this->db->prepare($query);
            $statement->bindValue('user_id', $userId, \PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            if ($result && count($result)) {
                $result = current($result);
                $roles[] = $result['role'];
            }
            return $roles;
        } catch (\PDOException $e) {
            return $roles;
        }
    }

    public function getUser($id)
    {
        if (($id != '') && ctype_digit((string)$id)) {
            $query = 'SELECT id, login, role_id, mail FROM users WHERE id= :id';
            $statement = $this->db->prepare($query);
            $statement->bindValue('id', $id, \PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return !$result ? array() : current($result);
        } else {
            return array();
        }
    }


    public function getRoles()
    {
        $query = 'SELECT id, name FROM roles';
        $result = $this->db->fetchAll($query);
        return !$result ? array() : $result;
    }

    public function saveUser($user)
    {
        if (isset($user['id'])
        && ($user['id'] != '')
        && ctype_digit((string)$user['id'])) {
            $id = $user['id'];
            unset($user['id']);
            return $this->db->update('users', $user, array('id' => $id));
        } else {
            return $this->db->insert('users', $user);
        }
    }

    public function deleteUser($id)
    {
        if (($id != '') && ctype_digit((string)$id)) {
            $query = 'DELETE FROM users WHERE id= :id';
            $statement = $this->db->prepare($query);
            $statement->bindValue('id', $id, \PDO::PARAM_INT);
            $statement->execute();
            return true;
        } else {
            return array();
        }
    }

    public function getCurrentUser(Application $app)
    {
        $token = array();
        $token = $app['security.token_storage']->getToken();
        $login = $token->getUser()->getUsername();
        $user = $this->getUserByLogin($login);
        return $user;
    }

    public function getCurrentUserId(Application $app)
    {
        $user = $this->getCurrentUser($app);
        $id = $user['id'];
        return $id;
    }
}