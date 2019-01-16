<?php
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
        $config = TableRegistry::getTableLocator()->exists('AttachmentsLinks') ? [] : ['className' => AttachmentsLinksTable::class];
        $this->AttachmentsLinks = TableRegistry::getTableLocator()->get('AttachmentsLinks', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->AttachmentsLinks);

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
