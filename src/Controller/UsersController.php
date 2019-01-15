<?php
/**
 * Users Controller
 */
namespace App\Controller;

use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use Cake\Utility\Security;
use Cake\Validation\Validator;

/**
 * Users Controller
 *
 * This controller manages users.
 *
 */
class UsersController extends AppController
{
    /**
     * Cookie key name
     *
     * @var string
     */
    private $_cookieKey = 'famiree_login';

    /**
     * Initialize method.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('Cookie');
    }
    /**
     * BeforeFilter method.
     *
     * @param Cake\Event\Event $event Cake Event object.
     * @return void
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['logout', 'reset', 'changePassword']);
        if (Configure::read('enableRegistration')) {
            $this->Auth->allow(['register']);
        }
    }
    /**
     * IsAuthorized method.
     *
     * @param array $user Authenticated user.
     * @return bool
     */
    public function isAuthorized($user)
    {
        if (in_array($this->getRequest()->getParam('action'), ['properties'])) {
            return $this->Auth->user('id');
        }

        return parent::isAuthorized($user);
    }

    /**
     * Login method.
     *
     * This method will display login form
     * @return mixed
     */
    public function login()
    {
        if ($this->Auth->user('id')) {
            $this->redirect($this->Auth->redirectUrl());
        }

        if ($user = $this->Auth->identify()) {
            $this->Auth->setUser($user);

            if ($this->Auth->authenticationProvider()->needsPasswordRehash()) {
                $user = TableRegistry::get('Profiles')->get($this->Auth->user('id'));
                $user->p = $this->request->getData('p');
                TableRegistry::get('Profiles')->save($user);
            }

            // set cookie
            if (!empty($this->getRequest()->getData('remember_me'))) {
                if ($CookieAuth = $this->Auth->getAuthenticate('Cookie')) {
                    $CookieAuth->createCookie($this->getRequest()->data);
                }
            }
        } else {
            if ($this->getRequest()->is('post') || env('PHP_AUTH_USER')) {
                $this->Flash->error(__('Invalid username or password, try again'));
            }
        }

        if ($this->Auth->user('id')) {
            $redirect = $this->Auth->redirectUrl();

            return $this->redirect($redirect);
        }
    }

    /**
     * Logout method
     *
     * @return mixed
     */
    public function logout()
    {
        if ($CookieAuth = $this->Auth->getAuthenticate('Cookie')) {
            $CookieAuth->deleteCookie();
        }

        return $this->redirect($this->Auth->logout());
    }

    /**
     * Reset method
     *
     * @return void
     */
    public function reset()
    {
        if ($this->Auth->user()) {
            $this->redirect($this->Auth->loginRedirect);
        }

        if ($this->getRequest()->is('post')) {
            $user = TableRegistry::get('Profiles')->find()
                ->select()
                ->where(['e' => $this->getRequest()->getData('email')])
                ->first();

            if ($user) {
                TableRegistry::get('Profiles')->sendResetEmail($user);
                $this->Flash->success(__('An email with password reset instructions has been sent.'));
            } else {
                $this->Flash->error(__('No user with specified email has been found.'));
            }
        }
    }
    /**
     * Change users password
     *
     * @param string $resetKey Auto generated reset key.
     * @return void
     */
    public function changePassword($resetKey = null)
    {
        if (!$resetKey) {
            throw new NotFoundException(__('Reset key does not exist.'));
        }

        $user = TableRegistry::get('Profiles')->findByRst($resetKey)->first();
        if (!$user) {
            throw new NotFoundException(__('User does not exist.'));
        }

        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            TableRegistry::get('Profiles')->patchEntity($user, $this->getRequest()->getData(), ['validate' => 'resetPassword']);

            if (!$user->getErrors() && TableRegistry::get('Profiles')->save($user)) {
                $this->Flash->success(__('Password has been changed.'));
                $this->redirect('/');
            } else {
                $this->Flash->error(__('Please verify that the information is correct.'));
            }
        } else {
            $user->p = null;
        }

        $this->set(compact('user'));
    }
}
