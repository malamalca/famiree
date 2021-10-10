<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Core\Table;

class SettingsTable extends Table
{
    public $entityName = '\App\Model\Entity\Setting';
    public $tableName = 'settings';
    public $fieldList = ['id', 'value'];

    /**
     * Override default get() to return newEntity when setting does not exist.
     *
     * @param string $id Setting id
     * @param string $defaultValue Default setting value if it does not exist.
     * @return \App\Model\Entity\Setting
     */
    public function get($id, $defaultValue = null)
    {
        $ret = parent::get($id);

        if (!$ret) {
            $ret = $this->newEntity();
            $ret->id = $id;
            $ret->value = $defaultValue;
        }

        return $ret;
    }

    /**
     * @param \App\Model\Entity\Setting $setting Setting Entity.
     * @return bool
     */
    public function validate($setting)
    {
        $setting->hasErrors = false;

        switch ($setting->id) {
            case 'name':
                $setting->hasErrors = empty($setting->value);
        }

        return !$setting->hasErrors;
    }

    /**
     * @param \App\Model\Entity\Setting $setting Setting Entity.
     * @return bool|\App\Model\Entity\Setting
     */
    public function patchAndSave($setting)
    {
        if (isset($_POST[$setting->id])) {
            $setting->value = $_POST[$setting->id];
        }

        if ($this->validate($setting)) {
            return $this->save($setting);
        }

        return false;
    }
}
