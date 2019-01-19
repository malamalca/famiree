<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ProfilesFixture
 *
 */
class ProfilesFixture extends TestFixture
{

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'ta' => ['type' => 'string', 'fixed' => true, 'length' => 100, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Attachment', 'precision' => null],
        'd_n' => ['type' => 'string', 'length' => 200, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Display name', 'precision' => null, 'fixed' => null],
        'ln' => ['type' => 'string', 'length' => 100, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Last name', 'precision' => null, 'fixed' => null],
        'mdn' => ['type' => 'string', 'length' => 100, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Maiden name', 'precision' => null, 'fixed' => null],
        'fn' => ['type' => 'string', 'length' => 100, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'First name', 'precision' => null, 'fixed' => null],
        'mn' => ['type' => 'string', 'length' => 100, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Middle name', 'precision' => null, 'fixed' => null],
        'g' => ['type' => 'string', 'fixed' => true, 'length' => 1, 'null' => false, 'default' => 'm', 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Gender', 'precision' => null],
        'l' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => '0', 'comment' => 'Living', 'precision' => null],
        'e' => ['type' => 'string', 'length' => 100, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Email', 'precision' => null, 'fixed' => null],
        'u' => ['type' => 'string', 'length' => 100, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Username', 'precision' => null, 'fixed' => null],
        'p' => ['type' => 'string', 'length' => 50, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Password', 'precision' => null, 'fixed' => null],
        'lvl' => ['type' => 'integer', 'length' => 4, 'unsigned' => false, 'null' => false, 'default' => '10', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'dob_y' => ['type' => 'string', 'length' => 10, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'dob_m' => ['type' => 'integer', 'length' => 4, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'dob_d' => ['type' => 'integer', 'length' => 4, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'dod_y' => ['type' => 'string', 'length' => 10, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'dod_m' => ['type' => 'integer', 'length' => 4, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'dod_d' => ['type' => 'integer', 'length' => 4, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'dob_c' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'dod_c' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'h_c' => ['type' => 'integer', 'length' => 4, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'Hair Color', 'precision' => null, 'autoIncrement' => null],
        'e_c' => ['type' => 'integer', 'length' => 4, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'Eye Color', 'precision' => null, 'autoIncrement' => null],
        'n_n' => ['type' => 'string', 'length' => 100, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Nick Names', 'precision' => null, 'fixed' => null],
        'loc' => ['type' => 'string', 'length' => 200, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null, 'fixed' => null],
        'plob' => ['type' => 'string', 'length' => 100, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Place of Birth', 'precision' => null, 'fixed' => null],
        'plod' => ['type' => 'string', 'length' => 100, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Place of Death', 'precision' => null, 'fixed' => null],
        'cod' => ['type' => 'string', 'length' => 100, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Cause of Death', 'precision' => null, 'fixed' => null],
        'plobu' => ['type' => 'string', 'length' => 100, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Place of Burrial', 'precision' => null, 'fixed' => null],
        'job' => ['type' => 'string', 'length' => 250, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Job description', 'precision' => null, 'fixed' => null],
        'edu' => ['type' => 'string', 'length' => 250, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Education', 'precision' => null, 'fixed' => null],
        'in_i' => ['type' => 'string', 'length' => 200, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Interests', 'precision' => null, 'fixed' => null],
        'in_a' => ['type' => 'string', 'length' => 200, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Activities', 'precision' => null, 'fixed' => null],
        'in_p' => ['type' => 'string', 'length' => 200, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'PeopleHeroes', 'precision' => null, 'fixed' => null],
        'in_c' => ['type' => 'string', 'length' => 200, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Cuisines', 'precision' => null, 'fixed' => null],
        'in_q' => ['type' => 'string', 'length' => 200, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Quotes', 'precision' => null, 'fixed' => null],
        'in_m' => ['type' => 'string', 'length' => 200, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Movies', 'precision' => null, 'fixed' => null],
        'in_tv' => ['type' => 'string', 'length' => 200, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'TV Shows', 'precision' => null, 'fixed' => null],
        'in_mu' => ['type' => 'string', 'length' => 200, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Music', 'precision' => null, 'fixed' => null],
        'in_b' => ['type' => 'string', 'length' => 200, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Books', 'precision' => null, 'fixed' => null],
        'in_s' => ['type' => 'string', 'length' => 200, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => 'Sports', 'precision' => null, 'fixed' => null],
        'cn_med' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'Media Count', 'precision' => null, 'autoIncrement' => null],
        'cn_mem' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => 'Memory Count', 'precision' => null, 'autoIncrement' => null],
        'last_login' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'creator_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'modifier_id' => ['type' => 'integer', 'length' => 11, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Init method
     *
     * @return void
     */
    public function init()
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
                'modifier_id' => 1
            ],
            [
                'id' => 2,
                'ta' => null,
                'd_n' => 'Huey Duck',
                'ln' => 'Duck',
                'fn' => 'Huey',
                'g' => 'm',
                'l' => 1,
            ],
        ];
        parent::init();
    }
}
