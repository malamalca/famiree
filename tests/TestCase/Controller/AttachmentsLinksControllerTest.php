<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\AttachmentsLinksController Test Case
 */
class AttachmentsLinksControllerTest extends TestCase
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

        $countBefore = $this->fetchTable('AttachmentsLinks')->find()->count();

        $this->post('/attachments-links/edit', [
            'attachment_id' => 'd372525d-9fb6-4643-bd21-217cb96d7496',
            'class' => 'Profile',
            'foreign_id' => '2',
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Attachments', 'action' => 'view', 'd372525d-9fb6-4643-bd21-217cb96d7496']);
        $this->assertFlashElement('flash/success');

        $countAfter = $this->fetchTable('AttachmentsLinks')->find()->count();
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

        $countBefore = $this->fetchTable('AttachmentsLinks')->find()->count();

        $this->post('/attachments-links/edit/2', [
            'attachment_id' => 'd372525d-9fb6-4643-bd21-217cb96d7496',
            'class' => 'Profile',
            'foreign_id' => '2',
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Attachments', 'action' => 'view', 'd372525d-9fb6-4643-bd21-217cb96d7496']);
        $this->assertFlashElement('flash/success');

        $countAfter = $this->fetchTable('AttachmentsLinks')->find()->count();
        $this->assertEquals($countBefore, $countAfter);
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->session(['Auth' => $this->authData['User']]);

        $countBefore = $this->fetchTable('AttachmentsLinks')->find()->count();

        $this->get('/attachments-links/delete/2');

        $this->assertRedirect();

        $countAfter = $this->fetchTable('AttachmentsLinks')->find()->count();
        $this->assertEquals($countBefore - 1, $countAfter);
    }
}
