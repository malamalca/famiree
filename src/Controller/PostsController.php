<?php
declare(strict_types=1);

namespace App\Controller;


/**
 * Posts Controller
 *
 * @property \App\Model\Table\PostsTable $Posts
 * @method \App\Model\Entity\Post[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PostsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        //$this->paginate = ['limit' => 10, 'contain' => ['Creators', 'Profiles'], 'order' => ['Posts.created DESC']];
        $posts = $this->paginate($this->Posts, ['finder' => 'paginated']);

        $this->set(compact('posts'));
    }

    /**
     * View method
     *
     * @param string|null $id Post id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $post = $this->Posts->get($id, contain: ['Profiles', 'Creators']);
        $this->set('post', $post);
    }

    /**
     * Edit method
     *
     * @param string|null $id Post id.
     * @return \Cake\Http\Response|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        if ($id) {
            $post = $this->Posts->get($id, contain: ['PostsLinks']);
        } else {
            $post = $this->Posts->newEmptyEntity();
        }
        if ($this->getRequest()->is(['patch', 'post', 'put'])) {
            $post = $this->Posts->patchEntity($post, $this->getRequest()->getData(), ['PostsLinks']);
            if ($this->Posts->save($post)) {
                $this->Flash->success(__('The post has been saved.'));

                $referer = base64_decode($this->getRequest()->getData('referer', ''));
                if ($referer) {
                    return $this->redirect($referer);
                } else {
                    return $this->redirect(['action' => 'index']);
                }
            }
            $this->Flash->error(__('The post could not be saved. Please, try again.'));
        }
        $creators = $this->fetchTable('Profiles')->find('list')->orderBy('ln');
        $this->set(compact('post', 'creators'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Post id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->getRequest()->allowMethod(['post', 'delete', 'get']);
        $post = $this->Posts->get($id);
        if ($this->Posts->delete($post)) {
            $this->Flash->success(__('The post has been deleted.'));
        } else {
            $this->Flash->error(__('The post could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
