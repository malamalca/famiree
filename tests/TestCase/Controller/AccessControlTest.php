<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Family data is private: everything except the account pages requires a login.
 */
class AccessControlTest extends TestCase
{
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
        'app.Logs',
        'app.Posts',
        'app.PostsLinks',
        'app.Attachments',
        'app.AttachmentsLinks',
        'app.Imgnotes',
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
    }

    /**
     * Pages that are not reachable for guests
     *
     * @return array
     */
    public static function privateUrls(): array
    {
        return [
            'home' => ['/'],
            'dashboard' => ['/profiles/dashboard'],
            'profiles index' => ['/profiles'],
            'profile view' => ['/profiles/view/1'],
            'profile tree' => ['/profiles/tree'],
            'profile tree of other' => ['/profiles/tree/2'],
            'profile edit' => ['/profiles/edit/1'],
            'profile add' => ['/profiles/add/child/1'],
            'profile delete' => ['/profiles/delete/2'],
            'profile avatar' => ['/profiles/edit-avatar/1/remove'],
            'profile reorder children' => ['/profiles/reorder-children/1'],
            'profile autocomplete' => ['/profiles/autocomplete?term=Duck'],
            'posts' => ['/posts'],
            'post view' => ['/posts/view/1'],
            'post edit' => ['/posts/edit'],
            'post delete' => ['/posts/delete/1'],
            'attachments' => ['/attachments'],
            'attachment view' => ['/attachments/view/d372525d-9fb6-4643-bd21-217cb96d7495'],
            'attachment edit' => ['/attachments/edit'],
            'attachment delete' => ['/attachments/delete/d372525d-9fb6-4643-bd21-217cb96d7495'],
            'attachment display' => ['/attachments/display/d372525d-9fb6-4643-bd21-217cb96d7495/original/x.png'],
            'attachment link delete' => ['/attachments-links/delete/1'],
            'imgnote edit' => ['/imgnotes/edit/1'],
            'imgnote delete' => ['/imgnotes/delete/1'],
        ];
    }

    /**
     * Guests are sent to the login page and nothing is changed
     *
     * @param string $url Url to request.
     * @return void
     */
    #[DataProvider('privateUrls')]
    public function testGuestsAreRedirectedToLogin(string $url)
    {
        $counts = $this->rowCounts();

        $this->get($url);

        $this->assertRedirectContains('/users/login');
        $this->assertEquals($counts, $this->rowCounts(), 'Request of a guest must not change data');
    }

    /**
     * Guests can not write either
     *
     * @return void
     */
    public function testGuestsCanNotPost()
    {
        $counts = $this->rowCounts();

        $this->post('/profiles/add/child/1', [
            'fn' => 'Intruder',
            'g' => 'm',
            'units' => [0 => ['kind' => 'c', 'union_id' => 1]],
        ]);
        $this->assertRedirectContains('/users/login');

        $this->post('/profiles/edit/1', ['fn' => 'Hacked']);
        $this->assertRedirectContains('/users/login');

        $this->assertEquals($counts, $this->rowCounts());
        $this->assertSame('Donald', $this->fetchTable('Profiles')->get(1)->fn);
    }

    /**
     * A session with an unknown structure is not a login
     *
     * @return void
     */
    public function testEmptySessionIsNotLoggedIn()
    {
        $this->session(['Auth' => []]);

        $this->get('/profiles/view/1');

        $this->assertRedirectContains('/users/login');
    }

    /**
     * Account pages are open for guests
     *
     * @return void
     */
    public function testPublicPages()
    {
        $this->get('/users/login');
        $this->assertResponseOk();

        $this->get('/users/reset');
        $this->assertResponseOk();

        $this->get('/users/change-password/unknown');
        $this->assertResponseCode(404);

        $this->get('/users/install');
        $this->assertRedirect('/', 'Installer is only available while there are no profiles');
    }

    /**
     * Logout works for everybody and ends in the login page
     *
     * @return void
     */
    public function testLogoutAsGuest()
    {
        $this->get('/logout');

        $this->assertRedirectContains('/users/login');
    }

    /**
     * Logged in users can see the data
     *
     * @return void
     */
    public function testLoggedInUsersHaveAccess()
    {
        $this->session(['Auth' => ['id' => 1, 'd_n' => 'Donald Duck', 'lvl' => LVL_VIEWER]]);

        foreach (['/', '/profiles', '/profiles/view/1', '/profiles/tree', '/posts', '/attachments'] as $url) {
            $this->get($url);
            $this->assertResponseOk($url);
        }
    }

    /**
     * Number of rows in tables that can be changed from the web
     *
     * @return array<string, int>
     */
    private function rowCounts(): array
    {
        $ret = [];
        foreach (['Profiles', 'Units', 'Unions', 'Posts', 'Attachments', 'AttachmentsLinks', 'Imgnotes'] as $table) {
            $ret[$table] = $this->fetchTable($table)->find()->count();
        }

        return $ret;
    }
}
