<?php
declare(strict_types=1);

/**
 * Famiree(tm) : A simple family tree
 * Copyright (c) Malamalca
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Malamalca
 * @link      https://github.com/malamalca/famiree Famiree(tm) Project
 * @since     3.0.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use App\Lib\AuthUser;
use Cake\Controller\Controller;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;

/**
 * Application Controller
 *
 * @property \Authentication\Controller\Component\AuthenticationComponent $Authentication
 */
class AppController extends Controller
{
    public ?AuthUser $currentUser = null;

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('Flash');
        $this->loadComponent('FormProtection');
        $this->loadComponent('Authentication.Authentication', [
            'logoutRedirect' => ['controller' => 'Users', 'action' => 'login'],
        ]);

        $this->currentUser = new AuthUser($this->identityData());
        $this->viewBuilder()->setOptions(['currentUser' => $this->currentUser]);
    }

    /**
     * Returns data of the logged in user (without password hash) or empty array.
     *
     * @return array
     */
    protected function identityData(): array
    {
        $identity = $this->Authentication->getIdentity();
        if (!$identity) {
            return [];
        }

        $data = $identity->getOriginalData();
        $data = $data instanceof EntityInterface ? $data->toArray() : (array)$data;
        unset($data['p']);

        return $data;
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\EventInterface $event The beforeRender event.
     * @return null
     */
    public function beforeRender(EventInterface $event)
    {
        $this->viewBuilder()
            ->addHelper('Famiree')
            ->addHelper('AssetCompress.AssetCompress');

        return null;
    }
}
