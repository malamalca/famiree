<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\Core\Configure;
use Cake\TestSuite\EmailTrait;
use Cake\TestSuite\TestCase;

/**
 * Account related parts of App\Model\Table\ProfilesTable: validation, display name and password reset mail.
 */
class ProfilesAccountTest extends TestCase
{
    use EmailTrait;

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
     * Setup test
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->loadRoutes();
        $this->Profiles = $this->fetchTable('Profiles');
    }

    /**
     * First name is required
     *
     * @return void
     */
    public function testFirstNameIsRequired()
    {
        $profile = $this->Profiles->newEntity(['fn' => '', 'ln' => 'Duck', 'g' => 'm']);
        $this->assertArrayHasKey('fn', $profile->getErrors());

        $profile = $this->Profiles->newEntity(['fn' => 'Donald', 'g' => 'm']);
        $this->assertEmpty($profile->getErrors());
    }

    /**
     * Display name is built from first, middle and last name
     *
     * @return void
     */
    public function testDisplayNameIsBuiltOnSave()
    {
        $profile = $this->Profiles->saveOrFail(
            $this->Profiles->newEntity(['fn' => 'Donald', 'mn' => 'Fauntleroy', 'ln' => 'Duck', 'g' => 'm'])
        );
        $this->assertSame('Donald Fauntleroy Duck', $profile->d_n);

        $profile = $this->Profiles->saveOrFail(
            $this->Profiles->newEntity(['fn' => 'Daisy', 'g' => 'f'])
        );
        $this->assertSame('Daisy', $profile->d_n, 'No trailing spaces without last name');

        // renaming rebuilds the display name
        $profile->ln = 'Duck';
        $this->Profiles->saveOrFail($profile);
        $this->assertSame('Daisy Duck', $this->Profiles->get($profile->id)->d_n);

        // unrelated change keeps it
        $profile->g = 'f';
        $profile->job = 'Secretary';
        $this->Profiles->saveOrFail($profile);
        $this->assertSame('Daisy Duck', $this->Profiles->get($profile->id)->d_n);
    }

    /**
     * Install validation: all fields and a matching password are needed
     *
     * @return void
     */
    public function testInstallValidation()
    {
        $profile = $this->Profiles->newEntity(
            ['fn' => '', 'ln' => '', 'u' => '', 'e' => '', 'p' => '', 'repeat_pass' => ''],
            ['validate' => 'install']
        );
        $errors = $profile->getErrors();
        foreach (['fn', 'ln', 'u', 'e'] as $field) {
            $this->assertArrayHasKey($field, $errors, "$field should be required");
        }

        $valid = [
            'fn' => 'Donald',
            'ln' => 'Duck',
            'u' => 'donald',
            'e' => 'donald@duck.test',
            'p' => 'secret',
            'repeat_pass' => 'secret',
        ];
        $this->assertEmpty($this->Profiles->newEntity($valid, ['validate' => 'install'])->getErrors());

        $errors = $this->Profiles->newEntity(['e' => 'not an email'] + $valid, ['validate' => 'install'])->getErrors();
        $this->assertArrayHasKey('e', $errors);

        $errors = $this->Profiles->newEntity(['repeat_pass' => 'different'] + $valid, ['validate' => 'install'])
            ->getErrors();
        $this->assertArrayHasKey('repeat_pass', $errors);

        $errors = $this->Profiles->newEntity(['p' => 'abc', 'repeat_pass' => 'abc'] + $valid, ['validate' => 'install'])
            ->getErrors();
        $this->assertArrayHasKey('p', $errors, 'Password needs at least 4 characters');

        $errors = $this->Profiles->newEntity(['repeat_pass' => ''] + $valid, ['validate' => 'install'])->getErrors();
        $this->assertArrayHasKey('repeat_pass', $errors);
    }

    /**
     * Reset password validation
     *
     * @return void
     */
    public function testResetPasswordValidation()
    {
        $profile = $this->Profiles->get(1);

        $this->Profiles->patchEntity($profile, ['p' => 'newsecret', 'repeat_pass' => 'newsecret'], [
            'validate' => 'resetPassword',
        ]);
        $this->assertEmpty($profile->getErrors());
        $this->assertTrue((new DefaultPasswordHasher())->check('newsecret', $profile->p));

        $profile = $this->Profiles->get(1);
        $this->Profiles->patchEntity($profile, ['p' => 'newsecret', 'repeat_pass' => 'other'], [
            'validate' => 'resetPassword',
        ]);
        $this->assertArrayHasKey('repeat_pass', $profile->getErrors());

        $profile = $this->Profiles->get(1);
        $this->Profiles->patchEntity($profile, ['p' => 'abc', 'repeat_pass' => 'abc'], ['validate' => 'resetPassword']);
        $this->assertArrayHasKey('p', $profile->getErrors());
    }

    /**
     * Reset mail contains a link with the stored reset key
     *
     * @return void
     */
    public function testSendResetEmail()
    {
        Configure::write('from', ['name' => 'Famiree', 'email' => 'info@famiree.test']);
        $user = $this->Profiles->get(1);

        $this->assertTrue($this->Profiles->sendResetEmail($user));

        $key = $this->Profiles->get(1)->rst;
        $this->assertNotEmpty($key);
        $this->assertMailCount(1);
        $this->assertMailSentTo('donald.duck@test.com');
        $this->assertMailSentFrom('info@famiree.test');
        $this->assertMailContainsText('/users/change-password/' . $key);
    }

    /**
     * Each reset request replaces the previous key
     *
     * @return void
     */
    public function testResetKeyIsRegenerated()
    {
        Configure::write('from', ['name' => 'Famiree', 'email' => 'info@famiree.test']);

        $this->Profiles->sendResetEmail($this->Profiles->get(1));
        $first = $this->Profiles->get(1)->rst;
        $this->Profiles->sendResetEmail($this->Profiles->get(1));
        $second = $this->Profiles->get(1)->rst;

        $this->assertNotSame($first, $second);
    }
}
