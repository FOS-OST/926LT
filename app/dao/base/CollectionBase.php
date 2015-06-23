<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 4/23/15
 * Time: 17:30
 */

namespace App\DAO\base;

use Phalcon\Mvc\Collection;

class CollectionBase extends Collection {

    public function formatObject() {
        $new_obj=new \stdClass();
        $new_obj->created_at=new \MongoDate();
        $new_obj->updated_at=new \MongoDate();
        return $new_obj;
    }
}