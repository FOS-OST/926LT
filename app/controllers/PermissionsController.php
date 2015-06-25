<?php
 
use Books\Models\Permissions;
use Phalcon\Config\Adapter\Ini as ConfigIni;

class PermissionsController extends ControllerBase
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
        $this->bc->add('Permissions', 'permissions');
        $this->title = 'Permissions Management';
    }

    /**
     * Index action
     */
    public function indexAction(){
        $numberPage = $this->request->get('page',null,1);
        $this->persistent->parameters = null;
        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = array();
        }
        $paginator = new CollectionAdapter(array(
            "model" => new Permissions(),
            "limit"=> 10,
            "page" => $numberPage
        ));
        $this->view->page = $paginator->getPaginate();
    }
    /**
     * Displays the creation form
     */
    public function newAction(){
        $files = scandir($this->config->application->controllersDir);
        $controllerActions = array();
        foreach ($files as $file) {
            if ($controller = $this->extractController($file)) {
                $functions = get_class_methods($controller);
                $controllers = explode('Controller', $controller);
                if ((count($controllers) > 1)) {
                    $controller = $controllers[0];
                }
                foreach ($functions as $name) {
                    $action = $this->extractAction($name);
                    if($action != '') {
                        $controllerActions[$controller][$action] = $action;
                    }
                }
            }
        }
//        $resources = array(
//            array('*/*', '*'),
//            array('*/' . $controllerName, '*'),
//            array('*/*', $actionName),
//            array($moduleName . '/*', '*'),
//            array($moduleName . '/' . $controllerName, '*'),
//            array($moduleName . '/' . $controllerName, $actionName)
//        );
        debug($controllerActions);
    }

    /**
     * Edits a user
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {

            $permission = Permissions::findById($id);
            if (!$permission) {
                $this->flash->error("Permission was not found");

                return $this->dispatcher->forward(array(
                    "controller" => "permissions",
                    "action" => "index"
                ));
            }

            $this->view->id = $permission->_id->{'$id'};

            $this->tag->setDefault("id", $permission->_id->{'$id'});
            $this->tag->setDefault("name", $permission->name);
            $this->tag->setDefault("active", $permission->active);
        }
    }

    /**
     * Creates a new user
     */
    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "permissions",
                "action" => "index"
            ));
        }

        $permission = new Permissions();

        $permission->name = $this->request->getPost("name");
        $permission->active = $this->request->getPost("active");
        $permission->updated_at = $this->request->getPost("updated_at");
        $permission->created_at = $this->request->getPost("created_at");

        if (!$permission->save()) {
            foreach ($permission->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "permissions",
                "action" => "new"
            ));
        }

        $this->flash->success("Permission was saved successfully");
        //return $this->response->redirect('Permissions/index');
        return $this->dispatcher->forward(array(
            "controller" => "permissions",
            "action" => "index"
        ));

    }

    /**
     * Saves a Permission edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "permissions",
                "action" => "index"
            ));
        }

        $id = $this->request->getPost("id");

        $permission = Permissions::findByid($id);
        if (!$permission) {
            $this->flash->error("Permission does not exist " . $id);

            return $this->dispatcher->forward(array(
                "controller" => "permissions",
                "action" => "index"
            ));
        }

        $permission->name = $this->request->getPost("name");
        $permission->active = (int)$this->request->getPost("active");

        if (!$permission->save()) {
            foreach ($permission->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "permissions",
                "action" => "edit",
                "params" => array($permission->_id->{'$id'})
            ));
        }

        $this->flash->success("Permission was updated successfully");
        //return $this->response->redirect('Permissions/index');
        return $this->dispatcher->forward(array(
            "controller" => "permissions",
            "action" => "index"
        ));

    }

    /**
     * Deletes a Permission
     *
     * @param string $id
     */
    public function deleteAction($id)
    {

        $permission = Permissions::findByid($id);
        if (!$permission) {
            $this->flash->error("Permission was not found");

            return $this->dispatcher->forward(array(
                "controller" => "permissions",
                "action" => "index"
            ));
        }

        if (!$permission->delete()) {

            foreach ($permission->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "permissions",
                "action" => "index"
            ));
        }

        $this->flash->success("Permission was deleted successfully");

        return $this->dispatcher->forward(array(
            "controller" => "permissions",
            "action" => "index"
        ));
    }

    public function activeAction() {
        $request =$this->request;
        if ($request->isPost()==true) {
            if ($request->isAjax() == true) {
                $id = $request->getPost('id');
                $value = $request->getPost('value');
                $permission = Permissions::findByid($id);
                $permission->active = (int)!$value;
                if ($permission->save()) {
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
