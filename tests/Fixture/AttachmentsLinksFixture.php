<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AttachmentsLinksFixture
 */
class AttachmentsLinksFixture extends TestFixture
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
                'attachment_id' => 'd372525d-9fb6-4643-bd21-217cb96d7495',
                'class' => 'Profile',
                'foreign_id' => '1',
            ],
            [
                'id' => 2,
                'attachment_id' => 'd372525d-9fb6-4643-bd21-217cb96d7496',
                'class' => 'Profile',
                'foreign_id' => '1',
            ],
        ];
        parent::init();
    }
}
