<?php
/**
 * Users Controller
 */
namespace App\Controller;

use Cake\Auth\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Http\Exception\NotFoundException;
use Cake\I18n\FrozenTime;
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
     * @param \Cake\Event\Event $event Cake Event object.
     * @return \Cake\Http\Response|null
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->Auth->allow(['logout', 'reset', 'changePassword', 'install']);
        if (Configure::read('enableRegistration')) {
            $this->Auth->allow(['register']);
        }

        return null;
    }
    /**
     * IsAuthorized method.
     *
     * @param array $user Authenticated user.
     * @return bool
     */
    public function isAuthorized($user)
    {
        if (in_array($this->getRequest()->getParam('action'), ['settings'])) {
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
            /** @var \App\Model\Entity\Profile $user */
            $user = TableRegistry::get('Profiles')->get($this->Auth->user('id'));
            $user->last_login = new FrozenTime();

            if ($this->Auth->authenticationProvider()->needsPasswordRehash()) {
                $user->p = $this->request->getData('p');
            }

            TableRegistry::get('Profiles')->save($user);

            // set cookie
            if (!empty($this->getRequest()->getData('remember_me'))) {
                /** @var \App\Auth\CookieAuthenticate $CookieAuth */
                $CookieAuth = $this->Auth->getAuthenticate('Cookie');
                if (!empty($CookieAuth)) {
                    $CookieAuth->createCookie($this->getRequest()->getData());
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
        /** @var \App\Auth\CookieAuthenticate $CookieAuth */
        $CookieAuth = $this->Auth->getAuthenticate('Cookie');
        if (!empty($CookieAuth)) {
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
            $redirect = $this->Auth->redirectUrl();

            $this->redirect($redirect);
        }

        if ($this->getRequest()->is('post')) {
            /** @var \App\Model\Table\ProfilesTable $ProfilesTable */
            $ProfilesTable = TableRegistry::get('Profiles');
            $user = $ProfilesTable->find()
                ->select()
                ->where(['e' => $this->getRequest()->getData('email')])
                ->first();

            if ($user) {
                $ProfilesTable->sendResetEmail($user);
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

        /** @var \App\Model\Table\ProfilesTable $ProfilesTable */
        $ProfilesTable = TableRegistry::get('Profiles');
        $user = $ProfilesTable->find()->select()->where(['rst' => $resetKey])->first();

        if (empty($user)) {
            throw new NotFoundException(__('User does not exist.'));
        }

        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $ProfilesTable->patchEntity($user, $this->getRequest()->getData(), ['validate' => 'resetPassword']);

            if (!$user->getErrors() && $ProfilesTable->save($user)) {
                $this->Flash->success(__('Password has been changed.'));
                $this->redirect('/');
            } else {
                $this->Flash->error(__('Please verify that the information is correct.'));
            }
        } else {
            $user->set('p', null);
        }

        $this->set(compact('user'));
    }

    /**
     * Install first user
     *
     * @return void
     */
    public function install()
    {
        /** @var \App\Model\Table\ProfilesTable $ProfilesTable */
        $ProfilesTable = TableRegistry::get('Profiles');

        $userCount = $ProfilesTable->find()->count();
        if ($userCount > 0) {
            $this->redirect('/');
        }

        $user = $ProfilesTable->newEntity();
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $ProfilesTable->patchEntity($user, $this->getRequest()->getData(), ['validate' => 'install']);

            if (!$user->getErrors() && $ProfilesTable->save($user)) {
                $this->Flash->success(__('User has been added.'));
                $this->redirect('/');
            } else {
                $this->Flash->error(__('Please verify that the information is correct.'));
            }
        } else {
            $user->set('p', null);
        }

        $this->set(compact('user'));
    }

    /**
     * Change users settings
     *
     * @return void
     */
    public function settings()
    {
        /** @var \App\Model\Table\ProfilesTable $ProfilesTable */
        $ProfilesTable = TableRegistry::get('Profiles');
        $user = $ProfilesTable->get($this->currentUser->get('id'));

        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $ProfilesTable->patchEntity($user, $this->getRequest()->getData(), ['validate' => 'settings']);
            if (empty($this->getRequest()->getData('p'))) {
                $user->setDirty('p', false);
                unset($user->p);
            }

            if (!$user->getErrors() && $ProfilesTable->save($user)) {
                $this->Flash->success(__('User settings have been changed.'));
                $this->redirect('/');
            } else {
                $this->Flash->error(__('Please verify that the information is correct.'));
            }
        }

        $this->set(compact('user'));
    }
}
