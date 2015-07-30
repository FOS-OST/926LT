<?php
/**
 * Created by PhpStorm.
 * User: HIEUTRIEU
 * Date: 7/25/2015
 * Time: 2:33 PM
 */
namespace Books\Backend\Controllers;

use Books\Backend\Models\Books;
use Books\Backend\Models\Users;
use Books\Backend\Models\Roles;
use Books\Backend\Models\TransactionHistory;
use Phalcon\Paginator\Pager;
use Phalcon\Paginator\Adapter\NativeArray as Paginator;

class UsersController extends ControllerBase
{

    /**
     * Initializes the controller
     */
    public function initialize()
    {
        parent::initialize();

        /**
         * Breadcrumbs for this section
         */
        $this->bc->add('Users', 'users');
        $this->title = 'Users Management';

        $this->assets->addCss('js/plugins/duallistbox/bootstrap-duallistbox.min.css');
        $this->assets->addJs('js/plugins/duallistbox/jquery.bootstrap-duallistbox.js');
    }

    /**
     * Index action
     */
    public function indexAction(){
        $currentPage = abs($this->request->getQuery('page', 'int', 1));
        $search = $this->request->getQuery('search', 'string', '');
        if ($currentPage == 0) {
            $currentPage = 1;
        }
        $this->persistent->parameters = null;
        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = array();
        }
        $conditions = Users::buildConditions($search);
        $parameters["sort"] = array('created_at' => -1);
        $parameters["conditions"] = $conditions;
        $users = Users::find($parameters);
        $pager = new Pager(
            new Paginator(array(
                'data'  => $users,
                'limit' => 20,
                'page'  => $currentPage,
            ))
        );
        $this->view->setVar('pager', $pager);
        $this->view->setVar('search', $search);
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {
        $this->view->user = new Users();
        $this->view->roles = Roles::getRoleOptions();
    }

