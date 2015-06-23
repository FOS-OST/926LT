<?php

use Phalcon\Mvc\Collection;
//class Users extends \Phalcon\Mvc\Model
class Test extends Collection {
    public function getSource() {
        return "test";
    }

    public function beforeCreate()
    {
        // Set the creation date
        $this->created_at = date('Y-m-d H:i:s');
    }

    public function beforeUpdate()
    {
        // Set the modification date
        $this->updated_in = date('Y-m-d H:i:s');
    }
}
