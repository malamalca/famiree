<?php
declare(strict_types=1);

namespace App\Test\TestCase\Lib;

use App\Lib\AuthUser;
use Cake\TestSuite\TestCase;

/**
 * App\Lib\AuthUser Test Case
 */
class AuthUserTest extends TestCase
{
    /**
     * Guests do not exist and have no role
     *
     * @return void
     */
    public function testGuest()
    {
        $user = new AuthUser([]);

        $this->assertFalse($user->exists());
        $this->assertFalse($user->role(LVL_VIEWER));
        $this->assertNull($user->get('id'));
        $this->assertNull($user['d_n']);
        $this->assertFalse(isset($user['d_n']));
    }

    /**
     * Logged in user
     *
     * @return void
     */
    public function testLoggedInUser()
    {
        $user = new AuthUser(['id' => 5, 'd_n' => 'Donald Duck', 'lvl' => LVL_EDITOR]);

        $this->assertTrue($user->exists());
        $this->assertSame(5, $user->get('id'));
        $this->assertSame('Donald Duck', $user['d_n']);
        $this->assertTrue(isset($user['d_n']));
        $this->assertNull($user->get('missing'));
    }

    /**
     * Lower level number means more privileges
     *
     * @return void
     */
    public function testRole()
    {
        $editor = new AuthUser(['id' => 5, 'lvl' => LVL_EDITOR]);

        $this->assertTrue($editor->role(LVL_EDITOR));
        $this->assertTrue($editor->role(LVL_VIEWER));
        $this->assertFalse($editor->role(LVL_ADMIN));
        $this->assertFalse($editor->role(LVL_ROOT));

        $root = new AuthUser(['id' => 1, 'lvl' => LVL_ROOT]);
        $this->assertTrue($root->role(LVL_ROOT));
    }

    /**
     * ArrayAccess set and unset
     *
     * @return void
     */
    public function testArrayAccess()
    {
        $user = new AuthUser(['id' => 5]);

        $user['d_n'] = 'Daisy';
        $this->assertSame('Daisy', $user->get('d_n'));

        unset($user['d_n']);
        $this->assertNull($user->get('d_n'));
    }
}
