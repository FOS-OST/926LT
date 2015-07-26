<?php
/**
 * Created by PhpStorm.
 * User: HIEUTRIEU
 * Date: 7/25/2015
 * Time: 2:33 PM
 */
namespace Books\Backend\Controllers;

use Books\Backend\Models\Roles;
use Books\Backend\Models\Permissions;
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
        $this->title = 'Permissions Management';
    }

    /**
     * Index action
     */
    public function indexAction(){
        $currentPage = abs($this->request->getQuery('page', 'int', 1));
        $this->persistent->parameters = null;
        $roles = Roles::find();
        $permissions = Permissions::find();
        $this->view->setVar('roles', $roles);
        $this->view->setVar('permissions', $permissions);
        $this->assets->addCss('js/plugins/bootstrap_toggle/css/bootstrap-toggle.min.css');
        $this->assets->addJs('js/plugins/bootstrap_toggle/js/bootstrap-toggle.min.js');
    }

    /**
     * Saves a Permission edited
     *
     */
    public function saveAction() {
        $post = $this->request->getPost();
        debug($post, true);
    }

    /**
     * Deletes a Permission
     *
     * @param string $id
     */
    public function deleteAction($id) {


    }
}
