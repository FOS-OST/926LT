<?php
/**
 * Created by PhpStorm.
 * User: hieutrieu
 * Date: 6/23/15
 * Time: 11:42
 */

namespace Books\Backend\Models;

use Books\Backend\Models\Base\ModelBase;
use MongoId;
use MongoRegex;
use Phalcon\Mvc\Collection;
use Phalcon\Mvc\Model\Validator\PresenceOf;

class TransactionHistory extends ModelBase {
    /**
     *
     * @var mongoId
     */
    public $user_id;
    public $client_id;

    /**
     * @var array
     */
    public $history = array();

    /**
     * @var string
     */
    public $payment_type = '';
    /**
     * @var float
     */
    public $amount = 0.0;
    /**
     * @var string
     */
    public $type = '';
    /**
     * @var float
     */
    public $total = 0.0;
    /**
     * @var mongoId
     */
    public $created_by = '';
    /**
     * @var string
     */
    public $created_by_name = '';
    /**
     * @var string
     */
    public $note = '';
    /**
     * @var int
     */
    public $status = 0;

    const TRANSFER_SUCCESS = 1;
    const TRANSFER_PENDING = 0;
    const TRANSFER_FAILED = -1;

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'transaction_history';
    }

    static function buildConditions($daterange, $uid){
        $daterange = explode(' - ', $daterange);
        $startDate = new \MongoDate(strtotime($daterange[0]));
        $endDate = new \MongoDate(strtotime($daterange[1]));
        $conditions = array(
            '$and' => array(
                array('user_id' => new MongoId($uid)),
                array('created_at' =>
                    array(
                        '$gte' => $startDate,
                        '$lt' => $endDate
                    ),
                )
            )
        );
        return $conditions;
    }
}
