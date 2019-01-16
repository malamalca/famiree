<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ImgNotesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ImgNotesTable Test Case
 */
class ImgNotesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ImgNotesTable
     */
    public $ImgNotes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.ImgNotes',
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
        $config = TableRegistry::getTableLocator()->exists('ImgNotes') ? [] : ['className' => ImgNotesTable::class];
        $this->ImgNotes = TableRegistry::getTableLocator()->get('ImgNotes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ImgNotes);

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
