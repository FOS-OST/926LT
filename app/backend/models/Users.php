<?php
/**
 * Created by PhpStorm.
 * User: HIEUTRIEU
 * Date: 7/25/2015
 * Time: 2:33 PM
 */

namespace Books\Backend\Models;

use Books\Backend\Models\Base\ModelBase;
use Phalcon\Mvc\Model\Validator\Email as Email;
use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Collection;
use MongoRegex;

class Users extends ModelBase {
    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $password;

    /**
     *
     * @var string
     */
    public $avatar;

    /**
     *
     * @var string
     */
    public $device_token;

    /**
     *
     * @var string
     */
    public $access_token;

    /**
     *
     * @var string
     */
    public $remember_token;

    /**
     *
     * @var string
     */
    public $phone;

    /**
     *
     * @var double
     */
    public $amount = 0;
    /**
     * @var double
     */
    public $total = 0;

    /**
     * @var string;
     */
    public $role_id;
    public $role_name;

    /**
     *
     * @var integer
     */
    public $active;

    /**
     *
     * @var integer
     */
    public $status;

    /**
     * @var array
     */
    public $books = array();

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation() {
        $this->validate(
            new Email(
                array(
                    'field'    => 'email',
                    'required' => true,
                )
            )
        );

        $this->validate(
            new PresenceOf(
                array(
                    "field"   => "name",
                    "message" => "The name is required",
                    'required' => true,
                )
            )
        );

        $this->validate(
            new PresenceOf(
                array(
                    "field"   => "password",
                    "message" => "The password is required",
                    'required' => true,
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
        return 'users';
    }

    static function buildConditions($search, $adminRole, $aclRoles){
        $searchRegex = new MongoRegex("/$search/i");
        $conditions = array(
            '$or' => array(
                array('name' => $searchRegex),
                array('email' => $searchRegex),
                array('phone' => $searchRegex),
            )
        );
        if(!in_array($adminRole['name'],$aclRoles['private'])) {
            // Filter public role to assign for this role
            $role = Roles::findFirst(array('conditions' => array('name' => array('$in' => $aclRoles['public']))));
            $conditions['role_id'] = $role->getId()->{'$id'};
        }

        return $conditions;
    }

}
