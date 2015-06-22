<?php
use Phalcon\Di\Injectable;

class BookSingle extends Injectable {
    private static $instance = null;

    public static function getInstance() {
        if(self::$instance==null) {
            self::$instance = new BookSingle();
        }
        return self::$instance;
    }

    public function render() {
        return $this->view->getPartial('books/types/single', array('content' => 'test'));
    }
}
