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
     * @access public
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
     * @params uuid $projectId Project id.
     * @access public
     * @return bool
     */
    public function role($role, $projectId = null)
    {
        static $roles;

        if (!$this->exists()) {
            return false;
        }

        if (is_null($projectId)) {
            return $this->container['privileges'] <= $role;
        } else {
            if (!isset($roles[$projectId][$this->get('id')])) {
                $ProjectsUsersTable = TableRegistry::get('ProjectsUsers');
                $projectsUser = $ProjectsUsersTable->find()
                    ->select(['role'])
                    ->where([
                            'project_id' => $projectId,
                            'user_id' => $this->get('id')
                    ])
                    ->first();

                if ($projectsUser) {
                    $roles[$projectId][$this->get('id')] = $projectsUser->role;

                    return $projectsUser->role <= $role;
                }
            } else {
                return $roles[$projectId][$this->get('id')] <= $role;
            }
        }

        return false;
    }

    /**
     * Get array value
     *
     * @param string $offset Array offset.
     * @access public
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
     * @access public
     * @return mixed
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
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
     * @access public
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
     * @access public
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
     * @access public
     * @return bool
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }
}
