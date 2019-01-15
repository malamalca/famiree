<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * Imgnotes Controller
 *
 * @property \App\Model\Table\ImgnotesTable $Imgnotes
 *
 * @method \App\Model\Entity\Imgnote[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ImgnotesController extends AppController
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
     * Add method
     *
     * @return void
     */
    public function add()
    {
        $this->setAction('edit');
    }

    /**
     * Edit method
     *
     * @param string|null $id Imgnote id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if ($id) {
            $imgnote = $this->Imgnotes->get($id);
        } else {
            $imgnote = $this->Imgnotes->newEntity();
        }
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $imgnote = $this->Imgnotes->patchEntity($imgnote, $this->getRequest()->getData());

            if ($this->Imgnotes->save($imgnote)) {
                if ($this->getRequest()->getData('crop_to_new')) {
                    if ($attachment = TableRegistry::get('Attachments')->createFromImgnote($imgnote)) {
                        TableRegistry::get('AttachmentsLinks')->linkProfile($imgnote->profile_id, $attachment->id);
                    }
                } else {
                    TableRegistry::get('AttachmentsLinks')->linkProfile($imgnote->profile_id, $imgnote->attachment_id);
                }
                $this->Flash->success(__('The imgnote has been saved.'));

                if ($referer = base64_decode($this->getRequest()->getData('referer', ''))) {
                    return $this->redirect($referer);
                } else {
                    return $this->redirect(['action' => 'index']);
                }
            }
            $this->Flash->error(__('The imgnote could not be saved. Please, try again.'));
        }
        $users = $this->Imgnotes->Users->find('list', ['limit' => 200]);
        $attachments = $this->Imgnotes->Attachments->find('list', ['limit' => 200]);
        $profiles = $this->Imgnotes->Profiles->find('list', ['limit' => 200]);
        $this->set(compact('imgnote', 'users', 'attachments', 'profiles'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Imgnote id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete', 'get']);
        $imgnote = $this->Imgnotes->get($id);
        if ($this->Imgnotes->delete($imgnote)) {
            $this->Flash->success(__('The imgnote has been deleted.'));
        } else {
            $this->Flash->error(__('The imgnote could not be deleted. Please, try again.'));
        }

        return $this->redirect($this->getRequest()->referer());
    }
}
