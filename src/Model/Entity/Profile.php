<?php
namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\I18n\FrozenDate;
use Cake\ORM\Entity;

/**
 * Profile Entity
 *
 * @property int $id
 * @property string|null $ta
 * @property string|null $d_n
 * @property string|null $ln
 * @property string|null $mdn
 * @property string|null $fn
 * @property string|null $mn
 * @property string $g
 * @property bool|null $l
 * @property string|null $e
 * @property string|null $rst
 * @property string|null $u
 * @property string|null $p
 * @property int $lvl
 * @property string|null $dob_y
 * @property int|null $dob_m
 * @property int|null $dob_d
 * @property string|null $dod_y
 * @property int|null $dod_m
 * @property int|null $dod_d
 * @property bool|null $dob_c
 * @property bool|null $dod_c
 * @property int|null $h_c
 * @property int|null $e_c
 * @property string|null $n_n
 * @property string|null $loc
 * @property string|null $plob
 * @property string|null $plod
 * @property string|null $cod
 * @property string|null $plobu
 * @property string|null $in_i
 * @property string|null $in_a
 * @property string|null $in_p
 * @property string|null $in_c
 * @property string|null $in_q
 * @property string|null $in_m
 * @property string|null $in_tv
 * @property string|null $in_mu
 * @property string|null $in_b
 * @property string|null $in_s
 * @property int|null $cn_med
 * @property int|null $cn_mem
 * @property \Cake\I18n\FrozenTime|null $last_login
 * @property \Cake\I18n\FrozenTime|null $created
 * @property int|null $creator_id
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property int|null $modifier_id
 *
 * @property \App\Model\Entity\Creator $creator
 * @property \App\Model\Entity\Modifier $modifier
 * @property \App\Model\Entity\Imgnote[] $imgnotes
 * @property \App\Model\Entity\Setting[] $settings
 * @property \App\Model\Entity\Unit[] $units
 */
class Profile extends Entity
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
        'ta' => true,
        'd_n' => true,
        'ln' => true,
        'mdn' => true,
        'fn' => true,
        'mn' => true,
        'g' => true,
        'l' => true,
        'e' => true,
        'rst' => true,
        'u' => true,
        'p' => true,
        'lvl' => true,
        'dob_y' => true,
        'dob_m' => true,
        'dob_d' => true,
        'dod_y' => true,
        'dod_m' => true,
        'dod_d' => true,
        'dob_c' => true,
        'dod_c' => true,
        'h_c' => true,
        'e_c' => true,
        'n_n' => true,
        'loc' => true,
        'plob' => true,
        'plod' => true,
        'cod' => true,
        'plobu' => true,
        'in_i' => true,
        'in_a' => true,
        'in_p' => true,
        'in_c' => true,
        'in_q' => true,
        'in_m' => true,
        'in_tv' => true,
        'in_mu' => true,
        'in_b' => true,
        'in_s' => true,
        'cn_med' => true,
        'cn_mem' => true,
        'last_login' => true,
        'created' => true,
        'creator_id' => true,
        'modified' => true,
        'modifier_id' => true,
        'creator' => true,
        'modifier' => true,
        'imgnotes' => true,
        'settings' => true,

        'units' => true,
        'marriages' => true,
    ];

    /**
     * Set password method.
     *
     * @param string $password Users password.
     * @return bool
     */
    protected function _setP($password)
    {
        return (new DefaultPasswordHasher)->hash($password);
    }

    /**
     * Returns profile age
     *
     * @return bool|int
     */
    public function age()
    {
        $ret = false;

        if (!empty($this->dob_y)) {
            $dateString = $this->dob_y;

            $dateString .= '-' . (empty($this->dob_m) ? '12' : str_pad((string)$this->dob_m, 2, '0', STR_PAD_LEFT));
            $dateString .= '-' . (empty($this->dob_d) ? '31' : str_pad((string)$this->dob_d, 2, '0', STR_PAD_LEFT));

            $date = new FrozenDate($dateString);
            $now = new FrozenDate();

            $ret = $now->year - $date->year;
            if ($date->month > $now->month) {
                $ret--;
            } elseif ($date->month == $now->month && $date->day > $now->day) {
                $ret--;
            }
        }

        return $ret;
    }
}
