<?php
 
use Books\App\Models\Users;
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
        $parameters["sort"] = array('updated_at' => -1);
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
        /*$paginator = new CollectionAdapter(array(
            "model" => new Users(),
            "limit"=> 10,
            "page" => $currentPage
        ));
        $this->view->page = $paginator->getPaginate();*/
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {

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

            $this->view->id = $user->_id->{'$id'};

            $this->tag->setDefault("id", $user->_id->{'$id'});
            $this->tag->setDefault("name", $user->name);
            $this->tag->setDefault("email", $user->email);
            $this->tag->setDefault("password", '');
            $this->tag->setDefault("avatar", $user->avatar);
            $this->tag->setDefault("device_token", $user->device_token);
            $this->tag->setDefault("access_token", $user->access_token);
            $this->tag->setDefault("remember_token", $user->remember_token);
            $this->tag->setDefault("active", $user->active);
            $this->tag->setDefault("phone", $user->phone);
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
        $user->amount = floatval(0);
        $user->status = floatval(1);

        $user->password = $this->security->hash($user->password);

        if (!$user->save()) {
            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "new"
            ));
        }

        $this->flash->success("user was saved successfully");
        //return $this->response->redirect('users/index');
        return $this->dispatcher->forward(array(
            "controller" => "users",
            "action" => "index"
        ));

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
        $user->password = $this->request->getPost("password");
        $user->avatar = $this->request->getPost("avatar");
        $user->phone = $this->request->getPost("phone");
        $user->active = (int)$this->request->getPost("active");

        if($user->password != '') $user->password = $this->security->hash($user->password);
        if (!$user->save()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "users",
                "action" => "edit",
                "params" => array($user->_id->{'$id'})
            ));
        }

        $this->flash->success("user was updated successfully");
        return $this->dispatcher->forward(array(
            "controller" => "users",
            "action" => "index"
        ));

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

}
