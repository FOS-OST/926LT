<?php
namespace Books\Backend\Controllers;

use Books\Backend\Libraries\Breadcrumbs;
use Phalcon\Mvc\Controller;
use Phalcon\Translate\Adapter\NativeArray;

class ControllerBase extends Controller {
    protected $admin = null;
    protected $title    = '';
    protected $bc       = null;
    protected $viewVars = [];
    protected $t        = null;
    /**
     * Initializes the controller
     */
    public function initialize()
    {
        $this->bc = new Breadcrumbs();
        $this->title = $this->tag->getTitle('title');
        $this->view->setTemplateBefore('private');
        $this->admin = $this->adminAuth->getIdentity();
        $this->view->t = $this->getTranslation();
        $this->t = $this->view->t;
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
        if (!is_array($this->admin)) {
            return $this->response->redirect('admin/auth/login');
        }
    }

    protected function addViewVar($variable, $value)
    {
        $this->viewVars[$variable] = $value;
    }

    protected function resetViewVars()
    {
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
