<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\UnionsTable;
use Cake\ORM\TableRegistry;
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
    public $fixtures = [
        'app.Unions',
        'app.Units'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Unions') ? [] : ['className' => UnionsTable::class];
        $this->Unions = TableRegistry::getTableLocator()->get('Unions', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
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
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
