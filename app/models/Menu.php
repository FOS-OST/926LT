<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 6/23/15
 * Time: 09:31
 */

namespace Books\App\Models;


use MongoId;
use Phalcon\Mvc\Collection;

class Menu extends ModelBase {
    /**
     * @var MongoId
     */
    private $id;
    /**
     * @var String
     */
    private $name;
    /**
     * @var int
     */
    private $index=-1;
    /**
     * @var bool
     */
    private $status=true;// show or hide status

    public function getSource()
    {
        return "menu";
    }


}