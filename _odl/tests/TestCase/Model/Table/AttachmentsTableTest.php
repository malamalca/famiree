<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\AttachmentsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\AttachmentsTable Test Case
 */
class AttachmentsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\AttachmentsTable
     */
    public $Attachments;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Attachments',
        'app.Profiles',
        'app.AttachmentsLinks',
        'app.Imgnotes'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Attachments') ? [] : ['className' => AttachmentsTable::class];
        $this->Attachments = TableRegistry::getTableLocator()->get('Attachments', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Attachments);

        parent::tearDown();
    }

    /**
     * Test fetchForProfile method
     *
     * @return void
     */
    public function testFetchForProfile()
    {
        $ret = $this->Attachments->fetchForProfile(1);
        $this->assertTrue((bool)$ret);
    }
}
