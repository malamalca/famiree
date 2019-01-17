<?php
namespace App;

use Migrations\AbstractMigration;

class Initial extends AbstractMigration
{

    public $autoId = false;

    /**
     * Up migration
     *
     * @return void
     */
    public function up()
    {
        $this->table('attachments')
            ->addColumn('id', 'uuid', [
                'default' => '',
                'limit' => null,
                'null' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('filename', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('original', 'string', [
                'default' => null,
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('ext', 'string', [
                'default' => 'gif',
                'limit' => 6,
                'null' => false,
            ])
            ->addColumn('mimetype', 'string', [
                'default' => null,
                'limit' => 30,
                'null' => true,
            ])
            ->addColumn('filesize', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('height', 'integer', [
                'default' => null,
                'limit' => 4,
                'null' => true,
            ])
            ->addColumn('width', 'integer', [
                'default' => null,
                'limit' => 4,
                'null' => true,
            ])
            ->addColumn('title', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('description', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('checksum', 'string', [
                'default' => null,
                'limit' => 32,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('attachments_links')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('attachment_id', 'uuid', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('class', 'string', [
                'default' => '',
                'limit' => 7,
                'null' => false,
            ])
            ->addColumn('foreign_id', 'string', [
                'default' => null,
                'limit' => 36,
                'null' => false,
            ])
            ->create();

        $this->table('comments')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('post_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
                'signed' => false,
            ])
            ->addColumn('body', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('status', 'integer', [
                'default' => '1',
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'post_id',
                ]
            )
            ->create();

        $this->table('event_dates')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('class', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => false,
            ])
            ->addColumn('foreign_id', 'integer', [
                'default' => '0',
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('kind', 'string', [
                'default' => '',
                'limit' => 8,
                'null' => false,
            ])
            ->addColumn('date_start', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('date_end', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => '0000-00-00 00:00:00',
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => '0000-00-00 00:00:00',
                'limit' => null,
                'null' => false,
            ])
            ->create();

        $this->table('imgnotes')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('attachment_id', 'uuid', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('profile_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('x1', 'integer', [
                'default' => null,
                'limit' => 4,
                'null' => true,
            ])
            ->addColumn('y1', 'integer', [
                'default' => null,
                'limit' => 4,
                'null' => true,
            ])
            ->addColumn('width', 'integer', [
                'default' => null,
                'limit' => 4,
                'null' => true,
            ])
            ->addColumn('height', 'integer', [
                'default' => null,
                'limit' => 4,
                'null' => true,
            ])
            ->addColumn('note', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('logs')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('title', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('class', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('foreign_id', 'uuid', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('action', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('user_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('change', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('posts')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('blog_id', 'integer', [
                'default' => '1',
                'limit' => 11,
                'null' => false,
            ])
            ->addColumn('status', 'tinyinteger', [
                'default' => '2',
                'limit' => 2,
                'null' => false,
            ])
            ->addColumn('title', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('slug', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('body', 'text', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('no_comments', 'integer', [
                'default' => '0',
                'limit' => 4,
                'null' => false,
            ])
            ->addColumn('allow_comments', 'boolean', [
                'default' => true,
                'limit' => null,
                'null' => false,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('creator_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modifier_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->create();

        $this->table('posts_links')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('post_id', 'integer', [
                'default' => '0',
                'limit' => 10,
                'null' => false,
            ])
            ->addColumn('class', 'string', [
                'default' => null,
                'limit' => 20,
                'null' => false,
            ])
            ->addColumn('foreign_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->create();

        $this->table('profiles')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('ta', 'string', [
                'comment' => 'Attachment',
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('d_n', 'string', [
                'comment' => 'Display name',
                'default' => null,
                'limit' => 200,
                'null' => true,
            ])
            ->addColumn('ln', 'string', [
                'comment' => 'Last name',
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('mdn', 'string', [
                'comment' => 'Maiden name',
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('fn', 'string', [
                'comment' => 'First name',
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('mn', 'string', [
                'comment' => 'Middle name',
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('g', 'string', [
                'comment' => 'Gender',
                'default' => 'm',
                'limit' => 1,
                'null' => false,
            ])
            ->addColumn('l', 'boolean', [
                'comment' => 'Living',
                'default' => false,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('e', 'string', [
                'comment' => 'Email',
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('u', 'string', [
                'comment' => 'Username',
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('p', 'string', [
                'comment' => 'Password',
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('lvl', 'integer', [
                'default' => '10',
                'limit' => 4,
                'null' => false,
            ])
            ->addColumn('dob_y', 'string', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('dob_m', 'integer', [
                'default' => null,
                'limit' => 4,
                'null' => true,
            ])
            ->addColumn('dob_d', 'integer', [
                'default' => null,
                'limit' => 4,
                'null' => true,
            ])
            ->addColumn('dod_y', 'string', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('dod_m', 'integer', [
                'default' => null,
                'limit' => 4,
                'null' => true,
            ])
            ->addColumn('dod_d', 'integer', [
                'default' => null,
                'limit' => 4,
                'null' => true,
            ])
            ->addColumn('dob_c', 'boolean', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('dod_c', 'boolean', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('h_c', 'integer', [
                'comment' => 'Hair Color',
                'default' => null,
                'limit' => 4,
                'null' => true,
            ])
            ->addColumn('e_c', 'integer', [
                'comment' => 'Eye Color',
                'default' => null,
                'limit' => 4,
                'null' => true,
            ])
            ->addColumn('n_n', 'string', [
                'comment' => 'Nick Names',
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('loc', 'string', [
                'default' => null,
                'limit' => 200,
                'null' => true,
            ])
            ->addColumn('plob', 'string', [
                'comment' => 'Place of Birth',
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('plod', 'string', [
                'comment' => 'Place of Death',
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('cod', 'string', [
                'comment' => 'Cause of Death',
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('plobu', 'string', [
                'comment' => 'Place of Burrial',
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('in_i', 'string', [
                'comment' => 'Interests',
                'default' => null,
                'limit' => 200,
                'null' => true,
            ])
            ->addColumn('in_a', 'string', [
                'comment' => 'Activities',
                'default' => null,
                'limit' => 200,
                'null' => true,
            ])
            ->addColumn('in_p', 'string', [
                'comment' => 'PeopleHeroes',
                'default' => null,
                'limit' => 200,
                'null' => true,
            ])
            ->addColumn('in_c', 'string', [
                'comment' => 'Cuisines',
                'default' => null,
                'limit' => 200,
                'null' => true,
            ])
            ->addColumn('in_q', 'string', [
                'comment' => 'Quotes',
                'default' => null,
                'limit' => 200,
                'null' => true,
            ])
            ->addColumn('in_m', 'string', [
                'comment' => 'Movies',
                'default' => null,
                'limit' => 200,
                'null' => true,
            ])
            ->addColumn('in_tv', 'string', [
                'comment' => 'TV Shows',
                'default' => null,
                'limit' => 200,
                'null' => true,
            ])
            ->addColumn('in_mu', 'string', [
                'comment' => 'Music',
                'default' => null,
                'limit' => 200,
                'null' => true,
            ])
            ->addColumn('in_b', 'string', [
                'comment' => 'Books',
                'default' => null,
                'limit' => 200,
                'null' => true,
            ])
            ->addColumn('in_s', 'string', [
                'comment' => 'Sports',
                'default' => null,
                'limit' => 200,
                'null' => true,
            ])
            ->addColumn('cn_med', 'integer', [
                'comment' => 'Media Count',
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('cn_mem', 'integer', [
                'comment' => 'Memory Count',
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('last_login', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('creator_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modifier_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->create();

        $this->table('settings')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('profile_id', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('date_order', 'string', [
                'default' => null,
                'limit' => 3,
                'null' => true,
            ])
            ->addColumn('date_separator', 'string', [
                'default' => null,
                'limit' => 1,
                'null' => true,
            ])
            ->addColumn('date_24hr', 'boolean', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('datef_common', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('datef_noyear', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('datef_short', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('locale', 'string', [
                'default' => null,
                'limit' => 50,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('unions')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('t', 'string', [
                'default' => null,
                'limit' => 1,
                'null' => true,
            ])
            ->addColumn('dom_d', 'integer', [
                'default' => null,
                'limit' => 4,
                'null' => true,
            ])
            ->addColumn('dom_m', 'integer', [
                'default' => null,
                'limit' => 4,
                'null' => true,
            ])
            ->addColumn('dom_y', 'string', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('loc', 'string', [
                'default' => null,
                'limit' => 100,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->create();

        $this->table('units')
            ->addColumn('id', 'integer', [
                'autoIncrement' => true,
                'default' => null,
                'limit' => 10,
                'null' => false,
            ])
            ->addPrimaryKey(['id'])
            ->addColumn('union_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('profile_id', 'integer', [
                'default' => null,
                'limit' => 10,
                'null' => true,
            ])
            ->addColumn('kind', 'string', [
                'default' => null,
                'limit' => 1,
                'null' => true,
            ])
            ->addColumn('sort_order', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => true,
            ])
            ->addColumn('created', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addColumn('modified', 'datetime', [
                'default' => null,
                'limit' => null,
                'null' => true,
            ])
            ->addIndex(
                [
                    'union_id',
                ]
            )
            ->addIndex(
                [
                    'profile_id',
                    'kind',
                ]
            )
            ->create();
    }

    /**
     * Down migration
     *
     * @return void
     */
    public function down()
    {
        $this->table('attachments')->drop()->save();
        $this->table('attachments_links')->drop()->save();
        $this->table('comments')->drop()->save();
        $this->table('event_dates')->drop()->save();
        $this->table('imgnotes')->drop()->save();
        $this->table('logs')->drop()->save();
        $this->table('posts')->drop()->save();
        $this->table('posts_links')->drop()->save();
        $this->table('profiles')->drop()->save();
        $this->table('settings')->drop()->save();
        $this->table('unions')->drop()->save();
        $this->table('units')->drop()->save();
    }
}
