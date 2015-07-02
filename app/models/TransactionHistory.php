<?php
namespace Books\App\Models;
use Phalcon\Mvc\Collection;
use Phalcon\Mvc\Model\Validator\PresenceOf;

class TransactionHistory extends ModelBase {
    /**
     *
     * @var string
     */
    public $user_id;

    /**
     * @var array
     */
    public $history = array();
    /**
     * @var float
     */
    public $total;

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'transaction_history';
    }
}
