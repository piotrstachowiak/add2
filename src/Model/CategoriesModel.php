<?php

namespace Model;

use Silex\Application;

class CategoriesModel
{
    protected $db;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    public function getCategory($id)
    {
        if (($id != '') && ctype_digit((string)$id)) {
            $query = 'SELECT name FROM category WHERE id= :id';
            $statement = $this->db->prepare($query);
            $statement->bindValue('id', $id, \PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return !$result ? array() : current($result);
        } else {
            return array();
        }
    }

    public function getAll()
    {
        $query = 'SELECT id, name FROM category';
        $result = $this->db->fetchAll($query);
        return !$result ? array() : $result;
    }
}