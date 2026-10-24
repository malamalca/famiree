<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PostsLinksFixture
 */
class PostsLinksFixture extends TestFixture
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
                'post_id' => 1,
                'class' => 'Lorem ipsum dolor ',
                'foreign_id' => 1,
            ],
        ];
        parent::init();
    }
}
