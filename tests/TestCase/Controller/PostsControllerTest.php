<?php
namespace App\Test\TestCase\Controller;

use App\Controller\PostsController;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\PostsController Test Case
 */
class PostsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Posts',
        'app.Profiles',
        'app.PostsLinks',
        'app.Logs'
    ];

    /**
     * Auth data
     */
    private $authData = [
        'User' => [
            'id' => 1,
            'd_n' => 'Test User',
        ]
    ];

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->session(['Auth' => $this->authData]);
        $this->get('/posts');

        $this->assertResponseOk();
        $this->assertNoRedirect();
    }

    /**
     * Test view method
     *
     * @return void
     */
    public function testView()
    {
        $this->session(['Auth' => $this->authData]);
        $this->get('/posts/view/1');

        $this->assertResponseOk();
        $this->assertNoRedirect();

        $this->disableErrorHandlerMiddleware();
        $this->expectException(RecordNotFoundException::class);
        $this->get('/posts/view/1111');
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->session(['Auth' => $this->authData]);
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->enableRetainFlashMessages();

        $countBefore = TableRegistry::get('Posts')->find()->count();

        $this->post('posts/add', [
            'title' => 'Test Memory',
            'body' => 'This is a memory from a test suite.',
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Posts', 'action' => 'index']);
        $this->assertFlashElement('Flash/success');

        $countAfter = TableRegistry::get('Posts')->find()->count();
        $this->assertEquals($countBefore + 1, $countAfter);
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->session(['Auth' => $this->authData]);
        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->enableRetainFlashMessages();

        $countBefore = TableRegistry::get('Posts')->find()->count();

        $this->post('posts/edit/1', [
            'id' => 1,
            'title' => 'Test Memory',
            'body' => 'This is an edited memory from a test suite.',
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Posts', 'action' => 'index']);
        $this->assertFlashElement('Flash/success');

        $countAfter = TableRegistry::get('Posts')->find()->count();
        $this->assertEquals($countBefore, $countAfter);

        $post = TableRegistry::get('Posts')->get(1);
        $this->assertEquals('Test Memory', $post->title);
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->session(['Auth' => $this->authData]);

        $countBefore = TableRegistry::get('Posts')->find()->count();

        $this->get('posts/delete/1');

        $this->assertRedirect();

        $countAfter = TableRegistry::get('Posts')->find()->count();
        $this->assertEquals($countBefore - 1, $countAfter);
    }
}
