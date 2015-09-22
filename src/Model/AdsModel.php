<?php

namespace Model;

use Silex\Application;

class AdsModel
{
    protected $db;

    public function __construct(Application $app)
    {
        $this->db = $app['db'];
    }

    public function getAll()
    {
        $query = 'SELECT id, category_id, title, text FROM ad';
        $result = $this->db->fetchAll($query);
        return !$result ? array() : $result;
    }

    public function getAd($id)
    {
        if (($id != '') && ctype_digit((string)$id)) {
            $query = 'SELECT id, category_id, title, user_id, text FROM ad WHERE id= :id';
            $statement = $this->db->prepare($query);
            $statement->bindValue('id', $id, \PDO::PARAM_INT);
            $statement->execute();
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
            return !$result ? array() : current($result);
        } else {
            return array();
        }
    }

    public function saveAd($ad)
    {
        if (isset($ad['id'])
        && ($ad['id'] != '')
        && ctype_digit((string)$ad['id'])) {
            $id = $ad['id'];
            unset($ad['id']);
            return $this->db->update('ad', $ad, array('id' => $id));
        } else {
            return $this->db->insert('ad', $ad);
        }
    }

    public function deleteAd($id)
    {
        if (($id != '') && ctype_digit((string)$id)) {
            $query = 'DELETE FROM ad WHERE id= :id';
            $statement = $this->db->prepare($query);
            $statement->bindValue('id', $id, \PDO::PARAM_INT);
            $statement->execute();
            return true;
        } else {
            return array();
        }
    }
}