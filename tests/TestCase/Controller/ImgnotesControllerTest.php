<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\ImgnotesController Test Case
 */
class ImgnotesControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Imgnotes',
        'app.Attachments',
        'app.AttachmentsLinks',
        'app.Profiles',
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
     * Test add method
     *
     * @return void
     */
    public function testAdd(): void
    {
        $this->session(['Auth' => $this->authData['User']]);

        $countBefore = $this->fetchTable('Imgnotes')->find()->count();

        $this->post('/imgnotes/edit', [
            'user_id' => 1,
            'attachment_id' => 'd372525d-9fb6-4643-bd21-217cb96d7496',
            'profile_id' => 1,
            'x1' => 110,
            'y1' => 275,
            'width' => 85,
            'height' => 85,
            'note' => 'Huey Duck',
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Attachments', 'action' => 'view', 'd372525d-9fb6-4643-bd21-217cb96d7496']);
        $this->assertFlashElement('flash/success');

        $countAfter = $this->fetchTable('Imgnotes')->find()->count();
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

        $countBefore = $this->fetchTable('Imgnotes')->find()->count();

        $this->post('/imgnotes/edit/1', [
            'id' => 1,
            'user_id' => 1,
            'attachment_id' => 'd372525d-9fb6-4643-bd21-217cb96d7496',
            'profile_id' => 1,
            'x1' => 160,
            'y1' => 35,
            'width' => 100,
            'height' => 110,
            'note' => 'This is Donald Duck',
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Attachments', 'action' => 'view', 'd372525d-9fb6-4643-bd21-217cb96d7496']);
        $this->assertFlashElement('flash/success');

        $countAfter = $this->fetchTable('Imgnotes')->find()->count();
        $this->assertEquals($countBefore, $countAfter);

        $imgnote = $this->fetchTable('Imgnotes')->get(1);
        $this->assertEquals('This is Donald Duck', $imgnote->note);
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->session(['Auth' => $this->authData['User']]);

        $countBefore = $this->fetchTable('Imgnotes')->find()->count();

        $this->get('/imgnotes/delete/1');

        $this->assertRedirect();

        $countAfter = $this->fetchTable('Imgnotes')->find()->count();
        $this->assertEquals($countBefore - 1, $countAfter);
    }
}
