<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Http\Response;

/**
 * Imgnotes Controller
 *
 * @property \App\Model\Table\ImgnotesTable $Imgnotes
 * @method \App\Model\Entity\Imgnote[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ImgnotesController extends AppController
{
    /**
     * Edit method
     *
     * @param string|null $id Imgnote id.
     * @return \Cake\Http\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        if ($id) {
            $imgnote = $this->Imgnotes->get($id);
        } else {
            $imgnote = $this->Imgnotes->newEmptyEntity();
        }
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $imgnote = $this->Imgnotes->patchEntity($imgnote, $this->getRequest()->getData());

            if ($this->Imgnotes->save($imgnote)) {
                /** @var \App\Model\Table\AttachmentsLinksTable $AttachmentsLinksTable */
                $AttachmentsLinksTable = $this->fetchTable('AttachmentsLinks');
                if ($this->getRequest()->getData('crop_to_new')) {
                    /** @var \App\Model\Table\AttachmentsTable $AttachmentsTable */
                    $AttachmentsTable = $this->fetchTable('Attachments');

                    /** @var \App\Model\Entity\Attachment $attachment */
                    $attachment = $AttachmentsTable->createFromImgnote($imgnote);

                    if ($attachment) {
                        $AttachmentsLinksTable->linkProfile($imgnote->profile_id, $attachment->id);
                    }
                } else {
                    $AttachmentsLinksTable->linkProfile($imgnote->profile_id, $imgnote->attachment_id);
                }
                $this->Flash->success(__('The imgnote has been saved.'));

                $referer = base64_decode($this->getRequest()->getData('referer', ''));
                if ($referer) {
                    return $this->redirect($referer);
                } else {
                    return $this->redirect(
                        ['controller' => 'Attachments', 'action' => 'view', $imgnote->attachment_id],
                    );
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
    public function delete(?string $id = null): ?Response
    {
        $this->getRequest()->allowMethod(['post', 'delete', 'get']);
        $imgnote = $this->Imgnotes->get($id);
        if ($this->Imgnotes->delete($imgnote)) {
            $this->Flash->success(__('The imgnote has been deleted.'));
        } else {
            $this->Flash->error(__('The imgnote could not be deleted. Please, try again.'));
        }

        return $this->redirect($this->referer());
    }
}
