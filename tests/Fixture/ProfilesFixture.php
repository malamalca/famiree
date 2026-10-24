<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ProfilesFixture
 */
class ProfilesFixture extends TestFixture
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
                'ta' => 'd372525d-9fb6-4643-bd21-217cb96d7495',
                'd_n' => 'Donald Duck',
                'ln' => 'Duck',
                'mdn' => null,
                'fn' => 'Donald',
                'mn' => null,
                'g' => 'm',
                'l' => 1,
                'e' => 'donald.duck@test.com',
                'u' => 'donald.duck',
                'p' => null,
                'lvl' => 2,
                'dob_y' => 1934,
                'dob_m' => 6,
                'dob_d' => 9,
                'dod_y' => null,
                'dod_m' => null,
                'dod_d' => null,
                'dob_c' => null,
                'dod_c' => null,
                'h_c' => null,
                'e_c' => null,
                'n_n' => null,
                'loc' => null,
                'plob' => null,
                'plod' => null,
                'cod' => null,
                'plobu' => null,
                'job' => null,
                'edu' => null,
                'in_i' => null,
                'in_a' => null,
                'in_p' => null,
                'in_c' => null,
                'in_q' => null,
                'in_m' => null,
                'in_tv' => null,
                'in_mu' => null,
                'in_b' => null,
                'in_s' => null,
                'cn_med' => 1,
                'cn_mem' => 0,
                'last_login' => null,
                'created' => '2018-12-14 13:55:53',
                'creator_id' => 1,
                'modified' => '2018-12-14 13:55:53',
                'modifier_id' => 1,
            ],
            [
                'id' => 2,
                'ta' => null,
                'd_n' => 'Huey Duck',
                'lvl' => 3,
                'ln' => 'Duck',
                'fn' => 'Huey',
                'g' => 'm',
                'l' => 1,
            ],
        ];
        parent::init();
    }
}
