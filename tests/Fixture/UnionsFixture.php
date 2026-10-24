<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UnionsFixture
 */
class UnionsFixture extends TestFixture
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
                'id' => 1, // Donald ducks family
                't' => 'L',
                'dom_d' => null,
                'dom_m' => null,
                'dom_y' => null,
                'loc' => 'Lorem ipsum dolor sit amet',
                'created' => '2019-01-08 17:18:23',
                'modified' => '2019-01-08 17:18:23',
            ],
        ];
        parent::init();
    }
}
