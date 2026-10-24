<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UnitsTable;
use Cake\ORM\Association\BelongsTo;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\UnitsTable Test Case
 */
class UnitsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\UnitsTable
     */
    public $Units;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Units',
        'app.Unions',
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
        $config = $this->getTableLocator()->exists('Units') ? [] : ['className' => UnitsTable::class];
        $this->Units = $this->getTableLocator()->get('Units', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Units);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertSame('units', $this->Units->getTable());
        $this->assertSame('id', $this->Units->getDisplayField());
        $this->assertSame('id', $this->Units->getPrimaryKey());
        $this->assertTrue($this->Units->hasBehavior('Timestamp'));

        $this->assertInstanceOf(BelongsTo::class, $this->Units->getAssociation('Unions'));
        $this->assertInstanceOf(BelongsTo::class, $this->Units->getAssociation('Profiles'));
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $valid = ['union_id' => 1, 'profile_id' => 1, 'kind' => 'p', 'sort_order' => 1];
        $this->assertEmpty($this->Units->newEntity($valid)->getErrors());

        $this->assertArrayHasKey('kind', $this->Units->newEntity(['kind' => 'pc'] + $valid)->getErrors());
        $this->assertArrayHasKey('sort_order', $this->Units->newEntity(['sort_order' => 'first'] + $valid)->getErrors());
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $valid = ['union_id' => 1, 'profile_id' => 1, 'kind' => 'p', 'sort_order' => 5];
        $this->assertNotFalse($this->Units->save($this->Units->newEntity($valid)));

        $unit = $this->Units->newEntity(['union_id' => 9999] + $valid);
        $this->assertFalse($this->Units->save($unit), 'Union has to exist');
        $this->assertArrayHasKey('union_id', $unit->getErrors());

        $unit = $this->Units->newEntity(['profile_id' => 9999] + $valid);
        $this->assertFalse($this->Units->save($unit), 'Profile has to exist');
        $this->assertArrayHasKey('profile_id', $unit->getErrors());
    }
}
