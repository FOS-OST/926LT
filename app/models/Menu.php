<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 6/23/15
 * Time: 09:31
 */

namespace Books\App\Models;


use MongoId;
use MongoRegex;
use Phalcon\Mvc\Collection;

class Menu extends ModelBase {
    const TYPE_HORIZONTAL = 0;
    const TYPE_VERTICAL = 1;
    /**
     * @var String
     */
    public $name;
    /**
     * @var int
     */
    public $order = 0;
    /**
     * @var bool
     */
    public $type = false; // false: horizontal - true: vertical
    /**
     * @var bool
     */
    public $status=true; // show or hide status
    /**
     * @var bool
     */
    public $first_load = false; // show or hide status
    public $banner = '';
    public $icon = '';

    /**
     * @var array
     */
    public $categories=array(); // show or hide status

    public function getSource(){
        return "menus";
    }

    public static function buildConditions($search){
        $searchRegex = new MongoRegex("/$search/i");
        $conditions = array('name' => $searchRegex);
        return $conditions;
    }
}