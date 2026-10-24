<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PostsTable;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Association\HasMany;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PostsTable Test Case
 */
class PostsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PostsTable
     */
    public $Posts;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Posts',
        'app.Profiles',
        'app.PostsLinks',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Posts') ? [] : ['className' => PostsTable::class];
        $this->Posts = $this->getTableLocator()->get('Posts', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Posts);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertSame('posts', $this->Posts->getTable());
        $this->assertSame('title', $this->Posts->getDisplayField());
        $this->assertSame('id', $this->Posts->getPrimaryKey());
        $this->assertTrue($this->Posts->hasBehavior('Timestamp'));

        $this->assertInstanceOf(BelongsTo::class, $this->Posts->getAssociation('Creators'));
        $this->assertSame('Profiles', $this->Posts->getAssociation('Creators')->getClassName());
        $this->assertInstanceOf(HasMany::class, $this->Posts->getAssociation('PostsLinks'));
        $this->assertTrue($this->Posts->getAssociation('PostsLinks')->getDependent());
        $this->assertInstanceOf(BelongsToMany::class, $this->Posts->getAssociation('Profiles'));
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->assertEmpty($this->Posts->newEntity(['title' => 'My memory', 'body' => 'Once upon a time'])->getErrors());

        $this->assertArrayHasKey('title', $this->Posts->newEntity(['title' => str_repeat('x', 101)])->getErrors());
        $this->assertArrayHasKey('title', $this->Posts->newEntity(['title' => ['array']])->getErrors());
        $this->assertArrayHasKey('body', $this->Posts->newEntity(['body' => ['array']])->getErrors());
    }

}
