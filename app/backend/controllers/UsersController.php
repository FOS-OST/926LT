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
use Books\Backend\Models\UsersBooks;
use Books\Backend\Libraries\Mail\Mail;
use MongoId;
use MongoRegex;
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
        $this->bc->add('Users', 'admin/users/index');
        $this->title = 'Users Management';

        $this->assets->addCss('js/plugins/duallistbox/bootstrap-duallistbox.min.css');
        $this->assets->addJs('js/plugins/duallistbox/jquery.bootstrap-duallistbox.js');
    }

    /**
     * Index action
     */
    public function indexAction()
    {
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
        $aclRoles = array(
            'private' => $this->adminAcl->getPrivateRoles(),
            'public' => $this->adminAcl->getPublicRoles()
        );
        $conditions = Users::buildConditions($search, $this->admin['role'], $aclRoles);
        $parameters["sort"] = array('created_at' => -1);
        $parameters["conditions"] = $conditions;
        $users = Users::find($parameters);
        $pager = new Pager(
            new Paginator(array(
                'data' => $users,
                'limit' => 20,
                'page' => $currentPage,
            ))
        );
        $this->view->setVar('pager', $pager);
        $this->view->setVar('search', $search);
    }
    /*history*/
    public function historyAction($uid)
    {
        $this->assets->addJs('js/plugins/daterangepicker/daterangepicker.js');
        $user = Users::findById($uid);
        $this->title = $this->t->_('Transactions History of', array('name' => $user->name));
        $search = $this->request->getQuery('daterange', 'string', '');
        $conditions = TransactionHistory::buildConditions($search, $uid);
       //echo '<pre>'; print_r($conditions);die;
        $histories = TransactionHistory::find(array(
            'conditions' => $conditions,
            'sort' => array('created_at' => -1),
        ));

        $this->addViewVar('search', $search);
        $this->addViewVar('histories', $histories);
        $this->addViewVar('user', $user);
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
    /*public function editAction($id)
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
    }*/

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
        if ($user->validation()) {
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
    public function saveAction()
    {
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

        if ($password != '') {
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

    public function activeAction()
    {
        $request = $this->request;
        if ($request->isPost() == true) {
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

    public function creditAction($uid)
    {
        $user = Users::findById($uid);
        $userName = $user->name;
        if ($this->request->isAjax() == true) {
            if ($this->request->isPost() == true) {
                $amount = $this->request->getPost('amount');
                $note = $this->request->getPost('note');
                if ($amount > 0 && $note != '') {
                    $total = $user->total + $amount;
                    $user->total = $total;
                    if (!$user->save()) {
                        echo json_encode(array('error' => true, 'msg' => "Hệ thống đã nạp tiền ({$user->name}) thất bại."));
                    } else {
                        $userAdmin = Users::findById($this->admin['id']);
                        if ($userAdmin->total < $amount) {
                            echo json_encode(array('error' => true, 'msg' => "Số tiền trong tài khoản của bạn không đủ để thực hiện giao dịch. Bạn cần liên hệ với Administrator để nạp tiền bổ xung vào tài khoản của bạn."));
                            exit;
                        } else {
                            $userHistory = new TransactionHistory();
                            $userHistory->payment_type = 'Nạp tiền từ Admin';
                            $userHistory->amount = $amount;
                            $userHistory->type = 'Deposit';
                            $userHistory->user_id = new MongoId($uid);
                            $userHistory->client_id = new MongoId($uid);
                            $userHistory->created_by = new MongoId($this->admin['id']);
                            $userHistory->created_by_name = $this->admin['name'];
                            $userHistory->note = $note;
                            $userHistory->status = TransactionHistory::TRANSFER_SUCCESS;
                            $userHistory->total = $total;
                            $userHistory->save();

                            $userAdmin->total = $userAdmin->total - $amount;
                            if ($userAdmin->save()) {
                                $adminHistory = new TransactionHistory();
                                $adminHistory->payment_type = 'Trừ tiền trong tài khoản khi nạp tiền cho Khách hàng từ Admin';
                                $adminHistory->amount = $amount;
                                $adminHistory->type = 'Withdraw';
                                $adminHistory->user_id = new MongoId($this->admin['id']);
                                $adminHistory->client_id = new MongoId($uid);
                                $adminHistory->created_by = new MongoId($this->admin['id']);
                                $adminHistory->created_by_name = $this->admin['name'];
                                $adminHistory->note = "Nạp số tiền ({$amount}) cho khách hàng ({$user->name})";
                                $adminHistory->status = TransactionHistory::TRANSFER_SUCCESS;
                                $adminHistory->total = $userAdmin->total;
                                $adminHistory->save();
                            }

                            echo json_encode(array('error' => false, 'msg' => "Hệ thống đã nạp tiền cho thành viên ({$user->name}) thành công."));
                            exit;
                        }
                    }
                } else {
                    echo json_encode(array('error' => true, 'msg' => 'Bạn kiểm tra lai xem đã điền số tiền và ghi chú hợp lệ chưa.'));
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
    public function buybookAction($uid)
    {
        $user = Users::findById($uid);
        if ($this->request->isAjax() == true) {
            if ($this->request->isPost() == true) {
                $bookSelected = $this->request->getPost('bookSelected');
                $bookIds = array();
                $bookBuys = $user->books;
                foreach ($bookSelected as $index => $bookSelect) {
                    $book = explode(':', $bookSelect);
                    list($id, $name) = $book;
                    $bookIds[$id] = new MongoId($id);
                    $bookBuys[$id] = array(
                        'id' => $id,
                        'name' => $name
                    );
                }
                $user->books = $bookBuys;
                if (!$user->save()) {
                    echo json_encode(array('error' => true, 'msg' => "Đã có lỗi khi bán sách cho thành viên {$user->name}."));
                } else {
                    if (count($bookIds) > 0) {
                        $books = Books::find(array(
                            'conditions' => array(
                                '_id' => array('$in' => array_values($bookIds))
                            ),
                        ));
                    }
                    foreach ($books as $book) {
                        $userBook = new UsersBooks();
                        $userBook->user_id = $user->getId();
                        $userBook->book_id = $book->getId();
                        $userBook->book_name = $book->name;
                        $userBook->book_price = doubleval($book->price);
                        $userBook->book_author = $book->author;
                        $userBook->created_by = new MongoId($this->admin['id']);
                        $userBook->created_by_name = $this->admin['name'];
                        $userBook->save();
                    }
                    echo json_encode(array('error' => false, 'msg' => "Đã bán sách thành công cho thành viên {$user->name}."));
                }
                exit;
            } else {
                $books = Books::find();
                $bookBought = array();
                foreach ($user->books as $book) {
                    $bookBought[$book['id']] = $book['id'];
                }
                echo $this->view->partial('users/_buybook', array('user' => $user, 'books' => $books, 'bookBought' => $bookBought));
                exit;
            }
        }
        exit;
    }

    public function booksAction($uid)
    {
        $this->assets->addJs('js/plugins/daterangepicker/daterangepicker.js');
        $user = Users::findById($uid);
        $this->title = 'Sách đã mua của - ' . $user->name;
        $search = $this->request->getQuery('daterange', 'string', '');
        $conditions = UsersBooks::buildConditions($search, $uid);
        $usersBooks = UsersBooks::find(array(
            'conditions' => $conditions,
            'sort' => array('created_at' => -1),
        ));

        $this->addViewVar('search', $search);
        $this->addViewVar('user', $user);
        $this->addViewVar('books', $usersBooks);
    }

    


    public function editAction($id)
    {
        $this->title = $this->t->_('Edit users');
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
            $this->tag->setDefault("password", '');
            $this->view->user = $user;
        }
    }

    public function  resetpasswordAction($id)
    {
        $user = Users::findById($id);
        if($_POST){
            $result = "";
            $chars = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $charArray = str_split($chars);
            for ($i = 0; $i < 10; $i++) {
                $randItem = array_rand($charArray);
                $result .= "" . $charArray[$randItem];
            }

            if ($result != '') {
                $user->password = $this->security->hash($result);
            }
            if (!$user->save()) {
                $this->flash->error("Không thưc hiên đươc reset password");
                return $this->dispatcher->forward(array(
                    "controller" => "users",
                    "action" => "edit",
                    "params" => array($user->getId()->{'$id'})
                ));
            } else {
                     $mail = new Mail();
                     $mail->send($user->email, 'Mật Khẩu Mới', $user->name, $result);
                return $this->response->redirect('admin/users/history/'.$user->getId()->{'$id'});
            }
        }
        $this->addViewVar('user', $user);
        echo $this->view->partial('users/resetpassword');
        exit;
    }

}
