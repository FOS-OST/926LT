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
use stdClass;

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
    public $active=1;
    /**
     * @var integer
     */
    public $allowPublish=0;
    public $allowMenu=0;
    public $allowUser=0;
    public $allowBook=1;

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
        $options[0] = 'Không cấp nhóm quyền';
        foreach($roles as $role) {
            $options[$role->getId()->{'$id'}] = $role->name;
        }
        return $options;
    }

    public static function composePermission($allowPublish, $allowMenu, $allowUser){
        $permission = new stdClass();
        $permission->allowPublish = intval($allowPublish);
        $permission->allowMenu = intval($allowMenu);
        $permission->allowUser = intval($allowUser);

        return $permission;
    }
}
