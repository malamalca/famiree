<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Hash;

/**
 * Profiles Controller
 *
 * @property \App\Model\Table\ProfilesTable $Profiles
 *
 * @method \App\Model\Entity\Profile[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ProfilesController extends AppController
{
    /**
     * isAuthorized hook method.
     *
     * @param object $user Logged in user.
     * @return bool
     */
    public function isAuthorized($user)
    {
        return true;
    }

    /**
     * Dashboard method
     *
     * @return \Cake\Http\Response|void
     */
    public function dashboard()
    {
        $profiles = $this->paginate($this->Profiles);
        $counts = $this->Profiles->countGenders();
        $posts = TableRegistry::get('Posts')->find()
            ->select()
            ->contain(['Profiles', 'Creators'])
            ->order(['Posts.created DESC'])
            ->limit(5)
            ->all();
        $logs = TableRegistry::get('Logs')->find()
            ->select()
            ->contain(['Profiles', 'Imgnotes', 'Attachments', 'Posts', 'Users'])
            ->order(['Logs.created DESC'])
            ->limit(10)
            ->all();

        $this->set(compact('profiles', 'counts', 'posts', 'logs'));
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $criterio = $this->getRequest()->getQuery('criterio');
        $this->paginate = ['limit' => 10, 'finder' => ['search' => ['criterio' => $criterio]]];

        $profiles = $this->paginate($this->Profiles);

        $this->set(compact('profiles', 'criterio'));
    }

    /**
     * Main tree function
     *
     * @return void
     */
    public function tree()
    {
        $this->viewBuilder()->enableAutoLayout(false);

        $current_profile = $this->getRequest()->getParam('pass.0', $this->currentUser->get('id'));
        $tree = $this->Profiles->tree($current_profile, 100);
        $this->set(compact('tree', 'current_profile'));
    }

    /**
     * View method
     *
     * @param string|null $id Profile id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        if (empty($id)) {
            $id = $this->currentUser->get('id');
        }

        $profile = $this->Profiles->get($id, ['contain' => ['Creators']]);
        $family = $this->Profiles->family($id);

        $attachments = TableRegistry::get('Attachments')->fetchForProfile($id);
        $posts = TableRegistry::get('Posts')->fetchForProfile($id);

        $this->set(compact('profile', 'family', 'posts', 'attachments'));
    }

    /**
     * Add child method
     *
     * @param int $parentId Id of parent profile
     * @return void
     */
    public function addChild($parentId)
    {
        $this->setAction('add', 'child', $parentId);
    }

    /**
     * Add partner method
     *
     * @param int $spouseId Id of existing spouse
     * @return void
     */
    public function addPartner($spouseId)
    {
        $this->setAction('add', 'spouse', $spouseId);
    }

    /**
     * Add partner method
     *
     * @param int $childId Id of existing spouse
     * @return void
     */
    public function addParent($childId)
    {
        $this->setAction('add', 'parent', $childId);
    }

    /**
     * Add sibling method
     *
     * @param int $siblingId Id of existing sibling
     * @return void
     */
    public function addSibling($siblingId)
    {
        $this->setAction('add', 'sibling', $siblingId);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null
     */
    public function add($relationship, $profileId = null)
    {
        $baseProfile = $this->Profiles->get($profileId);

        switch ($relationship) {
            case 'child':
            case 'spouse':
                $baseProfileKind = 'p';
                $marriages = $this->Profiles->family($profileId, 'marriages');
                $this->set(compact('marriages'));
                break;
            case 'parent':
                $baseProfileKind = 'c'; // adding a parent so base profile is child
                $family = $this->Profiles->family($profileId, ['parents', 'siblings']);
                if (isset($family['parents']) && count($family['parents']) == 2) {
                    $this->Flash->error(__('The profile already has two parents.'));

                    return $this->redirect($this->getRequest()->referer());
                }
                $this->set(compact('family'));
                break;
            case 'sibling':
                $baseProfileKind = 'c';
                $siblings = $this->Profiles->family($profileId, 'siblings');
                $this->set(compact('siblings'));
                break;
        }

        $profile = $this->Profiles->newEntity([], ['associated' => ['Units']]);
        $profile->l = true;

        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $profile = $this->Profiles->patchEntity($profile, $this->getRequest()->getData(), ['associated' => ['Units']]);

            if ($this->Profiles->save($profile)) {
                // create new family with base profile in neccessary
                if (empty($profile->units[0]->union_id)) {
                    $union = TableRegistry::get('Unions')->newEntity();
                    $union->units = [
                        $profile->units[0],
                        TableRegistry::get('Units')->newEntity(['profile_id' => $baseProfile->id, 'kind' => $baseProfileKind])
                    ];
                    $union = TableRegistry::get('Unions')->save($union);
                }

                $this->Flash->success(__('The profile has been saved.'));

                return $this->redirect(['action' => 'view', $profile->id]);
            }
            $this->Flash->error(__('The profile could not be saved. Please, try again.'));
        }

        $this->set(compact('relationship', 'baseProfile', 'profile'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Profile id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $profile = $this->Profiles->get($id, ['contain' => ['Marriages' => ['Profiles']]]);
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $profile = $this->Profiles->patchEntity($profile, $this->getRequest()->getData(), ['associated' => ['Marriages']]);
            if ($this->Profiles->save($profile)) {
                $this->Flash->success(__('The profile has been saved.'));

                return $this->redirect(['action' => 'view', $profile->id]);
            }
            $this->Flash->error(__('The profile could not be saved. Please, try again.'));
        }
        $this->set(compact('profile'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Profile id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->getRequest()->allowMethod(['get', 'post', 'delete']);
        $profile = $this->Profiles->get($id);
        if ($this->Profiles->delete($profile)) {
            $this->Flash->success(__('The profile has been deleted.'));
        } else {
            $this->Flash->error(__('The profile could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'tree']);
    }

    /**
     * Change avatar
     *
     * @param int $id Profile id
     * @param uuid $attachmentId Attachment id to be set as new avatar (optional)
     * @return \Cake\Http\Response|void
     */
    public function editAvatar($id = null, $attachmentId = null)
    {
        $profile = $this->Profiles->get($id);
        $attachments = TableRegistry::get('Attachments')->fetchForProfile($id);

        if (!empty($attachmentId)) {
            if ($attachmentId == 'remove') {
                $profile->ta = null;
            } else {
                $attachment = TableRegistry::get('Attachments')->get($attachmentId);
                $profile->ta = $attachment->id;
            }

            if ($this->Profiles->save($profile)) {
                $this->Flash->success(__('Profile\'s avatar has been successfully saved.'));
            } else {
                $this->Flash->error(__('Profile\'s avatar could not be changed.'));
            }

            return $this->redirect(['controller' => 'Profiles', 'action' => 'view', $id]);
        }

        $this->set(compact('profile', 'attachments'));
    }

    /**
     * Reorder children function
     *
     * @param uuid $id Contact id
     * @return void
     */
    public function reorderChildren($id = null)
    {
        $profile = $this->Profiles->get($id);
        $marriages = $this->Profiles->family($id, 'marriages');

        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $unitIds = Hash::extract($this->getRequest()->getData('units', []), '{n}.id');
            if (count($unitIds) > 0) {
                $unitsTable = TableRegistry::get('Units');
                $units = $unitsTable->find()
                    ->select(['id', 'sort_order'])
                    ->where(['id IN' => $unitIds])
                    ->all();

                $unitsTable->patchEntities($units, $this->getRequest()->getData('units'));
                $result = $unitsTable->saveMany($units);
            }

            $this->redirect(['action' => 'view', $id]);
        }

        $this->set('parent_id', $id);
        $this->set(compact('profile', 'marriages', 'referer'));
    }

    /**
     * Autocomplete method
     *
     * @return \Cake\Http\Response|void
     */
    public function autocomplete()
    {
        if ($this->getRequest()->is(['ajax', 'get']) && ($term = $this->getRequest()->getQuery('q'))) {
            $ret = '';

            // fire autocomplete with at least 2 characters
            if (strlen($term) > 1) {
                $profiles = $this->Profiles->find()
                    ->select(['id', 'd_n'])
                    ->where(['d_n LIKE' => '%' . $term . '%'])
                    ->limit(50)
                    ->all();

                foreach ($profiles as $p) {
                    //$ret[] = ['label' => $p->d_n, 'value' => $p->id];
                    $ret .= $p->id . '|' . h($p->d_n) . chr(10);
                }
            }
            $this->response = $this->response->withStringBody($ret);

            return $this->response;
        } else {
            throw new NotFoundException(__('Invalid ajax call.'));
        }
    }
}