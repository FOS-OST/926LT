<?php
/**
 * Hieutrieu
 */
namespace Books\Backend\Libraries\Acl;
use Books\Backend\Models\Permissions;
use Phalcon\Exception;
use Phalcon\Mvc\User\Component;
use Phalcon\Acl\Adapter\Memory as AclMemory;
use Phalcon\Acl\Role as AclRole;
use Phalcon\Acl\Resource as AclResource;
use Books\Backend\Models\Roles;

/**
 * Books\Acl\Acl
 */
class Acl extends Component {
    /**
     * The ACL Object
     *
     * @var \Phalcon\Acl\Adapter\Memory
     */
    private $acl;

    /**
     * The filepath of the ACL cache file from APP_DIR
     *
     * @var string
     */
    private $filePath = '/backend/cache/acl/data.txt';

    private $privateRoles = array(
        'Administrator',
    );

    /**
     * Define the resources that are considered "private". These controller => actions require authentication.
     *
     * @var array
     */
    private $privateResources = array(
        'users' => array(
            'new','edit','index','delete','save'
        ),
        'roles' => array(
            'new','edit','index','delete','save'
        ),
        'books' => array(
            'new','edit','index','delete','preview','save'
        ),
        'category' => array(
            'new','edit','index','delete','save'
        ),
        'menu' => array(
            'new','edit','index','delete','save'
        )
    );

    /**
     * Human-readable descriptions of the actions used in {@see $privateResources}
     *
     * @var array
     */
    private $actionDescriptions = array(
        'index' => 'Access',
        'search' => 'Search',
        'create' => 'Create',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'changePassword' => 'Change password'
    );

    /**
     * Checks if a controller is private or not
     *
     * @param string $controllerName
     * @return boolean
     */
    public function isPrivate($controllerName)
    {
        return isset($this->privateResources[$controllerName]);
    }

    /**
     * Checks if the current profile is allowed to access a resource
     *
     * @param string $profile
     * @param string $controller
     * @param string $action
     * @return boolean
     */
    public function isAllowed($profile, $controller, $action)
    {
        return $this->getAcl()->isAllowed($profile, $controller, $action);
    }

    /**
     * Returns the ACL list
     *
     * @return Phalcon\Acl\Adapter\Memory
     */
    public function getAcl() {
        $profile = $this->adminAuth->getIdentity();
        // Check if the ACL is already created
        if (is_object($this->acl)) {
            return $this->acl;
        }
        // Check if the ACL is in APC
        if (function_exists('apc_fetch')) {
            $acl = apc_fetch('books-acl');
            if (is_object($acl)) {
                $this->acl = $acl;
                return $acl;
            }
        }

        // Check if the ACL is already generated
        if (!file_exists(APP_DIR . $this->filePath)) {

            $this->acl = $this->rebuild();
            return $this->acl;
        }
        // Get the ACL from the data file
        $data = file_get_contents(APP_DIR . $this->filePath);
        $data = unserialize($data);
        if($data->isRole($profile['id'])) {
            $this->acl = $data;
        } else {
            $this->acl = $this->rebuild();
        }

        // Store the ACL in APC
        if (function_exists('apc_store')) {
            apc_store('books-acl', $this->acl);
        }
        //debug($this->acl, true);
        return $this->acl;
    }

    /**
     * Returns the permissions assigned to a profile
     *
     * @param Profiles $profile
     * @return array
     */
    public function getPermissions(Permissions $permissions)
    {
        $permissions = array();
        foreach ($permissions->getPermissions() as $permission) {
            $permissions[$permission->resource . '.' . $permission->action] = true;
        }
        return $permissions;
    }

    /**
     * Returns all the resoruces and their actions available in the application
     *
     * @return array
     */
    public function getResources()
    {
        return $this->privateResources;
    }

    /**
     * Returns the action description according to its simplified name
     *
     * @param string $action
     * @return $action
     */
    public function getActionDescription($action)
    {
        if (isset($this->actionDescriptions[$action])) {
            return $this->actionDescriptions[$action];
        } else {
            return $action;
        }
    }

    /**
     * Rebuilds the access list into a file
     *
     * @return \Phalcon\Acl\Adapter\Memory
     */
    public function rebuild() {
        $acl = new AclMemory();
        $acl->setDefaultAction(\Phalcon\Acl::DENY);

        //debug($this->privateResources['menu'], true);
        // Register roles
        $profile = $this->adminAuth->getIdentity();
        if($profile['role_id']) {
            $role = Roles::findById($profile['role_id']);
        } else {
            $role = null;
        }
        $acl->addRole(new AclRole($profile['id']));
        if(in_array($role->name, $this->privateRoles)) {
            $acl = $this->fullAccess($profile['id'],$acl);
        } else {
            foreach ($this->privateResources as $resource => $actions) {
                $acl->addResource(new AclResource($resource), $actions);
            }
            // Allow access module menu
            if ($role && $role->allowMenu) {
                try {
                    foreach ($this->privateResources['menu'] as $action) {
                        $acl->allow($profile['id'], 'menu', $action);
                    }
                } catch (Exception $e) {
                    debug($e, true);
                }
            }
            if ($role && $role->allowBook) {
                try {
                    foreach ($this->privateResources['books'] as $action) {
                        $acl->allow($profile['id'], 'books', $action);
                    }
                } catch (Exception $e) {
                    debug($e, true);
                }
            }
            if ($role && $role->allowUser) {
                try {
                    foreach ($this->privateResources['users'] as $action) {
                        $acl->allow($profile['id'], 'users', $action);
                    }
                } catch (Exception $e) {
                    debug($e, true);
                }
            }

            $permissions = Permissions::find(array(
                'conditions' => array(
                    'user_id' => new \MongoId($profile['id'])
                )
            ));
            foreach ($permissions as $permission) {
                try {
                    if ($permission->allowView) {
                        $acl->allow($profile['id'], 'books', 'index');
                    }
                    if ($permission->allowEdit) {
                        $acl->allow($profile['id'], 'books', 'edit');
                    }
                    if ($permission->allowDelete) {
                        $acl->allow($profile['id'], 'books', 'delete');
                    }
                    if ($permission->allowTest) {
                        $acl->allow($profile['id'], 'books', 'preview');
                    }
                } catch (Exception $e) {
                    debug($e, true);
                }
            }
        }
        if (touch(APP_DIR . $this->filePath) && is_writable(APP_DIR . $this->filePath)) {
            file_put_contents(APP_DIR . $this->filePath, serialize($acl));

            // Store the ACL in APC
            if (function_exists('apc_store')) {
                apc_store('books-acl', $acl);
            }
        } else {
            $this->flash->error(
                'The user does not have write permissions to create the ACL list at ' . APP_DIR . $this->filePath
            );
        }

        return $acl;
    }

    public function fullAccess($userId, $acl) {
        foreach ($this->privateResources as $resource => $actions) {
            $acl->addResource(new AclResource($resource), $actions);
            foreach ($actions as $action) {
                $acl->allow($userId, $resource, $action);
            }
        }
        return $acl;
    }
}
