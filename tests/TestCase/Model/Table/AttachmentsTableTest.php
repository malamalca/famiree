<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AttachmentsTable;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\HasMany;
use Cake\TestSuite\TestCase;
use Laminas\Diactoros\UploadedFile;

/**
 * App\Model\Table\AttachmentsTable Test Case
 */
class AttachmentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\AttachmentsTable
     */
    public $Attachments;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Attachments',
        'app.Profiles',
        'app.AttachmentsLinks',
        'app.Imgnotes',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Attachments') ? [] : ['className' => AttachmentsTable::class];
        $this->Attachments = $this->getTableLocator()->get('Attachments', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Attachments);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertSame('attachments', $this->Attachments->getTable());
        $this->assertSame('title', $this->Attachments->getDisplayField());
        $this->assertSame('id', $this->Attachments->getPrimaryKey());
        $this->assertTrue($this->Attachments->hasBehavior('Timestamp'));

        $this->assertInstanceOf(BelongsTo::class, $this->Attachments->getAssociation('Creators'));
        $this->assertSame('Profiles', $this->Attachments->getAssociation('Creators')->getClassName());
        $this->assertInstanceOf(HasMany::class, $this->Attachments->getAssociation('AttachmentsLinks'));
        $this->assertTrue($this->Attachments->getAssociation('AttachmentsLinks')->getDependent());
        $this->assertInstanceOf(HasMany::class, $this->Attachments->getAssociation('Imgnotes'));
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $valid = ['ext' => 'png', 'title' => 'Duck', 'filesize' => 1024, 'width' => 10, 'height' => 20];
        $this->assertEmpty($this->Attachments->newEntity($valid)->getErrors());

        // extension is required when creating
        $this->assertArrayHasKey('ext', $this->Attachments->newEntity(['title' => 'Duck'])->getErrors());
        $this->assertArrayHasKey('ext', $this->Attachments->newEntity(['ext' => ''] + $valid)->getErrors());
        $this->assertArrayHasKey('ext', $this->Attachments->newEntity(['ext' => 'toolongext'] + $valid)->getErrors());

        $this->assertArrayHasKey('title', $this->Attachments->newEntity(['title' => str_repeat('x', 101)] + $valid)->getErrors());
        $this->assertArrayHasKey('filesize', $this->Attachments->newEntity(['filesize' => 'big'] + $valid)->getErrors());
        $this->assertArrayHasKey('checksum', $this->Attachments->newEntity(['checksum' => str_repeat('a', 33)] + $valid)->getErrors());
        $this->assertArrayHasKey('id', $this->Attachments->newEntity(['id' => 'not-a-uuid'] + $valid)->getErrors());
    }

    /**
     * Only images are accepted for upload
     *
     * @return void
     */
    public function testUploadValidation()
    {
        $png = TESTS . 'Resource' . DS . 'DDuckAngry.png';
        $upload = new UploadedFile($png, filesize($png), UPLOAD_ERR_OK, 'DDuckAngry.png', 'image/png');
        $this->assertEmpty($this->Attachments->newEntity(['ext' => 'png', 'attachment' => $upload])->getErrors());

        $text = TMP . 'not-an-image.txt';
        file_put_contents($text, 'just text');
        try {
            $upload = new UploadedFile($text, filesize($text), UPLOAD_ERR_OK, 'not-an-image.png', 'image/png');
            $errors = $this->Attachments->newEntity(['ext' => 'png', 'attachment' => $upload])->getErrors();
            $this->assertArrayHasKey('mime', $errors['attachment'] ?? [], 'File content decides, not the client mime type');
        } finally {
            unlink($text);
        }
    }
}
