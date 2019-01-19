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
                'ta' => 'Lorem ipsum dolor sit amet',
                'd_n' => 'Lorem ipsum dolor sit amet',
                'ln' => 'Lorem ipsum dolor sit amet',
                'mdn' => 'Lorem ipsum dolor sit amet',
                'fn' => 'Lorem ipsum dolor sit amet',
                'mn' => 'Lorem ipsum dolor sit amet',
                'g' => 'L',
                'l' => 1,
                'e' => 'Lorem ipsum dolor sit amet',
                'u' => 'Lorem ipsum dolor sit amet',
                'p' => 'Lorem ipsum dolor sit amet',
                'lvl' => 1,
                'dob_y' => 'Lorem ip',
                'dob_m' => 1,
                'dob_d' => 1,
                'dod_y' => 'Lorem ip',
                'dod_m' => 1,
                'dod_d' => 1,
                'dob_c' => 1,
                'dod_c' => 1,
                'h_c' => 1,
                'e_c' => 1,
                'n_n' => 'Lorem ipsum dolor sit amet',
                'loc' => 'Lorem ipsum dolor sit amet',
                'plob' => 'Lorem ipsum dolor sit amet',
                'plod' => 'Lorem ipsum dolor sit amet',
                'cod' => 'Lorem ipsum dolor sit amet',
                'plobu' => 'Lorem ipsum dolor sit amet',
                'job' => 'Electrician',
                'edu' => 'University',
                'in_i' => 'Lorem ipsum dolor sit amet',
                'in_a' => 'Lorem ipsum dolor sit amet',
                'in_p' => 'Lorem ipsum dolor sit amet',
                'in_c' => 'Lorem ipsum dolor sit amet',
                'in_q' => 'Lorem ipsum dolor sit amet',
                'in_m' => 'Lorem ipsum dolor sit amet',
                'in_tv' => 'Lorem ipsum dolor sit amet',
                'in_mu' => 'Lorem ipsum dolor sit amet',
                'in_b' => 'Lorem ipsum dolor sit amet',
                'in_s' => 'Lorem ipsum dolor sit amet',
                'cn_med' => 1,
                'cn_mem' => 1,
                'last_login' => '2018-12-14 13:55:53',
                'created' => '2018-12-14 13:55:53',
                'creator_id' => 1,
                'modified' => '2018-12-14 13:55:53',
                'modifier_id' => 1
            ],
        ];
        parent::init();
    }
}
