<?php
declare(strict_types=1);

namespace App\Test\TestCase;

/**
 * Builds a small family on top of the Profiles, Units and Unions fixtures.
 *
 * Fixtures contain union 1 with Donald Duck (id 1, parent) and Huey Duck (id 2, child). This trait adds:
 *
 *  - Daisy Duck, the second parent of union 1
 *  - Dewey Duck, a second child of union 1 (sibling of Huey)
 *  - Union 2 with Donald as parent, Gladstone Gander as the other parent and Louie Duck as their child
 *
 * It requires the `app.Profiles`, `app.Units` and `app.Unions` fixtures and `LocatorAwareTrait` (any TestCase).
 */
trait FamilyBuilderTrait
{
    /**
     * Ids of created profiles
     *
     * @var array<string, int>
     */
    protected array $ids = [];

    /**
     * Builds the family
     *
     * @return void
     */
    protected function buildFamily(): void
    {
        $profiles = $this->fetchTable('Profiles');
        $units = $this->fetchTable('Units');
        $unions = $this->fetchTable('Unions');

        $this->ids['donald'] = 1;
        $this->ids['huey'] = 2;

        foreach (
            [
                'daisy' => ['fn' => 'Daisy', 'ln' => 'Duck', 'g' => 'f', 'dob_y' => 1936],
                'dewey' => ['fn' => 'Dewey', 'ln' => 'Duck', 'g' => 'm', 'dob_y' => 1960],
                'gladstone' => ['fn' => 'Gladstone', 'ln' => 'Gander', 'g' => 'f', 'dob_y' => 1935],
                'louie' => ['fn' => 'Louie', 'ln' => 'Duck', 'g' => 'm', 'dob_y' => 1962],
            ] as $key => $data
        ) {
            $profile = $profiles->newEntity($data + ['l' => true]);
            $this->ids[$key] = (int)$profiles->saveOrFail($profile)->id;
        }

        $union2 = $unions->saveOrFail($unions->newEntity(['t' => 'L']));
        $this->ids['union2'] = (int)$union2->id;

        foreach (
            [
                [1, $this->ids['daisy'], 'p', 2],
                [1, $this->ids['dewey'], 'c', 2],
                [$this->ids['union2'], 1, 'p', 1],
                [$this->ids['union2'], $this->ids['gladstone'], 'p', 1],
                [$this->ids['union2'], $this->ids['louie'], 'c', 1],
            ] as [$unionId, $profileId, $kind, $sortOrder]
        ) {
            $units->saveOrFail($units->newEntity([
                'union_id' => $unionId,
                'profile_id' => $profileId,
                'kind' => $kind,
                'sort_order' => $sortOrder,
            ]));
        }
    }
}
