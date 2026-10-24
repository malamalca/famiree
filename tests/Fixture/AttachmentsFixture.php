<?php
namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AttachmentsFixture
 */
class AttachmentsFixture extends TestFixture
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
                'id' => 'd372525d-9fb6-4643-bd21-217cb96d7495',
                'user_id' => 1,
                'filename' => 'DDuck.png',
                'original' => 'DDuck.png',
                'ext' => 'png',
                'mimetype' => 'image/png',
                'filesize' => 20316,
                'height' => 128,
                'width' => 128,
                'title' => 'D. Duck Profile',
                'description' => null,
                'checksum' => null,
                'created' => '2019-01-08 18:34:10',
                'modified' => '2019-01-08 18:34:10',
            ],
            [
                'id' => 'd372525d-9fb6-4643-bd21-217cb96d7496',
                'user_id' => 1,
                'filename' => 'DFamily.jpg',
                'original' => 'DFamily.jpg',
                'ext' => 'jpg',
                'mimetype' => 'image/jpeg',
                'filesize' => 33648,
                'height' => 475,
                'width' => 419,
                'title' => 'D. Duck Family',
                'description' => null,
                'checksum' => '4a3992be185886220dc46207e3a948f9',
                'created' => '2019-01-08 18:34:10',
                'modified' => '2019-01-08 18:34:10',
            ],
        ];
        parent::init();
    }
}
