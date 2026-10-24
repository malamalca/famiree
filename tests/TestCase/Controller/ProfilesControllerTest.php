<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\ProfilesController Test Case
 */
class ProfilesControllerTest extends TestCase
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
        'app.Attachments',
        'app.AttachmentsLinks',
        'app.Imgnotes',
        'app.Units',
        'app.Unions',
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
     * Test dashboard method
     *
     * @return void
     */
    public function testDashboard()
    {
        $this->session(['Auth' => $this->authData['User']]);
        $this->get('/profiles/dashboard');

        $this->assertResponseOk();
        $this->assertNoRedirect();
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->session(['Auth' => $this->authData['User']]);
        $this->get('/profiles/index');

        $this->assertResponseOk();
        $this->assertNoRedirect();
    }

    /**
     * Test tree method
     *
     * @return void
     */
    public function testTree()
    {
        $this->session(['Auth' => $this->authData['User']]);
        $this->get('/profiles/tree');

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
        $this->get('/profiles/view/2');

        $this->assertResponseOk();
        $this->assertNoRedirect();
    }

    /**
     * Test add child method
     *
     * @return void
     */
    public function testAddChild()
    {
        $this->session(['Auth' => $this->authData['User']]);

        $profilesCount = $this->fetchTable('Profiles')->find()->count();
        $unionsCount = $this->fetchTable('Unions')->find()->count();
        $unitsCount = $this->fetchTable('Units')->find()->count();

        $this->post('/profiles/add/child/1', [
            'ta' => null,
            'ln' => 'Duck',
            'fn' => 'Dewey',
            'g' => 'm',
            'l' => 1,
            'units' => [0 => [
                'kind' => 'c',
                'union_id' => 1,
            ]],
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Profiles', 'action' => 'view', 3]);
        $this->assertFlashElement('flash/success');

        $this->assertEquals($profilesCount + 1, $this->fetchTable('Profiles')->find()->count());
        $this->assertEquals($unionsCount, $this->fetchTable('Unions')->find()->count());
        $this->assertEquals($unitsCount + 1, $this->fetchTable('Units')->find()->count());

        $profile = $this->fetchTable('Profiles')->get(3);
        $this->assertEquals('Dewey Duck', $profile->d_n);
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->session(['Auth' => $this->authData['User']]);

        $profilesCount = $this->fetchTable('Profiles')->find()->count();
        $unionsCount = $this->fetchTable('Unions')->find()->count();
        $unitsCount = $this->fetchTable('Units')->find()->count();

        $this->post('/profiles/edit/1', [
            'id' => 1,
            'fn' => 'Donald P.',
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Profiles', 'action' => 'view', 1]);
        $this->assertFlashElement('flash/success');

        $this->assertEquals($profilesCount, $this->fetchTable('Profiles')->find()->count());
        $this->assertEquals($unionsCount, $this->fetchTable('Unions')->find()->count());
        $this->assertEquals($unitsCount, $this->fetchTable('Units')->find()->count());

        $profile = $this->fetchTable('Profiles')->get(1);
        $this->assertEquals('Donald P.', $profile->fn);
        $this->assertEquals('Donald P. Duck', $profile->d_n);
    }

    /**
     * Test edit avatar method
     *
     * @return void
     */
    public function testEditAvatar()
    {
        $this->session(['Auth' => $this->authData['User']]);

        $countBefore = $this->fetchTable('Profiles')->find()->count();

        $this->get('/profiles/edit-avatar/2/d372525d-9fb6-4643-bd21-217cb96d7495');

        $this->assertRedirect();

        $countAfter = $this->fetchTable('Profiles')->find()->count();
        $this->assertEquals($countBefore, $countAfter);
        $profile = $this->fetchTable('Profiles')->get(1);
        $this->assertEquals('d372525d-9fb6-4643-bd21-217cb96d7495', $profile->ta);
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->session(['Auth' => $this->authData['User']]);

        $countBefore = $this->fetchTable('Profiles')->find()->count();

        $this->get('/profiles/delete/1');

        $this->assertRedirect();

        $countAfter = $this->fetchTable('Profiles')->find()->count();
        $this->assertEquals($countBefore - 1, $countAfter);
    }
}
