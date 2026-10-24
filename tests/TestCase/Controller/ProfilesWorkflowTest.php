<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Test\TestCase\FamilyBuilderTrait;
use Cake\Cache\Cache;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * Family building workflows of App\Controller\ProfilesController
 */
class ProfilesWorkflowTest extends TestCase
{
    use FamilyBuilderTrait;
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
        $this->enableRetainFlashMessages();

        $this->session(['Auth' => ['id' => 1, 'd_n' => 'Donald Duck', 'lvl' => LVL_ROOT]]);
    }

    /**
     * Number of rows
     *
     * @param string $table Table name.
     * @return int
     */
    private function rows(string $table): int
    {
        return $this->fetchTable($table)->find()->count();
    }

    /**
     * Adding a spouse creates a new union with both profiles as parents
     *
     * @return void
     */
    public function testAddSpouseCreatesUnion()
    {
        $unions = $this->rows('Unions');
        $units = $this->rows('Units');

        $this->post('/profiles/add/spouse/2', [
            'ln' => 'Duck',
            'fn' => 'Mathilda',
            'g' => 'f',
            'l' => 1,
            'units' => [0 => ['kind' => 'p', 'union_id' => '']],
        ]);

        $this->assertRedirectContains('/profiles/view/');
        $this->assertEquals($unions + 1, $this->rows('Unions'));
        $this->assertEquals($units + 2, $this->rows('Units'));

        $spouse = $this->fetchTable('Profiles')->find()->where(['fn' => 'Mathilda'])->firstOrFail();
        $marriages = $this->fetchTable('Profiles')->family(2, 'marriages');
        $this->assertCount(1, $marriages);
        $this->assertEquals($spouse->id, $marriages[0]['spouse']->id);
    }

    /**
     * Adding a parent to an existing union
     *
     * @return void
     */
    public function testAddSecondParent()
    {
        $units = $this->rows('Units');

        $this->post('/profiles/add/parent/2', [
            'ln' => 'Duck',
            'fn' => 'Daisy',
            'g' => 'f',
            'l' => 1,
            'units' => [0 => ['kind' => 'p', 'union_id' => 1]],
        ]);

        $this->assertRedirectContains('/profiles/view/');
        $this->assertEquals($units + 1, $this->rows('Units'));
        $this->assertCount(2, $this->fetchTable('Profiles')->family(2, 'parents'));
    }

    /**
     * A third parent is refused
     *
     * @return void
     */
    public function testAddThirdParentIsRefused()
    {
        $this->buildFamily();
        $profiles = $this->rows('Profiles');

        $this->get('/profiles/add/parent/' . $this->ids['huey']);

        $this->assertRedirect();
        $this->assertFlashElement('flash/error');
        $this->assertEquals($profiles, $this->rows('Profiles'));
    }

    /**
     * Adding a sibling to an existing family
     *
     * @return void
     */
    public function testAddSibling()
    {
        $this->post('/profiles/add/sibling/2', [
            'ln' => 'Duck',
            'fn' => 'Louie',
            'g' => 'm',
            'l' => 1,
            'units' => [0 => ['kind' => 'c', 'union_id' => 1]],
        ]);

        $this->assertRedirectContains('/profiles/view/');
        $siblings = $this->fetchTable('Profiles')->family(2, 'siblings');
        $this->assertCount(1, $siblings);
        $this->assertSame('Louie', $siblings[0]->fn);
    }

    /**
     * Invalid data is not saved
     *
     * @return void
     */
    public function testAddWithEmptyFirstName()
    {
        $profiles = $this->rows('Profiles');

        $this->post('/profiles/add/child/1', [
            'ln' => 'Duck',
            'fn' => '',
            'g' => 'm',
            'units' => [0 => ['kind' => 'c', 'union_id' => 1]],
        ]);

        $this->assertResponseOk();
        $this->assertNoRedirect();
        $this->assertFlashElement('flash/error');
        $this->assertEquals($profiles, $this->rows('Profiles'));
    }

    /**
     * The base profile of an add request must exist
     *
     * @return void
     */
    public function testAddToMissingProfile()
    {
        $this->get('/profiles/add/child/9999');

        $this->assertResponseCode(404);
    }

    /**
     * Edits are logged with the user that made them
     *
     * @return void
     */
    public function testEditIsLoggedWithUser()
    {
        $this->fetchTable('Logs')->deleteAll([]);

        $this->post('/profiles/edit/2', ['id' => 2, 'job' => 'Pilot']);

        $this->assertRedirectContains('/profiles/view/2');
        $log = $this->fetchTable('Logs')->find()->orderBy(['id' => 'DESC'])->firstOrFail();
        $this->assertSame('edit', $log->action);
        $this->assertSame('Profile', $log->class);
        $this->assertEquals(2, $log->foreign_id);
        $this->assertEquals(1, $log->user_id, 'Log has to be attributed to the logged in user');
    }

    /**
     * Deleting a profile removes it from its family and logs the deletion
     *
     * @return void
     */
    public function testDeleteProfile()
    {
        $this->buildFamily();
        $this->fetchTable('Logs')->deleteAll([]);

        $this->get('/profiles/delete/' . $this->ids['dewey']);

        $this->assertRedirect(['controller' => 'Profiles', 'action' => 'tree']);
        $this->assertFlashElement('flash/success');
        $this->assertFalse($this->fetchTable('Profiles')->exists(['id' => $this->ids['dewey']]));
        $this->assertCount(0, $this->fetchTable('Profiles')->family($this->ids['huey'], 'siblings'));

        $log = $this->fetchTable('Logs')->find()->where(['action' => 'delete'])->firstOrFail();
        $this->assertEquals(1, $log->user_id);
        $this->assertSame('Dewey Duck', $log->title);
    }

    /**
     * Changing and removing the avatar
     *
     * @return void
     */
    public function testEditAvatarSetAndRemove()
    {
        $this->get('/profiles/edit-avatar/1/remove');
        $this->assertRedirect(['controller' => 'Profiles', 'action' => 'view', '1']);
        $this->assertNull($this->fetchTable('Profiles')->get(1)->ta);

        $attachment = $this->fetchTable('Attachments')->find()->firstOrFail();
        $this->get('/profiles/edit-avatar/1/' . $attachment->id);
        $this->assertRedirect(['controller' => 'Profiles', 'action' => 'view', '1']);
        $this->assertSame($attachment->id, $this->fetchTable('Profiles')->get(1)->ta);
    }

    /**
     * Children can be reordered
     *
     * @return void
     */
    public function testReorderChildren()
    {
        $this->buildFamily();
        $units = $this->fetchTable('Units');
        $huey = $units->find()->where(['union_id' => 1, 'profile_id' => $this->ids['huey']])->firstOrFail();
        $dewey = $units->find()->where(['union_id' => 1, 'profile_id' => $this->ids['dewey']])->firstOrFail();

        $this->get('/profiles/reorder-children/1');
        $this->assertResponseOk();

        $this->post('/profiles/reorder-children/1', [
            'units' => [
                ['id' => $dewey->id, 'sort_order' => 0],
                ['id' => $huey->id, 'sort_order' => 1],
            ],
        ]);

        $this->assertRedirect(['controller' => 'Profiles', 'action' => 'view', '1']);
        $this->assertEquals(0, $units->get($dewey->id)->sort_order);
        $this->assertEquals(1, $units->get($huey->id)->sort_order);

        // children are returned in the new order
        $children = $this->fetchTable('Profiles')->family(1, 'marriages')[0]['children'];
        $this->assertSame('Dewey', $children[0]->fn);
    }

    /**
     * Autocomplete returns label/value pairs for terms with at least two characters
     *
     * @return void
     */
    public function testAutocomplete()
    {
        $this->get('/profiles/autocomplete?term=Hue');

        $this->assertResponseOk();
        $this->assertSame([['label' => 'Huey Duck', 'value' => 2]], json_decode((string)$this->_response->getBody(), true));

        $this->get('/profiles/autocomplete?term=Duck');
        $this->assertCount(2, json_decode((string)$this->_response->getBody(), true));

        $this->get('/profiles/autocomplete?term=D');
        $this->assertSame([], json_decode((string)$this->_response->getBody(), true));
    }

    /**
     * Autocomplete without a term is not a valid call
     *
     * @return void
     */
    public function testAutocompleteWithoutTerm()
    {
        $this->get('/profiles/autocomplete');

        $this->assertResponseCode(404);
    }

    /**
     * Search on the index page filters profiles
     *
     * @return void
     */
    public function testIndexSearch()
    {
        $this->get('/profiles/index?criterio=Hue');

        $this->assertResponseOk();
        $this->assertResponseContains('Huey Duck');
        $this->assertResponseNotContains('Donald Duck</a>');
    }

    /**
     * The tree of the logged in user is shown without an id
     *
     * @return void
     */
    public function testTreeOfLoggedInUser()
    {
        $this->buildFamily();

        $this->get('/profiles/tree');
        $this->assertResponseOk();
        $this->assertResponseContains('Donald Duck');

        $this->get('/profiles/tree/' . $this->ids['huey'] . '?depth=1');
        $this->assertResponseOk();
        $this->assertResponseContains('Huey Duck');
    }

    /**
     * Profile view shows the family
     *
     * @return void
     */
    public function testViewShowsFamily()
    {
        $this->buildFamily();

        $this->get('/profiles/view/' . $this->ids['huey']);

        $this->assertResponseOk();
        $this->assertResponseContains('Huey');
        $this->assertResponseContains('Dewey');
    }

    /**
     * Dashboard lists upcoming birthdays in groups (tomorrow, this month, next month, further on)
     *
     * @return void
     */
    public function testDashboardBirthdays()
    {
        $today = new \DateTimeImmutable('today');
        $year = (int)$today->format('Y');
        $lastDay = new \DateTimeImmutable($year . '-12-31');
        if ($lastDay->diff($today)->days < 50) {
            $this->markTestSkipped('Birthdays are only listed until the end of the year.');
        }

        $profiles = $this->fetchTable('Profiles');
        Cache::delete('Profiles.birthdays');
        foreach (
            [
                'Tomorrow' => '+1 day',
                'Nextmonth' => 'first day of next month',
                'Later' => '+45 days',
            ] as $name => $when
        ) {
            $birthday = $today->modify($when);
            $profiles->saveOrFail($profiles->newEntity([
                'fn' => 'Birthday' . $name,
                'ln' => 'Duck',
                'g' => 'f',
                'l' => true,
                'dob_y' => 1970,
                'dob_m' => (int)$birthday->format('n'),
                'dob_d' => (int)$birthday->format('j'),
            ]));
        }
        Cache::delete('Profiles.birthdays');

        $this->get('/profiles/dashboard');

        $this->assertResponseOk();
        $this->assertResponseContains('BirthdayTomorrow Duck');
        $this->assertResponseContains('BirthdayNextmonth Duck');
        $this->assertResponseContains('BirthdayLater Duck');
        $this->assertResponseContains('date_title');
        Cache::delete('Profiles.birthdays');
    }

    /**
     * View of a missing profile is a 404
     *
     * @return void
     */
    public function testViewMissingProfile()
    {
        $this->get('/profiles/view/9999');

        $this->assertResponseCode(404);
    }
}
