<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * AttachmentsLinks Controller
 *
 * @property \App\Model\Table\AttachmentsLinksTable $AttachmentsLinks
 *
 * @method \App\Model\Entity\AttachmentsLink[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AttachmentsLinksController extends AppController
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
     * @param string|null $id Attachments Link id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if ($id) {
            $attachmentsLink = $this->AttachmentsLinks->get($id);
        } else {
            $attachmentsLink = $this->AttachmentsLinks->newEntity();
        }
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $attachmentsLink = $this->AttachmentsLinks->patchEntity($attachmentsLink, $this->getRequest()->getData());
            if ($this->AttachmentsLinks->save($attachmentsLink)) {
                $this->Flash->success(__('The attachments link has been saved.'));

                if ($referer = base64_decode($this->getRequest()->getData('referer', ''))) {
                    return $this->redirect($referer);
                } else {
                    return $this->redirect(['controller' => 'Attachments', 'action' => 'index']);
                }
            }
            $this->Flash->error(__('The attachments link could not be saved. Please, try again.'));
        }
        $attachments = $this->AttachmentsLinks->Attachments->find('list', ['limit' => 200]);
        $profiles = $this->AttachmentsLinks->Profiles->find('list', ['limit' => 200]);
        $this->set(compact('attachmentsLink', 'attachments', 'profiles'));
    }

}
