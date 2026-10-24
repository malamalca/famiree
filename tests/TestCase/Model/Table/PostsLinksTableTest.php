<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PostsLinksTable;
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
    protected array $fixtures = [
        'app.PostsLinks',
        'app.Posts',
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
        $config = $this->getTableLocator()->exists('PostsLinks') ? [] : ['className' => PostsLinksTable::class];
        $this->PostsLinks = $this->getTableLocator()->get('PostsLinks', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
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
        $this->assertSame('posts_links', $this->PostsLinks->getTable());
        $this->assertSame('id', $this->PostsLinks->getDisplayField());
        $this->assertSame('id', $this->PostsLinks->getPrimaryKey());
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $valid = ['post_id' => 1, 'foreign_id' => 1, 'class' => 'Profile'];
        $this->assertEmpty($this->PostsLinks->newEntity($valid)->getErrors());

        $this->assertArrayHasKey('class', $this->PostsLinks->newEntity(['post_id' => 1, 'foreign_id' => 1])->getErrors());
        $this->assertArrayHasKey('class', $this->PostsLinks->newEntity(['class' => str_repeat('x', 21)] + $valid)->getErrors());
    }

}
