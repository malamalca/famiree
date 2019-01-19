<?php
namespace App\Test\TestCase\Controller;

use App\Controller\AttachmentsController;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\AttachmentsController Test Case
 */
class AttachmentsControllerTest extends TestCase
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
     * Resource directory
     *
     * @var string
     */
    private $resourceFolder = null;

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

        $this->resourceFolder = dirname(__FILE__) . DS . '..' . DS . '..' . DS . 'Resource';

        $uploadedAttachments = new Folder($this->resourceFolder . DS . 'uploads');
        $uploadedAttachments->copy(TMP . 'uploads');

        Configure::write('sourceFolders.attachments', TMP . 'uploads' . DS);

        $uploadedThumbs = new Folder($this->resourceFolder . DS . 'thumbs');
        $uploadedThumbs->copy(TMP . 'thumbs');

        Configure::write('sourceFolders.thumbs', TMP . 'thumbs' . DS);

        Configure::write('uploadCheck', 'existing');

        copy($this->resourceFolder . DS . 'DDuckAngry.png', TMP . 'DDuckAngry.png');
    }

    /**
     * Teardown test
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        $uploadedAttachments = new Folder(TMP . 'uploads');
        $uploadedAttachments->delete();

        $uploadedThumbs = new Folder(TMP . 'thumbs');
        $uploadedThumbs->delete();

        if (file_exists(TMP . 'DDuckAngry.png')) {
            unlink(TMP . 'DDuckAngry.png');
        }
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->session(['Auth' => $this->authData]);
        $this->get('/attachments');

        $this->assertResponseOk();
        $this->assertNoRedirect();
    }

    /**
     * Test display method
     *
     * @return void
     */
    public function testDisplay()
    {
        $this->session(['Auth' => $this->authData]);
        $this->get('/attachments/display/d372525d-9fb6-4643-bd21-217cb96d7495');
        $this->assertRedirect(['controller' => 'Attachments', 'action' => 'display', 'd372525d-9fb6-4643-bd21-217cb96d7495', 'original', 'd-duck-profile.png']);

        $this->get('/attachments/display/d372525d-9fb6-4643-bd21-217cb96d7495/original/d-duck-profile.png');
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
        $this->get('/attachments/view/d372525d-9fb6-4643-bd21-217cb96d7495');

        $this->assertResponseOk();
        $this->assertNoRedirect();
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->session(['Auth' => $this->authData]);

        $countBefore = TableRegistry::get('Attachments')->find()->count();

        $this->post('attachments/add', [
            'user_id' => 1,
            'filename' => [
                'tmp_name' => TMP . DS . 'DDuckAngry.png',
                'name' => 'DDuckAngry.png',
                'type' => 'image/png',
                'size' => 115850,
                'error' => 0
            ],
            'title' => 'D. Duck the Madman',
            'description' => null
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Attachments', 'action' => 'index']);
        $this->assertFlashElement('Flash/success');

        $countAfter = TableRegistry::get('Attachments')->find()->count();
        $this->assertEquals($countBefore + 1, $countAfter);

        $this->assertFalse(file_exists(TMP . DS . 'DDuckAngry.png')); // file should move

        $attachment = TableRegistry::get('Attachments')->find()
            ->select()
            ->order('created DESC')
            ->first();

        $this->assertEquals('D. Duck the Madman', $attachment->title);

        $this->assertTrue(file_exists(Configure::read('sourceFolders.thumbs') . $attachment->id . '.png'));
        $this->assertTrue(file_exists(Configure::read('sourceFolders.attachments') . $attachment->id . DS . 'original'));
        $this->assertTrue(file_exists(Configure::read('sourceFolders.attachments') . $attachment->id . DS . 'large'));
        $this->assertTrue(file_exists(Configure::read('sourceFolders.attachments') . $attachment->id . DS . 'medium'));
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->session(['Auth' => $this->authData]);

        // edit without file upload
        $countBefore = TableRegistry::get('Attachments')->find()->count();

        $this->post('attachments/edit/d372525d-9fb6-4643-bd21-217cb96d7495', [
            'id' => 'd372525d-9fb6-4643-bd21-217cb96d7495',
            'user_id' => 1,
            'filename' => [
                'tmp_name' => '',
                'error' => 4,
                'name' => '',
                'type' => '',
                'size' => 0
            ],
            'title' => 'D. Duck Profile Edited',
            'description' => null,
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Attachments', 'action' => 'index']);
        $this->assertFlashElement('Flash/success');

        $countAfter = TableRegistry::get('Attachments')->find()->count();
        $this->assertEquals($countBefore, $countAfter);

        $attachment = TableRegistry::get('Attachments')->get('d372525d-9fb6-4643-bd21-217cb96d7495');

        $this->assertEquals('D. Duck Profile Edited', $attachment->title);

        $this->assertTrue(file_exists(Configure::read('sourceFolders.thumbs') . $attachment->id . '.png'));
        $this->assertTrue(file_exists(Configure::read('sourceFolders.attachments') . $attachment->id . DS . 'original'));
        $this->assertTrue(file_exists(Configure::read('sourceFolders.attachments') . $attachment->id . DS . 'large'));
        $this->assertTrue(file_exists(Configure::read('sourceFolders.attachments') . $attachment->id . DS . 'medium'));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // edit with file upload
        $countBefore = TableRegistry::get('Attachments')->find()->count();

        $this->post('attachments/edit/d372525d-9fb6-4643-bd21-217cb96d7495', [
            'id' => 'd372525d-9fb6-4643-bd21-217cb96d7495',
            'user_id' => 1,
            'filename' => [
                'tmp_name' => TMP . DS . 'DDuckAngry.png',
                'name' => 'DDuckAngry.png',
                'type' => 'image/png',
                'size' => 115850,
                'error' => 0
            ],
            'title' => 'D. Duck Profile Edited',
            'description' => null,
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Attachments', 'action' => 'index']);
        $this->assertFlashElement('Flash/success');

        $countAfter = TableRegistry::get('Attachments')->find()->count();
        $this->assertEquals($countBefore, $countAfter);

        $attachment = TableRegistry::get('Attachments')->get('d372525d-9fb6-4643-bd21-217cb96d7495');

        $this->assertEquals('D. Duck Profile Edited', $attachment->title);
        $this->assertEquals('DDuckAngry.png', $attachment->original);
        $this->assertEquals(128, $attachment->width);

        $this->assertTrue(file_exists(Configure::read('sourceFolders.thumbs') . $attachment->id . '.png'));
        $this->assertEquals(
            filesize(Configure::read('sourceFolders.attachments') . $attachment->id . DS . 'original'),
            filesize($this->resourceFolder . DS . 'DDuckAngry.png')
        );

        $this->assertTrue(file_exists(Configure::read('sourceFolders.attachments') . $attachment->id . DS . 'original'));
        $this->assertTrue(file_exists(Configure::read('sourceFolders.attachments') . $attachment->id . DS . 'large'));
        $this->assertTrue(file_exists(Configure::read('sourceFolders.attachments') . $attachment->id . DS . 'medium'));
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->assertTrue(file_exists(Configure::read('sourceFolders.thumbs') . 'd372525d-9fb6-4643-bd21-217cb96d7495.png'));
        $this->assertTrue(file_exists(Configure::read('sourceFolders.attachments') . 'd372525d-9fb6-4643-bd21-217cb96d7495'));

        $this->session(['Auth' => $this->authData]);

        $countBefore = TableRegistry::get('Attachments')->find()->count();

        $this->get('attachments/delete/d372525d-9fb6-4643-bd21-217cb96d7495');

        $this->assertRedirect();

        $countAfter = TableRegistry::get('Attachments')->find()->count();
        $this->assertEquals($countBefore - 1, $countAfter);

        $this->assertFalse(file_exists(Configure::read('sourceFolders.thumbs') . 'd372525d-9fb6-4643-bd21-217cb96d7495.png'));
        $this->assertFalse(file_exists(Configure::read('sourceFolders.attachments') . 'd372525d-9fb6-4643-bd21-217cb96d7495'));
    }
}
