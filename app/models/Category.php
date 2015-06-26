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
    public $image = '/files/images/book_bg.jpg';

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
     * @var integer
     */
    public $number_book_display=3;// number ebooks display by default on client

    /**
     *
     * @var array
     */
    public $ebooks=array();// list ebooks in this topic , format array('book index'=>'book id')

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

    static function updateBook(array $categoryIds, $bookId, $bookName=''){
        $book = array('id' => $bookId, 'order' => 0, 'name' => $bookName);
        foreach($categoryIds as $cId) {
            // Get Category By ID
            $category = self::findById($cId);
            if($category) {
                // Get All Books
                $eBooks = $category->ebooks;
                $bookIds = array();
                foreach ($eBooks as $index => $eBook) {
                    $bookIds[] = $eBook['id'];
                    if ($bookId == $eBook['id']) {
                        $eBooks[$index] = array('id' => $bookId, 'order' => $eBook['order'], 'name' => $bookName);;
                    }
                }
                if (!in_array($bookId, $bookIds)) {
                    $book['order'] = count($bookIds) + 1;
                    $eBooks[] = $book;
                }
                $category->updated_at = '';
                $category->ebooks = $eBooks;
                $category->save();
            }
        }
    }
}
