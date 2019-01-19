<?php
namespace App\Test\TestCase\Controller;

use App\Controller\AttachmentsLinksController;
use Cake\ORM\TableRegistry;
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
    public $fixtures = [
        'app.Imgnotes',
        'app.Attachments',
        'app.AttachmentsLinks',
        'app.Profiles',
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
     * Setup test
     *
     * @return void
     */
    public function setUp()
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
    public function testAdd()
    {
        $this->session(['Auth' => $this->authData]);

        $countBefore = TableRegistry::get('AttachmentsLinks')->find()->count();

        $this->post('attachments-links/add', [
            'attachment_id' => 'd372525d-9fb6-4643-bd21-217cb96d7496',
            'class' => 'Profile',
            'foreign_id' => '2'
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Attachments', 'action' => 'view', 'd372525d-9fb6-4643-bd21-217cb96d7496']);
        $this->assertFlashElement('Flash/success');

        $countAfter = TableRegistry::get('AttachmentsLinks')->find()->count();
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

        $countBefore = TableRegistry::get('AttachmentsLinks')->find()->count();

        $this->post('attachments-links/edit/2', [
            'attachment_id' => 'd372525d-9fb6-4643-bd21-217cb96d7496',
            'class' => 'Profile',
            'foreign_id' => '2'
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Attachments', 'action' => 'view', 'd372525d-9fb6-4643-bd21-217cb96d7496']);
        $this->assertFlashElement('Flash/success');

        $countAfter = TableRegistry::get('AttachmentsLinks')->find()->count();
        $this->assertEquals($countBefore, $countAfter);
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->session(['Auth' => $this->authData]);

        $countBefore = TableRegistry::get('AttachmentsLinks')->find()->count();

        $this->get('attachments-links/delete/2');

        $this->assertRedirect();

        $countAfter = TableRegistry::get('AttachmentsLinks')->find()->count();
        $this->assertEquals($countBefore - 1, $countAfter);
    }
}
