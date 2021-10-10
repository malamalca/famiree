<?php
namespace App\Test\TestCase\Lib;

use App\Lib\GedImport;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Lib\GedImport Test Case
 */
class GedImportTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Logs',
        'app.Profiles',
        'app.Unions',
        'app.Units'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * Test parseNam method
     *
     * @return void
     */
    public function testParseNam()
    {
        $ret = GedImport::parseNam('William /Shakespeare/');

        $this->assertFalse(empty($ret['first']));
        $this->assertFalse(empty($ret['last']));
        $this->assertTrue(empty($ret['third']));
        $this->assertEquals('William', $ret['first']);
        $this->assertEquals('Shakespeare', $ret['last']);

        $ret = GedImport::parseNam('  William     /Shakespeare/   ');

        $this->assertFalse(empty($ret['first']));
        $this->assertFalse(empty($ret['last']));
        $this->assertTrue(empty($ret['third']));
        $this->assertEquals('William', $ret['first']);
        $this->assertEquals('Shakespeare', $ret['last']);

        $ret = GedImport::parseNam('William //');

        $this->assertFalse(empty($ret['first']));
        $this->assertTrue(empty($ret['last']));
        $this->assertTrue(empty($ret['third']));
        $this->assertEquals('William', $ret['first']);

        $ret = GedImport::parseNam('    William     //   ');

        $this->assertFalse(empty($ret['first']));
        $this->assertTrue(empty($ret['last']));
        $this->assertTrue(empty($ret['third']));
        $this->assertEquals('William', $ret['first']);

        $ret = GedImport::parseNam('/Shakespeare/');

        $this->assertTrue(empty($ret['first']));
        $this->assertFalse(empty($ret['last']));
        $this->assertTrue(empty($ret['third']));
        $this->assertEquals('Shakespeare', $ret['last']);

        $ret = GedImport::parseNam('   /Shakespeare/    ');

        $this->assertTrue(empty($ret['first']));
        $this->assertFalse(empty($ret['last']));
        $this->assertTrue(empty($ret['third']));
        $this->assertEquals('Shakespeare', $ret['last']);

        $ret = GedImport::parseNam('William /Shakespeare/ III.');

        $this->assertFalse(empty($ret['first']));
        $this->assertFalse(empty($ret['last']));
        $this->assertFalse(empty($ret['third']));
        $this->assertEquals('William', $ret['first']);
        $this->assertEquals('Shakespeare', $ret['last']);
        $this->assertEquals('III.', $ret['third']);
    }
}
