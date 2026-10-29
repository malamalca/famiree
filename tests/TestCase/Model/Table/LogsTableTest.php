<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\LogsTable;
use Cake\ORM\Association\BelongsTo;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\LogsTable Test Case
 */
class LogsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\LogsTable
     */
    public $Logs;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Logs',
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
        $config = $this->getTableLocator()->exists('Logs') ? [] : ['className' => LogsTable::class];
        $this->Logs = $this->getTableLocator()->get('Logs', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Logs);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertSame('logs', $this->Logs->getTable());
        $this->assertSame('title', $this->Logs->getDisplayField());
        $this->assertSame('id', $this->Logs->getPrimaryKey());
        $this->assertTrue($this->Logs->hasBehavior('Timestamp'));

        foreach (['Profiles', 'Posts', 'Attachments', 'Imgnotes', 'Users'] as $association) {
            $this->assertInstanceOf(BelongsTo::class, $this->Logs->getAssociation($association));
        }
        $this->assertSame('Profiles', $this->Logs->getAssociation('Users')->getClassName());

        // logs of different classes share the foreign key, the class decides what they point to
        $this->assertSame(['class' => 'Profile'], $this->Logs->getAssociation('Profiles')->getConditions());
        $this->assertSame(['class' => 'Post'], $this->Logs->getAssociation('Posts')->getConditions());
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $valid = ['title' => 'Donald Duck', 'class' => 'Profile', 'action' => 'edit', 'change' => 'a:0:{}'];
        $this->assertEmpty($this->Logs->newEntity($valid)->getErrors());

        foreach (['title', 'class', 'action'] as $field) {
            $errors = $this->Logs->newEntity([$field => str_repeat('x', 51)] + $valid)->getErrors();
            $this->assertArrayHasKey($field, $errors, "$field is limited to 50 characters");
        }
    }
}
