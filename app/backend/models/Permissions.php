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

class Permissions extends ModelBase {

    /**
     * @var object mongoId
     */
    public $user_id;

    /**
     * @var object mongoId
     */
    public $book_id;
    /**
     *
     * @var integer
     */
    public $allowPublish = 0;

    /**
     *
     * @var integer
     */
    public $allowEdit = 0;

    /**
     *
     * @var integer
     */
    public $allowDelete = 0;

    /**
     *
     * @var integer
     */
    public $allowTest = 0;

    /**
     *
     * @var integer
     */
    public $allowView = 1;

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
