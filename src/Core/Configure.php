<?php
declare(strict_types=1);

namespace App\Core;

class Configure
{
    /**
     * @var \App\Core\Config
     */
    private static $_instance = null;

    /**
     * @var array
     */
    private $config;

    /**
     * Config constructor.
     *
     * @param array $config Config array
     * @return void
     */
    private function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * Returns the instance.
     *
     * @param array $config Config array
     * @return \Config
     */
    public static function getInstance($config = null)
    {
        if (self::$_instance == null) {
            self::$_instance = new self($config);
        }

        return self::$_instance;
    }

    /**
     * Read a config item.
     *
     * @param string $key Configure key
     * @param mixed $default Default value
     * @return mixed
     */
    public static function read($key, $default = null)
    {
        $instance = self::getInstance();

        $ret = $default;
        $levels = (array)explode('.', $key);

        if (!empty($levels)) {
            $base = $instance->config;

            $i = 0;

            while (isset($base[$levels[$i]])) {
                if (is_array($base[$levels[$i]]) && ($i < count($levels) - 1)) {
                    $base = $base[$levels[$i]];

                    $i++;
                } else {
                    if ($i == count($levels) - 1) {
                        return $base[$levels[$i]];
                    }
                }
            }
        }

        return $ret;
    }

    /**
     * Empty function
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Empty function
     *
     * @return void
     */
    public function __wakeup()
    {
    }

    /**
     * Empty function
     *
     * @return void
     */
    public function __destruct()
    {
        self::$_instance = null;
    }
}
