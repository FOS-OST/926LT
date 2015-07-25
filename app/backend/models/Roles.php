<?php
/**
 * Created by PhpStorm.
 * User: hieutrieu
 * Date: 6/23/15
 * Time: 11:42
 */

namespace Books\Backend\Models;

use Books\Backend\Models\Base\ModelBase;
use MongoRegex;
use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Collection;

class Roles extends ModelBase {
    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var int
     */
    public $active;

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
                    "message" => "The name is required"
                )
            )
        );
        if ($this->validationHasFailed() == true) {
            return false;
        }

        return true;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'roles';
    }

    public static function getRoleOptions(){
        $options = array();
        $roles = self::find(array(
            'conditions' => array('active' => 1)
        ));
        foreach($roles as $role) {
            $options[$role->getId()->{'$id'}] = $role->name;
        }
        return $options;
    }
}
