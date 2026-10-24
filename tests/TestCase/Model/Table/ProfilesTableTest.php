<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ProfilesTable;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Association\HasMany;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ProfilesTable Test Case
 */
class ProfilesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ProfilesTable
     */
    public $Profiles;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Profiles',
        'app.Imgnotes',
        'app.Units',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Profiles') ? [] : ['className' => ProfilesTable::class];
        $this->Profiles = $this->getTableLocator()->get('Profiles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Profiles);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertSame('profiles', $this->Profiles->getTable());
        $this->assertSame('d_n', $this->Profiles->getDisplayField());
        $this->assertSame('id', $this->Profiles->getPrimaryKey());
        $this->assertTrue($this->Profiles->hasBehavior('Timestamp'));
        $this->assertTrue($this->Profiles->hasBehavior('Loggable'));

        $this->assertInstanceOf(BelongsTo::class, $this->Profiles->getAssociation('Creators'));
        $this->assertSame('Profiles', $this->Profiles->getAssociation('Creators')->getClassName());
        $this->assertInstanceOf(BelongsToMany::class, $this->Profiles->getAssociation('Attachments'));
        $this->assertInstanceOf(BelongsToMany::class, $this->Profiles->getAssociation('Posts'));
        $this->assertInstanceOf(BelongsToMany::class, $this->Profiles->getAssociation('Marriages'));
        $this->assertSame('Unions', $this->Profiles->getAssociation('Marriages')->getClassName());
        $this->assertInstanceOf(HasMany::class, $this->Profiles->getAssociation('Units'));
        $this->assertTrue($this->Profiles->getAssociation('Units')->getDependent());
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->assertEmpty($this->Profiles->newEntity(['fn' => 'Donald', 'ln' => 'Duck'])->getErrors());

        $this->assertArrayHasKey('fn', $this->Profiles->newEntity(['fn' => ''])->getErrors());
        $this->assertArrayHasKey('id', $this->Profiles->newEntity(['id' => 'abc', 'fn' => 'Donald'])->getErrors());
    }

}
