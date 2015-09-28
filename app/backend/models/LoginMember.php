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

class LoginMember extends ModelBase {

    /**
     *
     * @var string
     */
    public $user_id;

    /**
     *
     * @var string
     */
    public $out;

    public function getSource() {
        return 'loginmember';
    }

    public static function buildConditionsCreate($date) {
        $conditions = array(
            'out' => null,
            'created_at' => array(
                '$gte' => $date
            ),
        );
        return $conditions;
    }

}
