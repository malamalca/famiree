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
     * Test linkProfile method
     *
     * @return void
     */
    public function testLinkProfile()
    {
        $ret = $this->AttachmentsLinks->linkProfile(2, 'd372525d-9fb6-4643-bd21-217cb96d7496');
        $this->assertTrue($ret);

        // link profile with attachment that does not exist
        $ret = $this->AttachmentsLinks->linkProfile(2, 'd372525d-9fb6-4334-bd21-217cb96d7496');
        $this->assertFalse($ret);
    }
}
