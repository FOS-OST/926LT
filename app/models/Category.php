<?php
namespace Books\App\Models;
use MongoRegex;
use Phalcon\Mvc\Model\Validator\PresenceOf;
use Phalcon\Mvc\Model\Validator\Uniqueness;
use Phalcon\Mvc\Collection;

class Category extends ModelBase
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
    public $image;

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var integer
     */
    public $status;
    /**
     *
     * @var string
     */

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
        return 'category';
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

    static function getDropdown() {
        $parameters = array(
            'conditions' => array('status' => 1)
        );
        $categories = self::find($parameters);
        $options = array();
        foreach($categories as $category) {
            $options[$category->_id->{'$id'}] = $category->name;
        }
        return $options;
    }
}
