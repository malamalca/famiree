<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ImgnotesTable;
use Cake\ORM\TableRegistry;
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
    public $fixtures = [
        'app.Imgnotes',
        'app.Attachments',
        'app.Profiles'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Imgnotes') ? [] : ['className' => ImgnotesTable::class];
        $this->ImgNotes = TableRegistry::getTableLocator()->get('Imgnotes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
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

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
