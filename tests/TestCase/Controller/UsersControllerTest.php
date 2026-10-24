<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\TestSuite\EmailTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\Utility\Security;

/**
 * App\Controller\UsersController Test Case
 */
class UsersControllerTest extends TestCase
{
    use EmailTrait;
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'app.Profiles',
        'app.Units',
        'app.Unions',
        'app.Posts',
        'app.Logs',
    ];

    /**
     * Setup test
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->enableRetainFlashMessages();

        $this->setPassword((new DefaultPasswordHasher())->hash('secret'));
    }

    /**
     * Stores a password hash for profile 1 without going through the entity setter.
     *
     * @param string $hash Password hash.
     * @return void
     */
    private function setPassword(string $hash): void
    {
        $this->fetchTable('Profiles')->updateAll(['p' => $hash], ['id' => 1]);
    }

    /**
     * Unauthenticated requests are redirected to the login page
     *
     * @return void
     */
    public function testUnauthenticatedRedirect()
    {
        $this->get('/');

        $this->assertRedirectContains('/users/login?redirect=');
    }

    /**
     * Test login form
     *
     * @return void
     */
    public function testLoginForm()
    {
        $this->get('/users/login');

        $this->assertResponseOk();
        $this->assertResponseContains('name="u"');
        $this->assertResponseContains('name="p"');
    }

    /**
     * Test successful login
     *
     * @return void
     */
    public function testLogin()
    {
        $this->post('/users/login', ['u' => 'donald.duck', 'p' => 'secret']);

        $this->assertRedirect('/');
        $this->assertSession(1, 'Auth.id');
    }

    /**
     * Test login with a wrong password
     *
     * @return void
     */
    public function testLoginWrongPassword()
    {
        $this->post('/users/login', ['u' => 'donald.duck', 'p' => 'wrong']);

        $this->assertResponseOk();
        $this->assertNoRedirect();
        $this->assertFlashElement('flash/error');
        $this->assertSessionNotHasKey('Auth');
    }

    /**
     * Old sha1 hashes are accepted and replaced by a new hash.
     *
     * @return void
     */
    public function testLoginRehashesLegacyPassword()
    {
        $this->setPassword(Security::hash('secret', 'sha1', true));

        $this->post('/users/login', ['u' => 'donald.duck', 'p' => 'secret']);

        $this->assertRedirect('/');
        $hash = $this->fetchTable('Profiles')->get(1)->get('p');
        $this->assertStringStartsWith('$', $hash);
        $this->assertTrue((new DefaultPasswordHasher())->check('secret', $hash));
    }

    /**
     * Test login on remember me cookie
     *
     * @return void
     */
    public function testLoginWithRememberMeCookie()
    {
        $this->post('/users/login', ['u' => 'donald.duck', 'p' => 'secret', 'remember_me' => '1']);

        $this->assertRedirect('/');
        $value = null;
        foreach ($this->_response->getHeader('Set-Cookie') as $header) {
            if (str_starts_with($header, 'famiree_auth=')) {
                $value = urldecode(explode(';', substr($header, strlen('famiree_auth=')))[0]);
            }
        }
        $this->assertNotEmpty($value);

        // new session, only the cookie identifies the user
        $this->session(['Auth' => null]);
        $this->cookie('famiree_auth', $value);
        $this->get('/users/login');

        $this->assertRedirect('/');
    }

    /**
     * Test logout
     *
     * @return void
     */
    public function testLogout()
    {
        $this->session(['Auth' => ['id' => 1, 'd_n' => 'Donald Duck', 'lvl' => 2]]);

        $this->get('/logout');

        $this->assertRedirectContains('/users/login');
        $this->assertSessionNotHasKey('Auth');
    }

    /**
     * Logged in users are sent away from the login page
     *
     * @return void
     */
    public function testLoginPageWhenLoggedIn()
    {
        $this->session(['Auth' => ['id' => 1, 'd_n' => 'Donald Duck', 'lvl' => 2]]);

        $this->get('/users/login');

        $this->assertRedirect('/');
    }

    /**
     * After login the user is returned to the page he wanted to visit
     *
     * @return void
     */
    public function testLoginRedirectsToRequestedPage()
    {
        $this->post('/users/login?redirect=%2Fprofiles%2Fview%2F2', ['u' => 'donald.duck', 'p' => 'secret']);

        $this->assertRedirect('/profiles/view/2');
    }

    /**
     * Redirect target after login must stay on this site
     *
     * @return void
     */
    public function testLoginDoesNotRedirectToOtherSites()
    {
        $this->post('/users/login?redirect=https%3A%2F%2Fevil.example%2F', ['u' => 'donald.duck', 'p' => 'secret']);

        $this->assertRedirect('/');
    }

    /**
     * Unknown user
     *
     * @return void
     */
    public function testLoginUnknownUser()
    {
        $this->post('/users/login', ['u' => 'nobody', 'p' => 'secret']);

        $this->assertNoRedirect();
        $this->assertFlashElement('flash/error');
        $this->assertSessionNotHasKey('Auth');
    }

    /**
     * Profiles without a password (people in the family tree that are not users) can not log in
     *
     * @return void
     */
    public function testLoginWithoutPasswordIsImpossible()
    {
        $this->fetchTable('Profiles')->updateAll(['u' => 'huey.duck', 'p' => null], ['id' => 2]);

        foreach (['', 'secret'] as $password) {
            $this->post('/users/login', ['u' => 'huey.duck', 'p' => $password]);
            $this->assertNoRedirect();
            $this->assertSessionNotHasKey('Auth');
        }
    }

    /**
     * Password reset request sends a mail
     *
     * @return void
     */
    public function testReset()
    {
        $this->get('/users/reset');
        $this->assertResponseOk();

        $this->post('/users/reset', ['email' => 'donald.duck@test.com']);

        $this->assertFlashElement('flash/success');
        $this->assertMailCount(1);
        $this->assertMailSentTo('donald.duck@test.com');
        $key = $this->fetchTable('Profiles')->get(1)->rst;
        $this->assertNotEmpty($key);
        $this->assertMailContainsText('/users/change-password/' . $key);
    }

    /**
     * Reset for an unknown address does not send mail
     *
     * @return void
     */
    public function testResetUnknownEmail()
    {
        $this->post('/users/reset', ['email' => 'nobody@test.com']);

        $this->assertFlashElement('flash/error');
        $this->assertNoMailSent();
    }

    /**
     * Change password with a valid key
     *
     * @return void
     */
    public function testChangePassword()
    {
        $this->fetchTable('Profiles')->updateAll(['rst' => 'abc123'], ['id' => 1]);

        $this->get('/users/change-password/abc123');
        $this->assertResponseOk();

        $this->post('/users/change-password/abc123', ['p' => 'brandnew', 'repeat_pass' => 'brandnew']);
        $this->assertRedirect('/');
        $this->assertFlashElement('flash/success');

        // old password does not work any more, new one does
        $this->post('/users/login', ['u' => 'donald.duck', 'p' => 'secret']);
        $this->assertNoRedirect();
        $this->post('/users/login', ['u' => 'donald.duck', 'p' => 'brandnew']);
        $this->assertRedirect('/');
    }

    /**
     * Passwords have to match to be changed
     *
     * @return void
     */
    public function testChangePasswordMismatch()
    {
        $this->fetchTable('Profiles')->updateAll(['rst' => 'abc123'], ['id' => 1]);
        $before = $this->fetchTable('Profiles')->get(1)->p;

        $this->post('/users/change-password/abc123', ['p' => 'brandnew', 'repeat_pass' => 'different']);

        $this->assertNoRedirect();
        $this->assertFlashElement('flash/error');
        $this->assertSame($before, $this->fetchTable('Profiles')->get(1)->p);
    }

    /**
     * Unknown reset key
     *
     * @return void
     */
    public function testChangePasswordWithWrongKey()
    {
        $this->get('/users/change-password/doesnotexist');

        $this->assertResponseCode(404);
    }

    /**
     * Install is not possible when there are already users
     *
     * @return void
     */
    public function testInstallWhenUsersExist()
    {
        $this->get('/users/install');

        $this->assertRedirect('/');
    }

    /**
     * First user is created on a fresh installation
     *
     * @return void
     */
    public function testInstall()
    {
        $profiles = $this->fetchTable('Profiles');
        $profiles->deleteAll([]);

        // everything is redirected to the installer while there are no profiles
        $this->get('/profiles/dashboard');
        $this->assertRedirect(['controller' => 'Users', 'action' => 'install']);

        $this->get('/users/install');
        $this->assertResponseOk();

        $this->post('/users/install', [
            'fn' => 'Donald',
            'ln' => 'Duck',
            'u' => 'donald',
            'e' => 'donald@duck.test',
            'p' => 'secret',
            'repeat_pass' => 'secret',
        ]);

        $this->assertRedirect('/');
        $this->assertSame(1, $profiles->find()->count());
        $user = $profiles->find()->firstOrFail();
        $this->assertSame('Donald Duck', $user->d_n);
        $this->assertNotSame('secret', $user->p);
    }
}
