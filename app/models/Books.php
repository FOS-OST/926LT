<?php
namespace Books\Models;
use MongoRegex;
use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Collection;

class Books extends Collection
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
    public $category_id;

    /**
     *
     * @var string
     */
    public $author;

    /**
     *
     * @var string
     */
    public $image;

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var float
     */
    public $price;

    /**
     *
     * @var integer
     */
    public $free;

    /**
     *
     * @var string
     */
    public $created_by;

    /**
     *
     * @var string
     */
    public $modified_by;

    /**
     *
     * @var integer
     */
    public $rate;

    /**
     *
     * @var integer
     */
    public $viewer;

    /**
     *
     * @var integer
     */
    public $order;

    /**
     *
     * @var integer
     */
    public $test;

    /**
     *
     * @var integer
     */
    public $status;
    /**
     *
     * @var string
     */
    public $updated_at;

    /**
     *
     * @var string
     */
    public $created_at;

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
        return 'books';
    }

    public function beforeCreate()
    {
        //Set the creation date
        $this->updated_at = date('Y-m-d H:i:s');
        $this->created_at = date('Y-m-d H:i:s');
    }

    public function beforeUpdate()
    {
        //Set the modification date
        $this->updated_at = date('Y-m-d H:i:s');
    }

    static function buildConditions($search){
        $searchRegex = new MongoRegex("/$search/i");
        $conditions = array(
            '$or' => array(
                array('name' => $searchRegex),
            )
        );
        return $conditions;
    }
}
