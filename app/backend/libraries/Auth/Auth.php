<?php
namespace Books\Backend\Libraries\Auth;
use Books\Backend\Models\Roles;
use Phalcon\Mvc\User\Component;
use Books\Backend\Models\Users;

/**
 * Books\Auth\Auth
 * Manages Authentication/Identity Management in books
 */
class Auth extends Component {

    /**
     * Checks the user credentials
     *
     * @param array $credentials
     * @return boolan
     */
    public function check($credentials){
        $check = false;
        // Check if the user exist
        $user = Users::findFirst(array(
            'conditions' => array('email' => $credentials['email'])
        ));
        if ($user == false) {
            $this->flash->error('Wrong email');
            return false;
        }
        // Check the password
        if (!$this->security->checkHash($credentials['password'], $user->password)) {
            $this->flash->error('Wrong password');
            return false;
        }

        // Check if the user was flagged
        $check = $this->checkUserFlags($user);

        // Register the successful login
        //$this->saveSuccessLogin($user);

        // Check if the remember me was selected
        if (isset($credentials['remember'])) {
            $this->createRememberEnviroment($user);
        }
        if($user->role_id) {
            $role = Roles::findById($user->role_id);
        } else {
            $role = null;
        }
        $this->session->set('auth-admin-identity', array(
            'id' => $user->getId()->{'$id'},
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'role' => $role?array('id' => $role->getId(),'name' => $role->name):null,
        ));
        return $check;
    }

    /**
     * Creates the remember me environment settings the related cookies and generating tokens
     *
     * @param Books\Models\Users $user
     */
    public function saveSuccessLogin($user)
    {
        $successLogin = new SuccessLogins();
        $successLogin->usersId = $user->id;
        $successLogin->ipAddress = $this->request->getClientAddress();
        $successLogin->userAgent = $this->request->getUserAgent();
        if (!$successLogin->save()) {
            $messages = $successLogin->getMessages();
            throw new Exception($messages[0]);
        }
    }

    /**
     * Implements login throttling
     * Reduces the efectiveness of brute force attacks
     *
     * @param int $userId
     */
    public function registerUserThrottling($userId)
    {
        $failedLogin = new FailedLogins();
        $failedLogin->usersId = $userId;
        $failedLogin->ipAddress = $this->request->getClientAddress();
        $failedLogin->attempted = time();
        $failedLogin->save();

        $attempts = FailedLogins::count(array(
            'ipAddress = ?0 AND attempted >= ?1',
            'bind' => array(
                $this->request->getClientAddress(),
                time() - 3600 * 6
            )
        ));

        switch ($attempts) {
            case 1:
            case 2:
                // no delay
                break;
            case 3:
            case 4:
                sleep(2);
                break;
            default:
                sleep(4);
                break;
        }
    }

    /**
     * Creates the remember me environment settings the related cookies and generating tokens
     *
     * @param Books\Models\Users $user
     */
    public function createRememberEnviroment(Users $user)
    {
        $userAgent = $this->request->getUserAgent();
        $token = md5($user->email . $user->password . $userAgent);
        $user->remember_token = $token;
        if ($user->save() != false) {
            $expire = time() + 86400 * 8;
            $this->cookies->set('RMU', $user->id, $expire);
            $this->cookies->set('RMT', $token, $expire);
        }
    }

    /**
     * Check if the session has a remember me cookie
     *
     * @return boolean
     */
    public function hasRememberMe()
    {
        return $this->cookies->has('RMU');
    }

    /**
     * Logs on using the information in the coookies
     *
     * @return Phalcon\Http\Response
     */
    public function loginWithRememberMe()
    {
        $userId = $this->cookies->get('RMU')->getValue();
        $cookieToken = $this->cookies->get('RMT')->getValue();

        $user = Users::find(array(
            'conditions' => array(
                '$and' => array(
                    array('_id' => $userId),
                    array('remember_token' => $cookieToken),
                )
            )
        ));
        if ($user) {
            $userAgent = $this->request->getUserAgent();
            $token = md5($user->email . $user->password . $userAgent);
            if ($cookieToken == $token) {
                $remember = User::find(array(
                    'usersId = ?0 AND token = ?1',
                    'bind' => array(
                        $user->id,
                        $token
                    )
                ));
                if ($remember) {

                    // Check if the cookie has not expired
                    if ((time() - (86400 * 8)) < $remember->createdAt) {

                        // Check if the user was flagged
                        $this->checkUserFlags($user);

                        // Register identity
                        $this->session->set('auth-admin-identity', array(
                            'id' => $user->id,
                            'name' => $user->name,
                            'role_id' => $user->role_id
                        ));

                        // Register the successful login
                        $this->saveSuccessLogin($user);

                        return $this->response->redirect('users');
                    }
                }
            }
        }

        $this->cookies->get('RMU')->delete();
        $this->cookies->get('RMT')->delete();

        return $this->response->redirect('admin/auth/login');
    }

    /**
     * Checks if the user is banned/inactive/suspended
     *
     * @param Books\Models\Users $user
     */
    public function checkUserFlags(Users $user){
        if ($user->active != 1) {
            $this->flash->error('The user is inactive');
            return false;
        }
        return true;
    }

    /**
     * Returns the current identity
     *
     * @return array
     */
    public function getIdentity()
    {
        return $this->session->get('auth-admin-identity');
    }

    /**
     * Returns the current identity
     *
     * @return string
     */
    public function getName()
    {
        $identity = $this->session->get('auth-admin-identity');
        return $identity['name'];
    }

    /**
     * Removes the user identity information from session
     */
    public function remove()
    {
        if ($this->cookies->has('RMU')) {
            $this->cookies->get('RMU')->delete();
        }
        if ($this->cookies->has('RMT')) {
            $this->cookies->get('RMT')->delete();
        }

        $this->session->remove('auth-admin-identity');
    }

    /**
     * Auths the user by his/her id
     *
     * @param int $id
     */
    public function authUserById($id)
    {
        $user = Users::findFirstById($id);
        if ($user == false) {
            throw new Exception('The user does not exist');
        }

        $this->checkUserFlags($user);

        $this->session->set('auth-admin-identity', array(
            'id' => $user->id,
            'name' => $user->name,
            'role_id' => $user->role_id
        ));
    }

    /**
     * Get the entity related to user in the active identity
     *
     * @return \Books\Models\Users
     */
    public function getUser()
    {
        $identity = $this->session->get('auth-admin-identity');
        if (isset($identity['id'])) {

            $user = Users::findFirstById($identity['id']);
            if ($user == false) {
                throw new Exception('The user does not exist');
            }

            return $user;
        }

        return false;
    }
}
