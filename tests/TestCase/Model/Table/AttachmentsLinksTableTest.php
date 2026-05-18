<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AttachmentsLinksTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AttachmentsLinksTable Test Case
 */
class AttachmentsLinksTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\AttachmentsLinksTable
     */
    public $AttachmentsLinks;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.AttachmentsLinks',
        'app.Attachments',
        'app.Profiles',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('AttachmentsLinks') ? [] : ['className' => AttachmentsLinksTable::class];
        $this->AttachmentsLinks = TableRegistry::getTableLocator()->get('AttachmentsLinks', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->AttachmentsLinks);

        parent::tearDown();
    }

    /**
     * Test testSaveWithModel method
     *
     * @return void
     */
    public function testSaveWithModel()
    {
        $data = [
            'attachment_id' => 'd372525d-9fb6-4643-bd21-217cb96d7495',
            'class' => 'Profile',
            'foreign_id' => '',
        ];

        $entity = $this->AttachmentsLinks->newEntity($data);
        $result = $this->AttachmentsLinks->save($entity);

        $this->assertFalse($result);

        $data = [
            'attachment_id' => 'd372525d-9fb6-4643-bd21-217cb96d7495',
            'class' => 'Profile',
            'foreign_id' => 'invalidId',
        ];

        $entity = $this->AttachmentsLinks->newEntity($data);
        $result = $this->AttachmentsLinks->save($entity);

        $this->assertFalse($result);

        $data = [
            'attachment_id' => 'd372525d-9fb6-4643-bd21-217cb96d7495',
            'class' => 'Profile',
            'foreign_id' => '2',
        ];

        $entity = $this->AttachmentsLinks->newEntity($data);
        $result = $this->AttachmentsLinks->save($entity);

        $this->assertTrue((bool)$result);

        $data = [
            'attachment_id' => 'd372525d-9fb6-4643-bd21-217cb96d7495',
            'class' => 'Profile',
            'foreign_id' => 2,
        ];

        $entity = $this->AttachmentsLinks->newEntity($data);
        $result = $this->AttachmentsLinks->save($entity);

        $this->assertTrue((bool)$result);
    }
}
