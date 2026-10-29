<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Response;

/**
 * AttachmentsLinks Controller
 *
 * @property \App\Model\Table\AttachmentsLinksTable $AttachmentsLinks
 * @method \App\Model\Entity\AttachmentsLink[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AttachmentsLinksController extends AppController
{
    /**
     * Edit method
     *
     * @param string|null $id Attachments Link id.
     * @return \Cake\Http\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        if ($id) {
            $attachmentsLink = $this->AttachmentsLinks->get($id);
        } else {
            $attachmentsLink = $this->AttachmentsLinks->newEmptyEntity();
        }
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $attachmentsLink = $this->AttachmentsLinks->patchEntity($attachmentsLink, $this->getRequest()->getData());
            if ($this->AttachmentsLinks->save($attachmentsLink)) {
                $this->Flash->success(__('The attachments link has been saved.'));

                $referer = base64_decode($this->getRequest()->getData('referer', ''));
                if ($referer) {
                    return $this->redirect($referer);
                } else {
                    return $this->redirect(
                        ['controller' => 'Attachments', 'action' => 'view', $attachmentsLink->attachment_id],
                    );
                }
            }
            $this->Flash->error(__('The attachments link could not be saved. Please, try again.'));
        }
    }

    /**
     * Delete method
     *
     * @param string|null $id Attachments Link id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null): ?Response
    {
        $this->getRequest()->allowMethod(['post', 'delete', 'get']);
        $imgnote = $this->AttachmentsLinks->get($id);
        if ($this->AttachmentsLinks->delete($imgnote)) {
            $this->Flash->success(__('The attachments link has been deleted.'));
        } else {
            $this->Flash->error(__('The attachments link could not be deleted. Please, try again.'));
        }

        return $this->redirect($this->referer());
    }
}
