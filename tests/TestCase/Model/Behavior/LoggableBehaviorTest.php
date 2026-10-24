<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Behavior;

use Cake\TestSuite\TestCase;

/**
 * App\Model\Behavior\LoggableBehavior Test Case
 *
 * Every change of a profile (and other loggable tables) is written to the logs table. The dashboard
 * "recent activity" and the change history are built from those rows.
 */
class LoggableBehaviorTest extends TestCase
{
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
    ];

    /**
     * @var \App\Model\Table\ProfilesTable
     */
    protected $Profiles;

    /**
     * @var \App\Model\Table\LogsTable
     */
    protected $Logs;

    /**
     * Setup test
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Profiles = $this->fetchTable('Profiles');
        $this->Logs = $this->fetchTable('Logs');
        // fixture row is not interesting
        $this->Logs->deleteAll([]);
    }

    /**
     * Returns the last log entry
     *
     * @return \App\Model\Entity\Log
     */
    private function lastLog()
    {
        return $this->Logs->find()->orderBy(['id' => 'DESC'])->firstOrFail();
    }

    /**
     * Adding a profile logs all of its (not excluded) properties
     *
     * @return void
     */
    public function testAddIsLogged()
    {
        $profile = $this->Profiles->saveOrFail(
            $this->Profiles->newEntity(['fn' => 'Donald', 'ln' => 'Duck', 'g' => 'm', 'job' => 'Sailor'])
        );

        $this->assertSame(1, $this->Logs->find()->count());
        $log = $this->lastLog();
        $this->assertSame('Profile', $log->class);
        $this->assertSame('add', $log->action);
        $this->assertEquals($profile->id, $log->foreign_id);
        $this->assertSame('Donald Duck', $log->title);

        $change = unserialize($log->change);
        $this->assertSame('Sailor', $change['job']['value']);
        $this->assertSame('Donald', $change['fn']['value']);
        $this->assertArrayNotHasKey('created', $change, 'Excluded properties are not logged');
        $this->assertArrayNotHasKey('modified', $change);
        $this->assertArrayNotHasKey('units', $change);
    }

    /**
     * Editing logs only changed properties with their old and new value
     *
     * @return void
     */
    public function testEditIsLogged()
    {
        $profile = $this->Profiles->get(1);
        $profile->job = 'Sailor';
        $profile->loc = 'Duckburg';
        $this->Profiles->saveOrFail($profile);

        $log = $this->lastLog();
        $this->assertSame('edit', $log->action);
        $this->assertEquals(1, $log->foreign_id);

        $change = unserialize($log->change);
        $this->assertEqualsCanonicalizing(['job', 'loc'], array_keys($change));
        $this->assertNull($change['job']['old']);
        $this->assertSame('Sailor', $change['job']['value']);

        // second change records the previous value
        $profile = $this->Profiles->get(1);
        $profile->job = 'Captain';
        $this->Profiles->saveOrFail($profile);

        $change = unserialize($this->lastLog()->change);
        $this->assertSame(['job'], array_keys($change));
        $this->assertSame('Sailor', $change['job']['old']);
        $this->assertSame('Captain', $change['job']['value']);
    }

    /**
     * Saving without changes does not create log entries
     *
     * @return void
     */
    public function testUnchangedSaveIsNotLogged()
    {
        $profile = $this->Profiles->get(1);
        $this->Profiles->saveOrFail($profile);

        $this->assertSame(0, $this->Logs->find()->count());
    }

    /**
     * Deleting is logged together with the data of the deleted profile
     *
     * @return void
     */
    public function testDeleteIsLogged()
    {
        $profile = $this->Profiles->get(2);
        $this->assertTrue((bool)$this->Profiles->delete($profile));

        $log = $this->lastLog();
        $this->assertSame('delete', $log->action);
        $this->assertSame('Profile', $log->class);
        $this->assertEquals(2, $log->foreign_id);
        $this->assertSame('Huey Duck', $log->title);

        $change = unserialize($log->change);
        $this->assertSame('Huey', $change['fn']['value']);
    }

    /**
     * Without a logged in user (cli, install) the log has no user
     *
     * @return void
     */
    public function testLogWithoutUser()
    {
        $profile = $this->Profiles->get(1);
        $profile->job = 'Sailor';
        $this->Profiles->saveOrFail($profile);

        $this->assertNull($this->lastLog()->user_id);
    }
}
