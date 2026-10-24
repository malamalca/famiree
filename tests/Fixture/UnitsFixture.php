<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UnitsFixture
 */
class UnitsFixture extends TestFixture
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
                'union_id' => 1,
                'profile_id' => 1,
                'kind' => 'p',
                'sort_order' => 1,
                'created' => '2019-01-06 15:56:25',
                'modified' => '2019-01-06 15:56:25',
            ],
            [
                'id' => 2,
                'union_id' => 1,
                'profile_id' => 2,
                'kind' => 'c',
                'sort_order' => 1,
                'created' => '2019-01-06 15:56:25',
                'modified' => '2019-01-06 15:56:25',
            ],
        ];
        parent::init();
    }
}
