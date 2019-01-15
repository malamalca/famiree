<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Union Entity
 *
 * @property int $id
 * @property string|null $t
 * @property int|null $dom_d
 * @property int|null $dom_m
 * @property string|null $dom_y
 * @property string|null $loc
 * @property \Cake\I18n\FrozenTime|null $created
 * @property \Cake\I18n\FrozenTime|null $modified
 *
 * @property \App\Model\Entity\Unit[] $units
 */
class Union extends Entity
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
        't' => true,
        'dom_d' => true,
        'dom_m' => true,
        'dom_y' => true,
        'loc' => true,
        'created' => true,
        'modified' => true,
        'units' => true
    ];
}
