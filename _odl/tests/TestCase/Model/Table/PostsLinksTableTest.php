<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PostsLinksTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PostsLinksTable Test Case
 */
class PostsLinksTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\PostsLinksTable
     */
    public $PostsLinks;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.PostsLinks',
        'app.Posts',
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
        $config = TableRegistry::getTableLocator()->exists('PostsLinks') ? [] : ['className' => PostsLinksTable::class];
        $this->PostsLinks = TableRegistry::getTableLocator()->get('PostsLinks', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->PostsLinks);

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
