<?php
declare(strict_types=1);

/**
 * Users Controller
 */
namespace App\Controller;

use Authentication\Identifier\PasswordIdentifier;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;

/**
 * Users Controller
 *
 * This controller manages users.
 */
class UsersController extends AppController
{
    /**
     * BeforeFilter method.
     *
     * @param \Cake\Event\EventInterface $event Cake Event object.
     * @return \Cake\Http\Response|null
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        $allowed = ['login', 'logout', 'reset', 'changePassword', 'install'];
        if (Configure::read('enableRegistration')) {
            $allowed[] = 'register';
        }
        $this->Authentication->allowUnauthenticated($allowed);

        return null;
    }

    /**
     * Login method.
     *
     * This method will display login form
     *
     * @return \Cake\Http\Response|null
     */
    public function login()
    {
        $result = $this->Authentication->getResult();

        if ($result && $result->isValid()) {
            if ($this->getRequest()->is('post')) {
                $this->rehashPassword();
            }

            $target = $this->Authentication->getLoginRedirect() ?? '/';

            return $this->redirect($target);
        }

        if ($this->getRequest()->is('post')) {
            $this->Flash->error(__('Invalid username or password, try again'));
        }

        return null;
    }

    /**
     * Logout method
     *
     * @return \Cake\Http\Response|null
     */
    public function logout()
    {
        $target = $this->Authentication->logout();

        return $this->redirect($target);
    }

    /**
     * Stores a fresh password hash when the logged in user still has an old (sha1) one.
     *
     * @return void
     */
    protected function rehashPassword(): void
    {
        $identifier = $this->Authentication->getAuthenticationService()->getIdentificationProvider();
        if ($identifier instanceof PasswordIdentifier && $identifier->needsPasswordRehash()) {
            /** @var \App\Model\Entity\Profile $user */
            $user = $this->fetchTable('Profiles')->get($this->currentUser->get('id'));
            $user->p = (string)$this->getRequest()->getData('p');
            $this->fetchTable('Profiles')->save($user);
        }
    }

    /**
     * Reset method
     *
     * @return void
     */
    public function reset()
    {
        if ($this->currentUser->exists()) {
            return $this->redirect('/');
        }

        if ($this->getRequest()->is('post')) {
            /** @var \App\Model\Table\ProfilesTable $ProfilesTable */
            $ProfilesTable = $this->fetchTable('Profiles');
            /** @var \App\Model\Entity\Profile $user */
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
        $ProfilesTable = $this->fetchTable('Profiles');
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
        $ProfilesTable = $this->fetchTable('Profiles');

        $userCount = $ProfilesTable->find()->count();
        if ($userCount > 0) {
            $this->redirect('/');
        }

        $user = $ProfilesTable->newEmptyEntity();
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $ProfilesTable->patchEntity($user, $this->getRequest()->getData(), ['validate' => 'install']);

            if (!$user->getErrors() && $ProfilesTable->save($user)) {
                $this->Flash->success(__('User has been added.'));
                $this->redirect('/');
            } else {
                $this->Flash->error(__('Please verify that the information is correct.'));
            }
        } else {
            $user->set('p', '');
        }

        $this->set(compact('user'));
    }
}
