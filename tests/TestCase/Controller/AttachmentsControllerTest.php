<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use FilesystemIterator;
use Laminas\Diactoros\UploadedFile;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use const UPLOAD_ERR_OK;

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
    public function setUp(): void
    {
        parent::setUp();

        $this->resourceFolder = dirname(__FILE__) . DS . '..' . DS . '..' . DS . 'Resource';

        $this->copyDirectory($this->resourceFolder . DS . 'uploads', TMP . 'uploads');

        Configure::write('sourceFolders.attachments', TMP . 'uploads' . DS);

        $this->copyDirectory($this->resourceFolder . DS . 'thumbs', TMP . 'thumbs');

        Configure::write('sourceFolders.thumbs', TMP . 'thumbs' . DS);

        Configure::write('uploadCheck', 'existing');

        copy($this->resourceFolder . DS . 'DDuckAngry.png', TMP . 'DDuckAngry.png');
    }

    /**
     * Teardown test
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $this->deleteDirectory(TMP . 'uploads');
        $this->deleteDirectory(TMP . 'thumbs');

        if (file_exists(TMP . 'DDuckAngry.png')) {
            unlink(TMP . 'DDuckAngry.png');
        }
    }

    /**
     * Recursively copies a directory
     *
     * @param string $source Source directory.
     * @param string $destination Destination directory.
     * @return void
     */
    private function copyDirectory(string $source, string $destination): void
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $item) {
            $target = $destination . DS . $iterator->getSubPathname();
            if ($item->isDir()) {
                if (!is_dir($target)) {
                    mkdir($target, 0777, true);
                }
            } else {
                copy($item->getPathname(), $target);
            }
        }
    }

    /**
     * Recursively deletes a directory
     *
     * @param string $path Directory to delete.
     * @return void
     */
    private function deleteDirectory(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($iterator as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }
        rmdir($path);
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->session(['Auth' => $this->authData['User']]);

        $this->get('/attachments');

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
        $this->session(['Auth' => $this->authData['User']]);

        $countBefore = $this->fetchTable('Attachments')->find()->count();

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->enableRetainFlashMessages();

        $attachment = new UploadedFile(
            TMP . 'DDuckAngry.png',
            115850,
            UPLOAD_ERR_OK,
            'DDuckAngry.png',
            'image/png'
        );

        $this->post('/attachments/edit', [
            'user_id' => 1,
            'attachment' => $attachment,
            'title' => 'D. Duck the Madman',
            'description' => null,
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Attachments', 'action' => 'index']);
        $this->assertFlashElement('flash/success');

        $countAfter = $this->fetchTable('Attachments')->find()->count();
        $this->assertEquals($countBefore + 1, $countAfter);

        $this->assertFalse(file_exists(TMP . 'DDuckAngry.png')); // file should move

        $attachment = $this->fetchTable('Attachments')->find()
            ->select()
            ->orderBy('created DESC')
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
        $this->session(['Auth' => $this->authData['User']]);

        // edit without file upload
        $countBefore = $this->fetchTable('Attachments')->find()->count();

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->enableRetainFlashMessages();

        $this->post('/attachments/edit/d372525d-9fb6-4643-bd21-217cb96d7495', [
            'id' => 'd372525d-9fb6-4643-bd21-217cb96d7495',
            'user_id' => 1,
            'filename' => [
                'tmp_name' => '',
                'error' => 4,
                'name' => '',
                'type' => '',
                'size' => 0,
            ],
            'title' => 'D. Duck Profile Edited',
            'description' => null,
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Attachments', 'action' => 'index']);
        $this->assertFlashElement('flash/success');

        $countAfter = $this->fetchTable('Attachments')->find()->count();
        $this->assertEquals($countBefore, $countAfter);

        $attachment = $this->fetchTable('Attachments')->get('d372525d-9fb6-4643-bd21-217cb96d7495');

        $this->assertEquals('D. Duck Profile Edited', $attachment->title);

        $this->assertTrue(file_exists(Configure::read('sourceFolders.thumbs') . $attachment->id . '.png'));
        $this->assertTrue(file_exists(Configure::read('sourceFolders.attachments') . $attachment->id . DS . 'original'));
        $this->assertTrue(file_exists(Configure::read('sourceFolders.attachments') . $attachment->id . DS . 'large'));
        $this->assertTrue(file_exists(Configure::read('sourceFolders.attachments') . $attachment->id . DS . 'medium'));

        ///////////////////////////////////////////////////////////////////////////////////////////
        // edit with file upload
        $countBefore = $this->fetchTable('Attachments')->find()->count();

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->enableRetainFlashMessages();

        $attachment = new UploadedFile(
            TMP . 'DDuckAngry.png',
            115850,
            UPLOAD_ERR_OK,
            'DDuckAngry.png',
            'image/png'
        );

        $this->post('attachments/edit/d372525d-9fb6-4643-bd21-217cb96d7495', [
            'id' => 'd372525d-9fb6-4643-bd21-217cb96d7495',
            'user_id' => 1,
            'attachment' => $attachment,
            'title' => 'D. Duck Profile Edited',
            'description' => null,
        ]);

        $this->assertResponseSuccess();
        $this->assertRedirect(['controller' => 'Attachments', 'action' => 'index']);
        $this->assertFlashElement('flash/success');

        $countAfter = $this->fetchTable('Attachments')->find()->count();
        $this->assertEquals($countBefore, $countAfter);

        $attachment = $this->fetchTable('Attachments')->get('d372525d-9fb6-4643-bd21-217cb96d7495');

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

        $this->session(['Auth' => $this->authData['User']]);

        $countBefore = $this->fetchTable('Attachments')->find()->count();

        $this->get('attachments/delete/d372525d-9fb6-4643-bd21-217cb96d7495');

        $this->assertRedirect();

        $countAfter = $this->fetchTable('Attachments')->find()->count();
        $this->assertEquals($countBefore - 1, $countAfter);

        $this->assertFalse(file_exists(Configure::read('sourceFolders.thumbs') . 'd372525d-9fb6-4643-bd21-217cb96d7495.png'));
        $this->assertFalse(file_exists(Configure::read('sourceFolders.attachments') . 'd372525d-9fb6-4643-bd21-217cb96d7495'));
    }

    /**
     * Test display method
     *
     * @return void
     */
    public function testDisplay()
    {
        $this->session(['Auth' => $this->authData['User']]);
        $this->get('/attachments/display/d372525d-9fb6-4643-bd21-217cb96d7495');
        $this->assertRedirect(['controller' => 'Attachments', 'action' => 'display', 'd372525d-9fb6-4643-bd21-217cb96d7495', 'original', 'D-Duck-Profile.png']);

        $this->get('/attachments/display/d372525d-9fb6-4643-bd21-217cb96d7495/original/D-Duck-Profile.png');
        $this->assertResponseOk();
        $this->assertNoRedirect();
    }
}
