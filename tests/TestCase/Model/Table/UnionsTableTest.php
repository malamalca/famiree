<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UnionsTable;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Association\HasMany;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\UnionsTable Test Case
 */
class UnionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\UnionsTable
     */
    public $Unions;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Unions',
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
        $config = $this->getTableLocator()->exists('Unions') ? [] : ['className' => UnionsTable::class];
        $this->Unions = $this->getTableLocator()->get('Unions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Unions);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertSame('unions', $this->Unions->getTable());
        $this->assertSame('id', $this->Unions->getDisplayField());
        $this->assertSame('id', $this->Unions->getPrimaryKey());
        $this->assertTrue($this->Unions->hasBehavior('Timestamp'));

        $this->assertInstanceOf(HasMany::class, $this->Unions->getAssociation('Units'));
        $this->assertInstanceOf(BelongsToMany::class, $this->Unions->getAssociation('Profiles'));
        $this->assertSame('units', $this->Unions->getAssociation('Profiles')->junction()->getTable());
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $valid = ['t' => 'L', 'dom_d' => 1, 'dom_m' => 2, 'dom_y' => '1950', 'loc' => 'Duckburg'];
        $this->assertEmpty($this->Unions->newEntity($valid)->getErrors());

        $this->assertArrayHasKey('t', $this->Unions->newEntity(['t' => 'LL'] + $valid)->getErrors());
        $this->assertArrayHasKey('dom_d', $this->Unions->newEntity(['dom_d' => 'first'] + $valid)->getErrors());
        $this->assertArrayHasKey('dom_m', $this->Unions->newEntity(['dom_m' => 'feb'] + $valid)->getErrors());
        $this->assertArrayHasKey('dom_y', $this->Unions->newEntity(['dom_y' => str_repeat('1', 11)] + $valid)->getErrors());
        $this->assertArrayHasKey('loc', $this->Unions->newEntity(['loc' => str_repeat('x', 101)] + $valid)->getErrors());
    }
}
