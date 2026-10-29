<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Test\TestCase\FamilyBuilderTrait;
use Cake\Cache\Cache;
use Cake\TestSuite\TestCase;

/**
 * Family relations and tree of App\Model\Table\ProfilesTable
 */
class ProfilesFamilyTest extends TestCase
{
    use FamilyBuilderTrait;

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

        Cache::clear('_cake_core_');
        Cache::delete('tree-units');
        $this->Profiles = $this->fetchTable('Profiles');
        $this->buildFamily();
    }

    /**
     * Helper: returns sorted profile ids
     *
     * @param iterable $profiles Profile entities.
     * @return array<int>
     */
    private function idsOf(iterable $profiles): array
    {
        $ret = [];
        foreach ($profiles as $profile) {
            $ret[] = (int)$profile->id;
        }
        sort($ret);

        return $ret;
    }

    /**
     * Parents of a child
     *
     * @return void
     */
    public function testFamilyParents()
    {
        $parents = $this->Profiles->family($this->ids['huey'], 'parents');

        $this->assertSame([$this->ids['donald'], $this->ids['daisy']], $this->idsOf($parents));
        // ordered by gender descending: male first
        $this->assertSame('m', $parents[0]->g);
        $this->assertSame('f', $parents[1]->g);
    }

    /**
     * Associative parents are keyed by profile id
     *
     * @return void
     */
    public function testFamilyParentsAssoc()
    {
        $parents = $this->Profiles->family($this->ids['huey'], 'parents', true);

        $this->assertEqualsCanonicalizing([$this->ids['donald'], $this->ids['daisy']], array_keys($parents));
    }

    /**
     * Siblings do not contain the profile itself
     *
     * @return void
     */
    public function testFamilySiblings()
    {
        $siblings = $this->Profiles->family($this->ids['huey'], 'siblings');

        $this->assertSame([$this->ids['dewey']], $this->idsOf($siblings));
    }

    /**
     * A profile that is not a child anywhere has neither parents nor siblings
     *
     * @return void
     */
    public function testFamilyWithoutParents()
    {
        $family = $this->Profiles->family($this->ids['daisy']);

        $this->assertArrayNotHasKey('parents', $family);
        $this->assertArrayNotHasKey('siblings', $family);
    }

    /**
     * Marriages of a profile include spouse and children of each union
     *
     * @return void
     */
    public function testFamilyMarriages()
    {
        $marriages = $this->Profiles->family($this->ids['donald'], 'marriages');

        $this->assertCount(2, $marriages);

        $spouses = [];
        $children = [];
        foreach ($marriages as $marriage) {
            $spouses[] = (int)$marriage['spouse']->id;
            $children = array_merge($children, $this->idsOf($marriage['children']));
        }
        sort($spouses);
        sort($children);

        $this->assertSame([$this->ids['daisy'], $this->ids['gladstone']], $spouses);
        $this->assertSame([$this->ids['huey'], $this->ids['dewey'], $this->ids['louie']], $children);
    }

    /**
     * Full family has all sections
     *
     * @return void
     */
    public function testFamilyAll()
    {
        $family = $this->Profiles->family($this->ids['huey']);

        $this->assertArrayHasKey('parents', $family);
        $this->assertArrayHasKey('siblings', $family);
        $this->assertArrayHasKey('marriages', $family);
    }

    /**
     * Tree of a profile with parents contains parents, siblings and half siblings
     *
     * @return void
     */
    public function testTreeOfChild()
    {
        $tree = $this->Profiles->tree($this->ids['huey']);

        $this->assertArrayHasKey('u', $tree);
        $this->assertArrayHasKey('p', $tree);

        foreach (['donald', 'daisy', 'huey', 'dewey', 'gladstone', 'louie'] as $name) {
            $this->assertArrayHasKey($this->ids[$name], $tree['p'], "$name is missing in the tree");
        }

        // requested profile is the center of the tree
        $this->assertEquals(0, $tree['p'][$this->ids['huey']]['Profile']['x']);
        $this->assertEquals(0, $tree['p'][$this->ids['huey']]['Profile']['y']);

        // both unions are present and parents are drawn above (higher y) their children
        $this->assertArrayHasKey(1, $tree['u']);
        $this->assertArrayHasKey($this->ids['union2'], $tree['u']);
        $this->assertGreaterThan(
            $tree['p'][$this->ids['huey']]['Profile']['y'],
            $tree['p'][$this->ids['donald']]['Profile']['y'],
        );
    }

    /**
     * Tree of a profile without parents
     *
     * @return void
     */
    public function testTreeOfParent()
    {
        $tree = $this->Profiles->tree($this->ids['daisy']);

        $this->assertArrayHasKey($this->ids['daisy'], $tree['p']);
        $this->assertArrayHasKey($this->ids['huey'], $tree['p']);
        $this->assertArrayHasKey($this->ids['dewey'], $tree['p']);
        $this->assertEquals(0, $tree['p'][$this->ids['daisy']]['Profile']['x']);
    }

    /**
     * Tree of a profile that is in no union
     *
     * @return void
     */
    public function testTreeOfLoneProfile()
    {
        $lone = $this->Profiles->saveOrFail($this->Profiles->newEntity(['fn' => 'Lone', 'ln' => 'Wolf', 'g' => 'm']));

        $tree = $this->Profiles->tree($lone->id);

        $this->assertSame([$lone->id], array_keys($tree['p']));
        $this->assertEmpty($tree['u']);
    }

    /**
     * Limiting depth does not break the tree
     *
     * @return void
     */
    public function testTreeDepthZero()
    {
        $tree = $this->Profiles->tree($this->ids['huey'], 0);

        $this->assertArrayHasKey($this->ids['huey'], $tree['p']);
        $this->assertArrayHasKey($this->ids['donald'], $tree['p']);
    }

    /**
     * Deleting a profile removes its units and the union that stays without members
     *
     * @return void
     */
    public function testDeleteProfileRemovesUnitsAndEmptyUnion()
    {
        $unions = $this->fetchTable('Unions');
        $units = $this->fetchTable('Units');

        // union 2 has Donald, Gladstone (parents) and Louie (child)
        $louie = $this->Profiles->get($this->ids['louie']);
        $this->assertTrue((bool)$this->Profiles->delete($louie));
        $this->assertTrue($unions->exists(['id' => $this->ids['union2']]), 'Union with two parents stays');

        $gladstone = $this->Profiles->get($this->ids['gladstone']);
        $this->assertTrue((bool)$this->Profiles->delete($gladstone));

        $this->assertFalse($unions->exists(['id' => $this->ids['union2']]), 'Union with a single member is removed');
        $this->assertSame(0, $units->find()->where(['union_id' => $this->ids['union2']])->count());
        $this->assertTrue($unions->exists(['id' => 1]), 'Other unions are not touched');
    }

    /**
     * Gender counts always contain both genders
     *
     * @return void
     */
    public function testCountGenders()
    {
        $counts = $this->Profiles->countGenders();

        $this->assertEquals(4, $counts['m'] ?? 0);
        $this->assertEquals(2, $counts['f']);

        $this->Profiles->deleteAll(['g' => 'f']);
        $counts = $this->Profiles->countGenders();
        $this->assertSame(0, $counts['f']);
    }

    /**
     * Search finder
     *
     * @return void
     */
    public function testFindSearch()
    {
        $all = $this->Profiles->find('search')->all();
        $this->assertCount(6, $all);
        // ordered by last name, first name
        $this->assertSame('Daisy Duck', $all->first()->d_n);

        $result = $this->Profiles->find('search', criterio: 'gand')->all();
        $this->assertSame([$this->ids['gladstone']], $this->idsOf($result));

        // a single character is not searched for
        $this->assertCount(6, $this->Profiles->find('search', criterio: 'g')->all());
    }

    /**
     * Counters of attachments and memories are recalculated
     *
     * @return void
     */
    public function testUpdateCounters()
    {
        $this->fetchTable('Profiles')->updateAll(['cn_med' => 9, 'cn_mem' => 9], ['id' => 1]);

        $this->Profiles->updateAttachmentCount([1]);
        $this->Profiles->updateMemoryCount([1]);

        $profile = $this->Profiles->get(1);
        $this->assertSame(0, (int)$profile->cn_med);
        $this->assertSame(0, (int)$profile->cn_mem);
    }
}
