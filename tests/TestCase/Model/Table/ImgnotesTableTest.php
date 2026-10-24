<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ImgnotesTable;
use Cake\ORM\Association\BelongsTo;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ImgnotesTable Test Case
 */
class ImgnotesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ImgnotesTable
     */
    public $Imgnotes;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Imgnotes',
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
        $config = $this->getTableLocator()->exists('Imgnotes') ? [] : ['className' => ImgnotesTable::class];
        $this->Imgnotes = $this->getTableLocator()->get('Imgnotes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Imgnotes);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertSame('imgnotes', $this->Imgnotes->getTable());
        $this->assertSame('id', $this->Imgnotes->getDisplayField());
        $this->assertSame('id', $this->Imgnotes->getPrimaryKey());
        $this->assertTrue($this->Imgnotes->hasBehavior('Timestamp'));

        $this->assertInstanceOf(BelongsTo::class, $this->Imgnotes->getAssociation('Creators'));
        $this->assertSame('Profiles', $this->Imgnotes->getAssociation('Creators')->getClassName());
        $this->assertInstanceOf(BelongsTo::class, $this->Imgnotes->getAssociation('Attachments'));
        $this->assertInstanceOf(BelongsTo::class, $this->Imgnotes->getAssociation('Profiles'));
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $valid = ['x1' => 10, 'y1' => 20, 'width' => 30, 'height' => 40, 'note' => 'Donald'];
        $this->assertEmpty($this->Imgnotes->newEntity($valid)->getErrors());

        foreach (['x1', 'y1', 'width', 'height'] as $field) {
            $errors = $this->Imgnotes->newEntity([$field => 'abc'] + $valid)->getErrors();
            $this->assertArrayHasKey($field, $errors, "$field has to be an integer");
        }

        $this->assertArrayHasKey('note', $this->Imgnotes->newEntity(['note' => str_repeat('x', 101)] + $valid)->getErrors());
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $data = [
            'user_id' => 1,
            'attachment_id' => 'd372525d-9fb6-4643-bd21-217cb96d7495',
            'profile_id' => 1,
            'x1' => 1,
            'y1' => 1,
            'width' => 10,
            'height' => 10,
        ];

        $imgnote = $this->Imgnotes->newEntity($data);
        $this->assertNotFalse($this->Imgnotes->save($imgnote));

        foreach (
            [
                'user_id' => 9999,
                'attachment_id' => 'aaaaaaaa-0000-4000-8000-000000000000',
                'profile_id' => 9999,
            ] as $field => $missing
        ) {
            $imgnote = $this->Imgnotes->newEntity([$field => $missing] + $data);
            $this->assertFalse($this->Imgnotes->save($imgnote), "$field has to exist");
            $this->assertArrayHasKey($field, $imgnote->getErrors());
        }
    }
}
