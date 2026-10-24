<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PostsFixture
 */
class PostsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'blog_id' => 1,
                'status' => 1,
                'title' => 'My First Memory',
                'body' => 'My first memory started when I was 3 years old. It was about ...',
                'created' => '2019-01-08 14:32:00',
                'creator_id' => 1,
                'modified' => '2019-01-08 14:32:00',
                'modifier_id' => 1,
            ],
        ];
        parent::init();
    }
}
