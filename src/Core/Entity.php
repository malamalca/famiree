<?php
declare(strict_types=1);

namespace App\Core;

class Entity {
    public static function getFieldList()
    {
        $classVars = get_class_vars(static::class);

        return array_keys($classVars);
    }
}