<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Unit Entity
 *
 * @property int $id
 * @property int|null $union_id
 * @property int|null $profile_id
 * @property string|null $kind
 * @property int|null $sort_order
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Union $union
 * @property \App\Model\Entity\Profile $profile
 */
class Unit extends Entity
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
        'union_id' => true,
        'profile_id' => true,
        'kind' => true,
        'sort_order' => true,
        'created' => true,
        'modified' => true,
        'union' => true,
        'profile' => true
    ];
}
