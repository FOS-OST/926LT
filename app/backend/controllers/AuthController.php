<?php
/**
 * Created by PhpStorm.
 * User: HIEUTRIEU
 * Date: 7/25/2015
 * Time: 2:33 PM
 */
namespace Books\Backend\Controllers;

use Books\Backend\Forms\LoginForm;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Controller;

class AuthController extends Controller {
    protected $admin = null;
    /**
     * Initializes the controller
     */
    public function initialize() {
        $this->view->setTemplateBefore('public');
        $this->admin = $this->adminAuth->getIdentity();
    }

    /**
     * Starts a session in the admin backend
     */
    public function loginAction(){
        $form = new LoginForm();
        try {
            if (is_array($this->admin)) {
                return $this->response->redirect('admin/dashboard/index');
            }
            if (!$this->request->isPost()) {
                if ($this->adminAuth->hasRememberMe()) {
                    return $this->auth->loginWithRememberMe();
                }
            } else {
                if ($form->isValid($this->request->getPost()) == false) {
                    foreach ($form->getMessages() as $message) {
                        $this->flash->error($message);
                    }
                } else {
                    //Wrong email/password combination
                    if($this->adminAuth->check(array(
                        'email' => $this->request->getPost('email'),
                        'password' => $this->request->getPost('password'),
                        'remember' => $this->request->getPost('remember')
                    ))) {
                        return $this->response->redirect('admin/dashboard/index');
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
        return $this->response->redirect('admin/auth/login');
    }
}
