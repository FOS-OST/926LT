<?php
/**
 * Created by PhpStorm.
 * User: HIEUTRIEU
 * Date: 7/25/2015
 * Time: 2:33 PM
 */
namespace Books\Backend\Controllers;

use Books\Backend\Models\Roles;
use Phalcon\Paginator\Pager;
use Phalcon\Paginator\Adapter\NativeArray as Paginator;

class RolesController extends ControllerBase
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
        $this->bc->add('Roles', 'admin/roles/index');
        $this->title = 'Roles Management';
        $this->assets->addCss('js/plugins/bootstrap_toggle/css/bootstrap-toggle.min.css');
        $this->assets->addJs('js/plugins/bootstrap_toggle/js/bootstrap-toggle.js');
    }

    /**
     * Index action
     */
    public function indexAction(){
        $currentPage = abs($this->request->getQuery('page', 'int', 1));
        $roles = Roles::find();
        $ignoreArr = array('Administrator', 'Member');
        $this->view->setVar('roles', $roles);
        $this->view->setVar('ignoreArr', $ignoreArr);
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
    public function editAction($id) {
        if (!$this->request->isPost()) {

            $role = Roles::findById($id);
            if (!$role) {
                $this->flash->error("role was not found");

                return $this->dispatcher->forward(array(
                    "controller" => "roles",
                    "action" => "index"
                ));
            }

            $this->tag->setDefaults($role->toArray());
        }
    }

    /**
     * Creates a new role
     */
    public function createAction() {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "roles",
                "action" => "index"
            ));
        }

        $role = new Roles();

        $role->name = $this->request->getPost("name");
        $role->active = 1;
        $allowMenu = filter_var($this->request->getPost('menu'), FILTER_VALIDATE_BOOLEAN);
        $allowPublished = filter_var($this->request->getPost('published'), FILTER_VALIDATE_BOOLEAN);
        $allowUser = filter_var($this->request->getPost('user'), FILTER_VALIDATE_BOOLEAN);
        $allowBook = filter_var($this->request->getPost('book'), FILTER_VALIDATE_BOOLEAN);

        $role->allowMenu = $allowMenu;
        $role->allowPublished = $allowPublished;
        $role->allowUser = $allowUser;
        $role->allowBook = $allowBook;

        if (!$role->save()) {
            foreach ($role->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward(array(
                "controller" => "roles",
                "action" => "new"
            ));
        } else {
            $this->flash->success("Role was saved successfully");
            return $this->dispatcher->forward(array(
                "controller" => "roles",
                "action" => "index"
            ));
        }

    }

    /**
     * Saves a role edited
     *
     */
    public function saveAction() {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "roles",
                "action" => "index"
            ));
        }
        $id = $this->request->getPost("_id");
        $allowMenu = filter_var($this->request->getPost('menu'), FILTER_VALIDATE_BOOLEAN);
        $allowPublished = filter_var($this->request->getPost('published'), FILTER_VALIDATE_BOOLEAN);
        $allowUser = filter_var($this->request->getPost('user'), FILTER_VALIDATE_BOOLEAN);
        $allowBook = filter_var($this->request->getPost('book'), FILTER_VALIDATE_BOOLEAN);

        $role = Roles::findByid($id);
        if (!$role) {
            $this->flash->error("role does not exist " . $id);

            return $this->dispatcher->forward(array(
                "controller" => "roles",
                "action" => "index"
            ));
        }

        $role->name = $this->request->getPost("name");
        $role->active = 1;
        $role->allowMenu = $allowMenu;
        $role->allowPublished = $allowPublished;
        $role->allowUser = $allowUser;
        $role->allowBook = $allowBook;
        if (!$role->save()) {
            foreach ($role->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "roles",
                "action" => "edit",
                "params" => array($role->getId())
            ));
        } else {

            $this->flash->success("Role was updated successfully");
            //return $this->response->redirect('roles/index');
            return $this->dispatcher->forward(array(
                "controller" => "roles",
                "action" => "index"
            ));
        }
    }

    /**
     * Deletes a role
     *
     * @param string $id
     */
    public function deleteAction($id)
    {

        $role = Roles::findByid($id);
        if (!$role) {
            $this->flash->error("Role was not found");

            return $this->dispatcher->forward(array(
                "controller" => "roles",
                "action" => "index"
            ));
        }

        if (!$role->delete()) {

            foreach ($role->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "roles",
                "action" => "index"
            ));
        }

        $this->flash->success("Role was deleted successfully");

        return $this->dispatcher->forward(array(
            "controller" => "roles",
            "action" => "index"
        ));
    }

    public function activeAction() {
        $request =$this->request;
        if ($request->isPost()==true) {
            if ($request->isAjax() == true) {
                $id = $request->getPost('id');
                $value = $request->getPost('value');
                $role = Roles::findByid($id);
                $role->active = (int)!$value;
                if ($role->save()) {
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