    /**
     * Edits a user
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {

            $user = Users::findById($id);
            if (!$user) {
                $this->flash->error("user was not found");

                return $this->dispatcher->forward(array(
                    "controller" => "users",
                    "action" => "index"
                ));
            }
            $this->view->roles = Roles::getRoleOptions();

            $this->tag->setDefaults((array)$user);
            $this->tag->setDefault("id", $user->getId()->{'$id'});
            $this->tag->setDefault("password",'');
            $this->view->user = $user;
        }
    }

    /**
     * Creates a new user
     */
    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "index"
            ));
        }

        $user = new Users();

        $user->name = $this->request->getPost("name");
        $user->email = $this->request->getPost("email", "email");
        $user->password = $this->request->getPost("password");
        $user->avatar = $this->request->getPost("avatar");
        $user->device_token = $this->request->getPost("device_token");
        $user->access_token = $this->request->getPost("access_token");
        $user->remember_token = $this->request->getPost("remember_token");
        $user->phone = $this->request->getPost("phone");
        $user->active = (int)$this->request->getPost("active");
        $user->role_id = $this->request->getPost("role_id");
        $user->total = floatval(0);
        $user->status = floatval(1);
        if($user->validation()) {
            $user->password = $this->security->hash($user->password);
        }

        if (!$user->save()) {
            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "new"
            ));
        } else {
            $this->flash->success("user was saved successfully");
            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "index"
            ));
        }
    }

    /**
     * Saves a user edited
     *
     */
    public function saveAction(){
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "index"
            ));
        }

        $id = $this->request->getPost("id");
        $user = Users::findByid($id);
        if (!$user) {
            $this->flash->error("user does not exist " . $id);

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "index"
            ));
        }

        $user->name = $this->request->getPost("name");
        $user->email = $this->request->getPost("email");
        $password = $this->request->getPost("password");
        $user->avatar = $this->request->getPost("avatar");
        $user->role_id = $this->request->getPost("role_id");
        $user->phone = $this->request->getPost("phone");
        $user->active = (int)$this->request->getPost("active");

        if($password != '') {
            $user->password = $this->security->hash($password);
        }
        if (!$user->save()) {
            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "edit",
                "params" => array($user->getId()->{'$id'})
            ));
        } else {
            $this->flash->success("user was updated successfully");
            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "index"
            ));
        }
    }

    /**
     * Deletes a user
     *
     * @param string $id
     */
    public function deleteAction($id)
    {

        $user = Users::findByid($id);
        if (!$user) {
            $this->flash->error("user was not found");

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "index"
            ));
        }
        $user->status = 0;
        if (!$user->save()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "search"
            ));
        }

        $this->flash->success("user was deleted successfully");

        return $this->dispatcher->forward(array(
            "controller" => "users",
            "action" => "index"
        ));
    }

    public function activeAction() {
        $request =$this->request;
        if ($request->isPost()==true) {
            if ($request->isAjax() == true) {
                $id = $request->getPost('id');
                $value = $request->getPost('value');
                $user = Users::findFirstByid($id);
                $user->active = !$value;
                if ($user->save()) {
                    //$this->response->setJsonContent();
                    echo json_encode(array('error' => false));
                    exit;
                }
            }
        }
        echo json_encode(array('error' => true));
        exit;
    }

    public function creditAction($uid){
        $user = Users::findById($uid);
        if ($this->request->isAjax() == true) {
            if ($this->request->isPost()==true) {
                $amount = $this->request->getPost('amount');
                $note = $this->request->getPost('note');
                $total = $user->total + $amount;
                $user->total = $total;
                if (!$user->save()) {
                    echo json_encode(array('error' => true, 'msg' => 'Save failed'));
                } else {
                    /*$translateHistory = TransactionHistory::findFirst(array(
                        'conditions' => array('user_id' => $uid)
                    ));

                    if(!$translateHistory) {
                        $translateHistory = new TransactionHistory();
                    }
                    $history = $translateHistory->history;
                    $history[] = array(
                        'payment_type' => 'Admin',
                        'amount' => $amount,
                        'type' => 'Deposit', //
                        'created_by' => array(
                            'id' => new MongoId($this->identity['id']),
                            'name' => $this->identity['name'],
                        ), //
                        'note' => $note, //
                        'created_at' => new MongoDate(),
                        'status' => TransactionHistory::TRANSFER_SUCCESS,
                    );
                    $translateHistory->history = $history;
                    $translateHistory->user_id = $uid;
                    $translateHistory->total = $total;
                    */
                    $translateHistory = new TransactionHistory();
                    $translateHistory->payment_type = 'Admin';
                    $translateHistory->amount = $amount;
                    $translateHistory->type = 'Deposit';
                    $translateHistory->user_id = new MongoId($uid);
                    $translateHistory->created_by = new MongoId($this->identity['id']);
                    $translateHistory->created_by_name = $this->identity['name'];
                    $translateHistory->note = $note;
                    $translateHistory->status = TransactionHistory::TRANSFER_SUCCESS;
                    $translateHistory->total = $total;

                    if (!$translateHistory->save()) {
                        //echo json_encode(array('error' => false, 'msg' => 'History Save successfully'));
                    } else {
                        //echo json_encode(array('error' => false, 'msg' => 'History Save failed'));
                    }
                    echo json_encode(array('error' => false, 'msg' => 'Save successfully'));
                    exit;
                }
                exit;
            } else {
                echo $this->view->partial('users/_credit', array('user' => $user));
                exit;
            }
        }
        exit;
    }

    /**
     * @param $uid object mongoId string
     */
    public function buybookAction($uid) {
        $user = Users::findById($uid);
        if ($this->request->isAjax() == true) {
            if ($this->request->isPost()==true) {
                $bookSelected = $this->request->getPost('bookSelected');
                $books = $user->books;
                foreach ($bookSelected as $index => $bookSelect) {
                    $book = explode(':', $bookSelect);
                    list($id, $name) = $book;
                    $books[] = array(
                        'id' => $id,
                        'name' => $name
                    );
                }
                $user->books = $books;
                if (!$user->save()) {
                    echo json_encode(array('error' => true, 'msg' => 'Save failed'));
                } else {
                    echo json_encode(array('error' => false, 'msg' => 'Save successfully'));
                }
                exit;
            } else {
                $books = Books::find();
                $bookBought = array();
                foreach($user->books as $book) {
                    $bookBought[$book['id']] = $book['id'];
                }
                echo $this->view->partial('users/_buybook', array('user' => $user, 'books' => $books, 'bookBought' => $bookBought));
                exit;
            }
        }
        exit;
    }

    public function booksAction($uid) {
        $user = Users::findById($uid);
        $this->title = 'Books of '.$user->name;
        $search = $this->request->getQuery('search', 'string', '');
        $bookIds = array();
        foreach($user->books as $book) {
            $bookIds[] = new MongoId($book['id']);
        }
        $searchRegex = new MongoRegex("/$search/i");
        $books = Books::find(array(
            'conditions' => array(
                '$and' => array(
                    array('_id'=>array('$in'=>$bookIds)),
                    array('name'=>$searchRegex),
                )
            )
        ));
        echo $this->view->partial('users/_books', array('user' => $user, 'books' => $books, 'search' => $search));
    }

    public function historyAction($uid) {
        $this->assets->addJs('js/plugins/daterangepicker/daterangepicker.js');
        $user = Users::findById($uid);
        $this->title = $this->t->_('Transactions History of', array('name' => $user->name));
        $search = $this->request->getQuery('daterange', 'string', '');
        $conditions = TransactionHistory::buildConditions($search, $uid);
        $histories = TransactionHistory::find(array(
            'conditions' => $conditions,
        ));
        $this->addViewVar('search', $search);
        $this->addViewVar('histories', $histories);
    }

}
