<?php

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Phalcon\Mvc\View;
use Books\App\Forms\LoginForm;
use Phalcon\Mvc\Controller;
class AuthController extends Controller {
    protected $identity = null;
    /**
     * Initializes the controller
     */
    public function initialize() {
        $this->view->setTemplateBefore('public');
        $this->identity = $this->auth->getIdentity();
    }

    /**
     * Starts a session in the admin backend
     */
    public function loginAction(){
        $form = new LoginForm();
        try {
            if (is_array($this->identity)) {
                return $this->response->redirect('books');
            }
            if (!$this->request->isPost()) {
                if ($this->auth->hasRememberMe()) {
                    return $this->auth->loginWithRememberMe();
                }
            } else {
                if ($form->isValid($this->request->getPost()) == false) {
                    foreach ($form->getMessages() as $message) {
                        $this->flash->error($message);
                    }
                } else {
                    //Wrong email/password combination
                    if($this->auth->check(array(
                        'email' => $this->request->getPost('email'),
                        'password' => $this->request->getPost('password'),
                        'remember' => $this->request->getPost('remember')
                    ))) {
                        return $this->response->redirect('users');
                    }
                }

            }
        } catch (AuthException $e) {
            $this->flash->error($e->getMessage());
        }
        $this->view->form = $form;
    }

    /**
     * Closes the session
     */
    public function logoutAction() {
        $this->auth->remove();
        return $this->response->redirect('auth/login');
    }
}
