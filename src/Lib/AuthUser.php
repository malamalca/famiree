<?php
namespace App\Lib;

use ArrayAccess;
use Cake\ORM\TableRegistry;

class AuthUser implements ArrayAccess
{
    const GLOBAL_ROLE_ROOT = 2;

    const PROJECT_ROLE_OWNER = 2;
    const PROJECT_ROLE_ADMIN = 5;
    const PROJECT_ROLE_EDITOR = 7;
    const PROJECT_ROLE_READER = 10;

    private $container = [];

    /**
     * Add admin sidebar elements.
     *
     * @param array $userArray Array returned by Auth::user().
     * @access public
     * @return void
     */
    public function __construct($userArray)
    {
        $this->container = $userArray;
    }

    /**
     * Checks is current user exists
     *
     * @return bool
     */
    public function exists()
    {
        return !empty($this->container['id']);
    }

    /**
     * Checks user's role.
     *
     * @param int $role User role.
     * @return bool
     */
    public function role($role)
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
    public function get($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * ArrayAccess::offsetSet()
     *
     * @param string $offset Array offset.
     * @param mixed $value Array element value.
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @return mixed
     */
    public function offsetSet($offset, $value)
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
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * ArrayAccess::offsetUnset()
     *
     * @param string $offset Array offset.
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * ArrayAccess::offsetGet()
     *
     * @param string $offset Array offset.
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @return bool
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
}
