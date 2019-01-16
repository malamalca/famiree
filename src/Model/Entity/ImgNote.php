<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ImgNote Entity
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $attachment_id
 * @property int|null $profile_id
 * @property int|null $x1
 * @property int|null $y1
 * @property int|null $width
 * @property int|null $height
 * @property string|null $note
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\Attachment $attachment
 * @property \App\Model\Entity\Profile $profile
 */
class ImgNote extends Entity
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
        'attachment_id' => true,
        'profile_id' => true,
        'x1' => true,
        'y1' => true,
        'width' => true,
        'height' => true,
        'note' => true,
        'created' => true,
        'modified' => true,

        'user' => true,
        'attachment' => true,
        'profile' => true
    ];

    /**
     * Returns string representation of entity
     *
     * @return string
     */
    public function toString()
    {
        return $this->note;
    }
}
