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
        $this->bc->add('Roles', 'roles');
        $this->title = 'Roles Management';
    }

    /**
     * Index action
     */
    public function indexAction(){
        $currentPage = abs($this->request->getQuery('page', 'int', 1));
        $this->persistent->parameters = null;
        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = array();
        }
        $roles = Roles::find();
        $pager = new Pager(
            new Paginator(array(
                'data'  => $roles,
                'limit' => 20,
                'page'  => $currentPage,
            ))
        );
        $this->view->setVar('pager', $pager);
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

            $role = Roles::findById($id);
            if (!$role) {
                $this->flash->error("role was not found");

                return $this->dispatcher->forward(array(
                    "controller" => "roles",
                    "action" => "index"
                ));
            }

            $this->view->id = $role->getId()->{'$id'};

            $this->tag->setDefault("id", $role->getId()->{'$id'});
            $this->tag->setDefault("name", $role->name);
            $this->tag->setDefault("active", $role->active);
        }
    }

    /**
     * Creates a new user
     */
    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "roles",
                "action" => "index"
            ));
        }

        $role = new Roles();

        $role->name = $this->request->getPost("name");
        $role->active = (int)$this->request->getPost("active");

        if (!$role->save()) {
            foreach ($role->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "roles",
                "action" => "new"
            ));
        }

        $this->flash->success("Role was saved successfully");
        //return $this->response->redirect('roles/index');
        return $this->dispatcher->forward(array(
            "controller" => "roles",
            "action" => "index"
        ));

    }

    /**
     * Saves a role edited
     *
     */
    public function saveAction()
    {
        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "roles",
                "action" => "index"
            ));
        }

        $id = $this->request->getPost("id");

        $role = Roles::findByid($id);
        if (!$role) {
            $this->flash->error("role does not exist " . $id);

            return $this->dispatcher->forward(array(
                "controller" => "roles",
                "action" => "index"
            ));
        }

        $role->name = $this->request->getPost("name");
        $role->active = (int)$this->request->getPost("active");
        $role->role_id = $this->request->getPost("role_id");

        if (!$role->save()) {
            foreach ($role->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "roles",
                "action" => "edit",
                "params" => array($role->getId()->{'$id'})
            ));
        }

        $this->flash->success("Role was updated successfully");
        //return $this->response->redirect('roles/index');
        return $this->dispatcher->forward(array(
            "controller" => "roles",
            "action" => "index"
        ));

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