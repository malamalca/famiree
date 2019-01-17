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
     * @param array $user Logged in user.
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
                /** @var \App\Model\Table\AttachmentsLinksTable $AttachmentsLinksTable */
                $AttachmentsLinksTable = TableRegistry::get('AttachmentsLinks');
                if ($this->getRequest()->getData('crop_to_new')) {
                    /** @var \App\Model\Table\AttachmentsTable $AttachmentsTable */
                    $AttachmentsTable = TableRegistry::get('Attachments');
                    /** @var \App\Model\Entity\Attachment $attachment */
                    if ($attachment = $AttachmentsTable->createFromImgnote($imgnote)) {
                        $AttachmentsLinksTable->linkProfile($imgnote->profile_id, $attachment->id);
                    }
                } else {
                    $AttachmentsLinksTable->linkProfile($imgnote->profile_id, $imgnote->attachment_id);
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

        $this->set(compact('imgnote'));
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
