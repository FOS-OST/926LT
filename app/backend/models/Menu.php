<?php
/**
 * Created by PhpStorm.
 * User: hieutrieu
 * Date: 6/23/15
 * Time: 09:31
 */

namespace Books\Backend\Models;

use Books\Backend\Models\Base\ModelBase;
use MongoId;
use MongoRegex;
use Phalcon\Mvc\Collection;
use Phalcon\Mvc\Model\Validator\PresenceOf;

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
    public $type = 1; // false: horizontal - true: vertical
    /**
     * @var string
     */
    public $classification;
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

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation() {
        $this->validate(
            new PresenceOf(
                array(
                    "field"   => "name",
                    "message" => "The name of menu is required",
                    'required' => true,
                )
            )
        );
        if ($this->validationHasFailed() == true) {
            return false;
        }

        return true;
    }

    public static function buildConditions($search){
        $searchRegex = new MongoRegex("/$search/i");
        $conditions = array(
            'status' => array('$gt' => -1),
            '$or' => array(
                array('name' => $searchRegex),
            )
        );
        return $conditions;
    }
}