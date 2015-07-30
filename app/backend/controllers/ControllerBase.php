<?php
namespace Books\Backend\Controllers;

use Books\Backend\Libraries\Breadcrumbs;
use Phalcon\Mvc\Controller;
use Phalcon\Translate\Adapter\NativeArray;
use Phalcon\Mvc\Dispatcher;

class ControllerBase extends Controller {
    protected $admin = null;
    protected $title    = '';
    protected $bc       = null;
    protected $viewVars = [];
    protected $t        = null;
    /**
     * Initializes the controller
     */
    public function initialize() {
        $this->bc = new Breadcrumbs();
        $this->title = $this->tag->getTitle('title');
        $this->view->setTemplateBefore('private');
        $this->view->t = $this->getTranslation();
        $this->t = $this->view->t;
    }

    public function beforeExecuteRoute(Dispatcher $dispatcher) {
        $this->admin = $this->adminAuth->getIdentity();
        $controllerName = $dispatcher->getControllerName();
        // Only check permissions on private controllers
        if ($this->adminAcl->isPrivate($controllerName)) {
            // If there is no identity available the user is redirected to index/index
            if (!is_array($this->admin)) {
                $this->flash->notice('You don\'t have access to this module: private');
                return $this->response->redirect('admin/auth/login');
            }

            // Check if the user have permission to the current option
            $actionName = $dispatcher->getActionName();
            if (!$this->adminAcl->isAllowed($this->admin['id'], $controllerName, $actionName)) {
                if ($this->request->isAjax() == true) {
                    echo json_encode(array('error'=>1,'msg' => 'Bạn không có quyền truy thực hiện chức năng này. Xin vui lòng liên hệ với Administrator.'));
                    exit;
                }
                $this->flash->notice('You don\'t have access to this module: ' . $controllerName . ':' . $actionName);
                return $dispatcher->forward(array(
                    'controller' => 'error',
                    'action' => 'access'
                ));
            }
        }
    }

    /**
     * This sets all the view variables before rendering
     */
    public function afterExecuteRoute() {
        /**
         * This effectively will set the breadcrumbs array in the view
         * and will allow us to render it
         */
        $this->addViewVar('bc', $this->bc->generate());
        $this->addViewVar('title', $this->title);
        $this->addViewVar('admin', $this->admin);
        $this->view->setVars($this->viewVars);
    }

    protected function addViewVar($variable, $value) {
        $this->viewVars[$variable] = $value;
    }

    protected function resetViewVars() {
        $this->viewVars = [];
    }

    protected function getTranslation() {

        //Ask browser what is the best language
        $language = $this->request->getBestLanguage();

        //Check if we have a translation file for that lang
        if (file_exists(APP_PATH."/app/messages/" . $language . ".php")) {
            require APP_PATH."/app/messages/" . $language . ".php";
        } else {
            // fallback to some default
            require APP_PATH."/app/messages/vi.php";
        }

        //Return a translation object
        return new NativeArray(array(
            "content" => $messages
        ));
    }

    protected function extractAction($name)
    {
        $action = explode('Action', $name);
        if ((count($action) > 1)) {
            return $action[0];
        }
    }

    protected function extractController($name)
    {
        $filename = explode('.php', $name);
        if (count(explode('Controller.php', $name)) > 1) {
            if (count($filename) > 1) {
                return $filename[0];
            }
        }

        return false;
    }

    public function json(array $data) {
        $status      = 200;
        $description = 'OK';
        $headers     = array();
        $contentType = 'application/json';
        $content     = json_encode($data);

        $response = new \Phalcon\Http\Response();

        $response->setStatusCode($status, $description);
        $response->setContentType($contentType, 'UTF-8');
        $response->setContent($content);

        // Set the additional headers
        foreach ($headers as $key => $value) {
            $response->setHeader($key, $value);
        }

        $this->view->disable();

        return $response;
    }
}
