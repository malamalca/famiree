<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * AttachmentsLink Entity
 *
 * @property int $id
 * @property string|null $attachment_id
 * @property string $class
 * @property string $foreign_id
 *
 * @property \App\Model\Entity\Attachment $attachment
 * @property \App\Model\Entity\Foreign $foreign
 */
class AttachmentsLink extends Entity
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
        'attachment_id' => true,
        'class' => true,
        'foreign_id' => true,
        'attachment' => true,
        'foreign' => true
    ];
}
