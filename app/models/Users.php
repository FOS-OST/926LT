<?php
namespace Books\App\Models;
use Phalcon\Mvc\Model\Validator\Email as Email;
use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Collection;
use MongoRegex;
class Users extends ModelBase
{
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
     * @var integer
     */
    public $amount;

    /**
     * @var string;
     */
    public $role_id;

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
        return 'users';
    }

    static function buildConditions($search){
        $searchRegex = new MongoRegex("/$search/i");
        $conditions = array(
            '$or' => array(
                array('name' => $searchRegex),
                array('email' => $searchRegex),
                array('phone' => $searchRegex),
            )
        );
        return $conditions;
    }
}
