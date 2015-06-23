<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 6/23/15
 * Time: 09:32
 */

namespace Books\App\Models;


use MongoDate;
use Phalcon\Mvc\Collection;

class ModelBase extends Collection {

    /**
     * @var MongoDate
     */
    protected $created_at;
    /**
     * @var MongoDate
     */
    protected $updated_at;

    public function beforeCreate()
    {
        //Set the creation date
        $this->updated_at = new \MongoDate();
        $this->created_at = new \MongoDate();
    }

    public function beforeUpdate()
    {
        //Set the modification date
        $this->updated_at = new \MongoDate();
    }

}