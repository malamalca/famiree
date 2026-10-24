<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\Datasource\Exception\RecordNotFoundException;
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
    protected array $fixtures = [
        'app.Posts',
        'app.Profiles',
        'app.PostsLinks',
        'app.Logs',
    ];

    /**
     * Auth data
     */
    private $authData = [
        'User' => [
            'id' => 1,
            'd_n' => 'Test User',
        ],
    ];

    /**
     * Setup test
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->enableRetainFlashMessages();
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->session(['Auth' => $this->authData['User']]);
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
        $this->session(['Auth' => $this->authData['User']]);
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
        $this->session(['Auth' => $this->authData['User']]);

        $countBefore = $this->fetchTable('Posts')->find()->count();

        $this->post('/posts/edit', [
            'title' => 'Test Memory',
            'body' => 'This is a memory from a test suite.',
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Posts', 'action' => 'index']);
        $this->assertFlashElement('flash/success');

        $countAfter = $this->fetchTable('Posts')->find()->count();
        $this->assertEquals($countBefore + 1, $countAfter);
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->session(['Auth' => $this->authData['User']]);

        $countBefore = $this->fetchTable('Posts')->find()->count();

        $this->post('/posts/edit/1', [
            'id' => 1,
            'title' => 'Test Memory',
            'body' => 'This is an edited memory from a test suite.',
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Posts', 'action' => 'index']);
        $this->assertFlashElement('flash/success');

        $countAfter = $this->fetchTable('Posts')->find()->count();
        $this->assertEquals($countBefore, $countAfter);

        $post = $this->fetchTable('Posts')->get(1);
        $this->assertEquals('Test Memory', $post->title);
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->session(['Auth' => $this->authData['User']]);

        $countBefore = $this->fetchTable('Posts')->find()->count();

        $this->get('/posts/delete/1');

        $this->assertRedirect();

        $countAfter = $this->fetchTable('Posts')->find()->count();
        $this->assertEquals($countBefore - 1, $countAfter);
    }
}
