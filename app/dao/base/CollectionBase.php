<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 4/23/15
 * Time: 17:30
 */

namespace App\DAO\base;

use App\Libraries\DBConnection;
use MongoDB;
use MongoCursor;
use MongoId;

class CollectionBase extends \MongoCollection {
    protected $collection_name;
    public function __construct($name)
    {
        $this->collection_name=$name;
        $db=DBConnection::getInstance()->getMongoDB();
        parent::__construct($db, $name); // TODO: Change the autogenerated stub
    }
    public static function formatObject() {
        $new_obj=new \stdClass();
        $new_obj->created_at=new \MongoDate();
        $new_obj->updated_at=new \MongoDate();
        return $new_obj;
    }

    /**
     * @param MongoId $id
     * @return MongoCursor
     */
    public function findObjectsById($id) {
        return $this->find(array('_id'=>$id));
    }
    /**
     * @param MongoId $id
     * @return array|null
     */
    public function findOneObjectById($id) {
        return $this->findOne(array('_id'=>$id));
    }
}