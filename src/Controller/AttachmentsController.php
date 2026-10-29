<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\I18n\Date;
use Cake\Utility\Text;
use Laminas\Diactoros\UploadedFile;

/**
 * Attachments Controller
 *
 * @property \App\Model\Table\AttachmentsTable $Attachments
 * @method \App\Model\Entity\Attachment[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AttachmentsController extends AppController
{
    public array $paginate = [
        'limit' => 25,
        'order' => [
            'Attachments.created' => 'asc',
        ],
    ];

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $q = $this->Attachments->find();
        if ($this->getRequest()->getQuery('filter') != 'all') {
            $q->where(['created >=' => (new Date())->subMonths(3)]);
        }

        $attachments = $this->paginate($q);

        $this->set(compact('attachments'));
    }

    /**
     * Serve up files directly from the uploads folder.
     *
     * @param string $id Attachment id
     * @param string $size Attachment size
     * @param string|null $name Attachment name
     * @return \Cake\Http\Response
     */
    public function display(string $id, string $size = 'original', ?string $name = null): Response
    {
        /** @var \App\Model\Entity\Attachment $attachment */
        $attachment = $this->Attachments->get($id);

        // correct slug
        $a_description = Text::slug($attachment->title);
        if (!$a_description) {
            $a_description = $attachment->original;
        } else {
            $a_description = str_replace('-' . $attachment->ext, '.' . $attachment->ext, $a_description);
            if (strpos('.' . $attachment->ext, $a_description) === false) {
                $a_description .= '.' . $attachment->ext;
            }
        }
        if ($name != $a_description) {
            $this->redirect([$id, $size, $a_description], 301);
        }

        $filePath = Configure::read('sourceFolders.attachments') . $attachment->id . DS . $size;
        if (!file_exists($filePath)) {
            throw new NotFoundException(__('Attachment does not exist.'));
        }

        $response = $this->response
            ->withType($attachment->mimetype)
            ->withFile(
                $filePath,
                ['name' => $a_description, 'download' => (bool)$this->getRequest()->getParam('download')],
            );

        return $response;
    }

    /**
     * View method
     *
     * @param string|null $id Attachment id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view(?string $id = null)
    {
        $attachment = $this->Attachments->get($id, contain: ['Creators', 'Imgnotes', 'AttachmentsLinks.Profiles']);

        $this->set('attachment', $attachment);
    }

    /**
     * Edit method
     *
     * @param string|null $id Attachment id.
     * @return \Cake\Http\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit(?string $id = null)
    {
        if ($id) {
            $attachment = $this->Attachments->get($id, contain: ['AttachmentsLinks']);
        } else {
            $attachment = $this->Attachments->newEmptyEntity();
        }
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $attachment = $this->Attachments->patchEntity(
                $attachment,
                $this->getRequest()->getData(),
                ['AttachmentsLinks'],
            );

            if ($this->Attachments->save($attachment)) {
                if ($this->getRequest()->getData('attachment') instanceof UploadedFile) {
                    $this->Attachments->processUpload($attachment, $this->getRequest()->getData('attachment'));
                }
                $this->Flash->success(__('The attachment has been saved.'));

                $referer = base64_decode($this->getRequest()->getData('referer', ''));
                if ($referer) {
                    return $this->redirect($referer);
                } else {
                    return $this->redirect(['action' => 'index']);
                }
            }
            $this->Flash->error(__('The attachment could not be saved. Please, try again.'));
        }
        $this->set(compact('attachment'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Attachment id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(?string $id = null): ?Response
    {
        $this->getRequest()->allowMethod(['get', 'post', 'delete']);
        $attachment = $this->Attachments->get($id);
        if ($this->Attachments->delete($attachment)) {
            $this->Flash->success(__('The attachment has been deleted.'));
        } else {
            $this->Flash->error(__('The attachment could not be deleted. Please, try again.'));
        }

        return $this->redirect($this->getRequest()->getQuery('redirect', $this->referer()));
    }
}
