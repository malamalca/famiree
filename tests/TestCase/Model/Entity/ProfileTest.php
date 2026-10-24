<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Entity;

use App\Model\Entity\Profile;
use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\Chronos\Chronos;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Entity\Profile Test Case
 */
class ProfileTest extends TestCase
{
    /**
     * Fixed "today" for age calculations: 2026-06-10
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        Chronos::setTestNow('2026-06-10 12:00:00');
    }

    /**
     * Tear down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        Chronos::setTestNow(Chronos::now());
    }

    /**
     * Age is not known without year of birth
     *
     * @return void
     */
    public function testAgeUnknown()
    {
        $this->assertFalse((new Profile([]))->age());
        $this->assertFalse((new Profile(['dob_m' => 6, 'dob_d' => 9]))->age());
    }

    /**
     * Age on, before and after the birthday
     *
     * @return void
     */
    public function testAgeWithFullDate()
    {
        $this->assertSame(92, (new Profile(['dob_y' => 1934, 'dob_m' => 6, 'dob_d' => 10]))->age(), 'birthday today');
        $this->assertSame(92, (new Profile(['dob_y' => 1934, 'dob_m' => 6, 'dob_d' => 9]))->age(), 'birthday yesterday');
        $this->assertSame(91, (new Profile(['dob_y' => 1934, 'dob_m' => 6, 'dob_d' => 11]))->age(), 'birthday tomorrow');
        $this->assertSame(91, (new Profile(['dob_y' => 1934, 'dob_m' => 7, 'dob_d' => 1]))->age(), 'birthday next month');
        $this->assertSame(92, (new Profile(['dob_y' => 1934, 'dob_m' => 5, 'dob_d' => 31]))->age(), 'birthday last month');
    }

    /**
     * Missing month and day mean the last day of the year (age is never overstated)
     *
     * @return void
     */
    public function testAgeWithPartialDate()
    {
        $this->assertSame(91, (new Profile(['dob_y' => 1934]))->age());
        $this->assertSame(92, (new Profile(['dob_y' => 1934, 'dob_m' => 5]))->age());
        $this->assertSame(91, (new Profile(['dob_y' => 1934, 'dob_m' => 6]))->age());
    }

    /**
     * Newborn
     *
     * @return void
     */
    public function testAgeOfNewborn()
    {
        $this->assertSame(0, (new Profile(['dob_y' => 2026, 'dob_m' => 6, 'dob_d' => 1]))->age());
    }

    /**
     * Password is hashed when set
     *
     * @return void
     */
    public function testPasswordIsHashed()
    {
        $profile = new Profile(['fn' => 'Donald', 'p' => 'secret']);

        $this->assertNotSame('secret', $profile->p);
        $this->assertTrue((new DefaultPasswordHasher())->check('secret', $profile->p));
        $this->assertFalse((new DefaultPasswordHasher())->check('other', $profile->p));
    }

    /**
     * Empty password is not hashed, an empty password must never become a valid one
     *
     * @return void
     */
    public function testEmptyPasswordIsNotHashed()
    {
        $profile = new Profile(['fn' => 'Donald']);

        $profile->p = null;
        $this->assertNull($profile->p);

        $profile->p = '';
        $this->assertSame('', $profile->p);
        $this->assertFalse((new DefaultPasswordHasher())->check('', $profile->p));
    }
}
