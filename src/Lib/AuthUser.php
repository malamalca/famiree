<?php
declare(strict_types=1);

namespace App\Lib;

use ArrayAccess;

class AuthUser implements ArrayAccess
{
    public const GLOBAL_ROLE_ROOT = 2;

    public const PROJECT_ROLE_OWNER = 2;
    public const PROJECT_ROLE_ADMIN = 5;
    public const PROJECT_ROLE_EDITOR = 7;
    public const PROJECT_ROLE_READER = 10;

    private array $container = [];

    /**
     * Add admin sidebar elements.
     *
     * @param array $userArray Array returned by Auth::user().
     * @access public
     * @return mixed
     */
    public function __construct(array $userArray)
    {
        $this->container = $userArray;
    }

    /**
     * Checks is current user exists
     *
     * @return bool
     */
    public function exists(): bool
    {
        return !empty($this->container['id']);
    }

    /**
     * Checks user's role.
     *
     * @param int $role User role.
     * @return bool
     */
    public function role(int $role): bool
    {
        static $roles;

        if (!$this->exists()) {
            return false;
        }

        return $this->container['lvl'] <= $role;
    }

    /**
     * Get array value
     *
     * @param string $offset Array offset.
     * @return mixed
     */
    public function get(string $offset): mixed
    {
        return $this->container[$offset] ?? null;
    }

    /**
     * ArrayAccess::offsetSet()
     *
     * @param mixed $offset Array offset.
     * @param mixed $value Array element value.
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (empty($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * ArrayAccess::offsetExists()
     *
     * @param string $offset Array offset.
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->container[$offset]);
    }

    /**
     * ArrayAccess::offsetUnset()
     *
     * @param mixed $offset Array offset.
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->container[$offset]);
    }

    /**
     * ArrayAccess::offsetGet()
     *
     * @param string $offset Array offset.
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->container[$offset] ?? null;
    }
}
