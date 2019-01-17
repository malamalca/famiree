<?php
namespace App\Model\Entity;

use Cake\Core\Configure;
use Cake\ORM\Entity;

/**
 * Attachment Entity
 *
 * @property string $id
 * @property int $user_id
 * @property string|null $filename
 * @property string|null $original
 * @property string $ext
 * @property string|null $mimetype
 * @property int|null $filesize
 * @property int|null $height
 * @property int|null $width
 * @property string|null $title
 * @property string|null $description
 * @property string|null $checksum
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\AttachmentsLink[] $attachments_links
 * @property \App\Model\Entity\Imgnote[] $imgnotes
 */
class Attachment extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'user_id' => true,
        'filename' => true,
        'original' => true,
        'ext' => true,
        'mimetype' => true,
        'filesize' => true,
        'height' => true,
        'width' => true,
        'title' => true,
        'description' => true,
        'checksum' => true,
        'created' => true,
        'modified' => true,

        'user' => true,
        'attachments_links' => true,
        'imgnotes' => true
    ];

    /**
     * Returns physical dimension of image file
     *
     * @param mixed $size Image size
     * @access public
     * @return bool|array
     */
    public function getImageSize($size = 'original')
    {
        $ret = false;
        $folder = Configure::read('sourceFolders.attachments');
        if ($size == 'thumbs') {
            $folder = Configure::read('sourceFolders.thumbs');
        }

        $filename = $folder . DS . $this->id . DS . $size;
        if (file_exists($filename)) {
            if ($sizes = getimagesize($filename)) {
                $ret = [];
                $ret['width'] = $sizes[0];
                $ret['height'] = $sizes[1];
            }
        }

        return $ret;
    }
}
