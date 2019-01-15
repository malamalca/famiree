<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Log Entity
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $class
 * @property string|null $foreign_id
 * @property string|null $action
 * @property int|null $user_id
 * @property string|null $change
 * @property \Cake\I18n\FrozenTime|null $created
 *
 * @property \App\Model\Entity\Foreign $foreign
 * @property \App\Model\Entity\User $user
 */
class Log extends Entity
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
        'title' => true,
        'class' => true,
        'foreign_id' => true,
        'action' => true,
        'user_id' => true,
        'change' => true,
        'created' => true,
        'foreign' => true,
        'user' => true
    ];
}
