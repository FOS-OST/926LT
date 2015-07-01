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
     *
     * @var string
     */
    public $type;

    /**
     * @var float
     */
    public $amount;

    /**
     * @var string
     */
    public $image;

    /**
     * @var string
     */
    public $payment_type;

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
