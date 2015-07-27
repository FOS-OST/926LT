<?php
/**
 * Created by PhpStorm.
 * User: HIEUTRIEU
 * Date: 7/25/2015
 * Time: 2:33 PM
 */
namespace Books\Backend\Controllers;

use Books\Backend\Models\Books;
use Books\Backend\Models\Category;
use Books\Backend\Models\Roles;
use Books\Backend\Models\Permissions;
use MongoId;
use Phalcon\Paginator\Pager;
use Phalcon\Paginator\Adapter\NativeArray as Paginator;

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
        $this->bc->add('Permissions', 'admin/permissions/index');
        $this->title = $this->t->_('Permissions Management');

        $this->assets->addCss('js/plugins/bootstrap_toggle/css/bootstrap-toggle.min.css');
        $this->assets->addJs('js/plugins/bootstrap_toggle/js/bootstrap-toggle.js');
        $this->assets->addJs('js/plugins/bootstrap_treegird/js/jquery.treegrid.js');
        $this->assets->addJs('js/plugins/bootstrap_treegird/js/jquery.treegrid.bootstrap3.js');
    }

    /**
     * Index action
     */
    public function indexAction($uid){
        $currentPage = abs($this->request->getQuery('page', 'int', 1));
        $allowPermissions = array();
        $categories = Category::find(array(
            'conditions' => array(
                'status' => 1
            ),
            'sort' => array('order' => 1)
        ));
        $permissions = Permissions::find(array(
            'conditions' => array(
                'user_id' => new MongoId($uid)
            )
        ));
        foreach($permissions as $permission) {
            $book_id = $permission->book_id->{'$id'};
            $allowPermissions[$book_id]['allowEdit'] = $permission->allowEdit;
            $allowPermissions[$book_id]['allowPublished'] = $permission->allowPublished;
            $allowPermissions[$book_id]['allowDelete'] = $permission->allowDelete;
            $allowPermissions[$book_id]['allowTest'] = $permission->allowTest;
        }

        $this->view->setVar('categories', $categories);
        $this->view->setVar('allowPermissions', $allowPermissions);
        $this->tag->setDefault('uid', $uid);
    }

    /**
     * Saves a Permission edited
     *
     */
    public function saveAction() {
        if($this->request->isPost()) {
            $uid = $this->request->getPost('uid');
            $permissions = Permissions::find(array(
                'conditions' => array(
                    'user_id' => new MongoId($uid)
                )
            ));
            foreach($permissions as $permission) {
                $permission->delete();
            }
            $books = $this->request->getPost('books');
            foreach($books as $bookId => $book) {
                $permission = new Permissions();
                $permission->user_id = new MongoId($uid);
                $permission->book_id = new MongoId($bookId);
                $permission->allowEdit = isset($book['allowEdit'])?1:0;
                $permission->allowPublished = isset($book['allowPublished'])?1:0;
                $permission->allowDelete = isset($book['allowDelete'])?1:0;
                $permission->allowTest = isset($book['allowTest'])?1:0;
                $permission->save();
            }
        }
        return $this->dispatcher->forward(array(
            "controller" => "users",
            "action" => "index",
        ));
    }

    /**
     * Deletes a Permission
     *
     * @param string $id
     */
    public function deleteAction($id) {


    }
}
