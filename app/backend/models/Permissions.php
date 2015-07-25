<?php
/**
 * Created by PhpStorm.
 * User: hieutrieu
 * Date: 6/23/15
 * Time: 09:31
 */

namespace Books\Backend\Models;

use Books\Backend\Models\Base\ModelBase;
use Phalcon\Mvc\Collection;
use Phalcon\Mvc\Model\Validator\PresenceOf;

class Permissions extends Collection {
    /**
     *
     * @var integer
     */
    public $role_id;

    /**
     *
     * @var string
     */
    public $controller;

    /**
     *
     * @var string
     */
    public $action;

    /**
     * Validations and business logic
     *
     * @return boolean
     */
    public function validation() {
        $this->validate(
            new PresenceOf(
                array(
                    "field"   => "role_id",
                    "message" => "The name is required"
                )
            )
        );
        $this->validate(
            new PresenceOf(
                array(
                    "field"   => "controller",
                    "message" => "The controller is required"
                )
            )
        );
        $this->validate(
            new PresenceOf(
                array(
                    "field"   => "action",
                    "message" => "The action is required"
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
        return 'permissions';
    }
}