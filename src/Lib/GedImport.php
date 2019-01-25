<?php
namespace App\Lib;

use App\Model\Entity\Profile;
use App\Model\Entity\Union;
use App\Model\Entity\Unit;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use PhpGedcom\Parser;

class GedImport
{

    /**
     * Import ged from a file
     *
     * @param string $filename Ged filename.
     * @return bool
     */
    public static function fromFile($filename)
    {
        if (file_exists($filename)) {
            $parser = new Parser;
            $gedcom = $parser->parse($filename);
        } else {
            throw new NotFoundException(__('Get file does not exist'));
        }

        $unions = [];
        $families = $gedcom->getFam();
        foreach ($families as $famId => $fam) {
            $unions[$famId] = new Union();
            /** @var \PhpGedcom\Record\Fam\Even $marriage */
            $marriage = $fam->getEven('MARR');
            if (!empty($marriage)) {
                /** @var \PhpGedcom\Record\Indi\Even\Plac $plac */
                $plac = $marriage->getPlac();
                if (!empty($plac)) {
                    $unions[$famId]->loc = $plac->getPlac();
                }
                $marrDate = $marriage->getDate();
                if (!empty($marrDate)) {
                    $unions[$famId]->dom_y = (string)$marrDate->getYear();
                    $unions[$famId]->dom_m = $marrDate->getMonth();
                    $unions[$famId]->dom_d = $marrDate->getDay();
                }
            }
        }

        foreach ($unions as $famId => $u) {
            $unions[$famId] = TableRegistry::get('Unions')->save($u);
        }

        $profiles = [];
        $individuals = $gedcom->getIndi();
        foreach ($individuals as $indiId => $indi) {
            $profile = new Profile();
            $name = $indi->getName();
            if (!empty($name[0])) {
                $profile->d_n = $name[0]->getName();
                $names = self::parseNam($name[0]->getName());
                if (!empty($names['first'])) {
                    $profile->fn = $names['first'];
                }
                if (!empty($names['last'])) {
                    $profile->ln = $names['last'];
                }
                if ($firstName = $name[0]->getGivn()) {
                    $profile->fn = $firstName;
                }
                if ($lastName = $name[0]->getSurn()) {
                    $profile->ln = $lastName;
                }
            }
            $profile->g = strtolower($indi->getSex());

            $dob = self::getEventDate($indi->getAllEven(), 'BIRT');
            if (!empty($dob)) {
                $profile->dob_y = (string)$dob->getYear();
                $profile->dob_m = $dob->getMonth();
                $profile->dob_d = $dob->getDay();
            }

            $dod = self::getEventDate($indi->getAllEven(), 'DEAT');
            if (!empty($dod)) {
                $profile->dod_y = (string)$dod->getYear();
                $profile->dod_m = $dod->getMonth();
                $profile->dod_d = $dod->getDay();
            }

            $profile->units = [];

            $famc = $indi->getFamc();
            if (!empty($famc[0]) && !empty($unions[$famc[0]->getFamc()])) {
                $unit = new Unit();
                $unit->profile_id = null;
                $unit->kind = 'c';
                $unit->union_id = $unions[$famc[0]->getFamc()]->id;
                $profile->units[] = $unit;
            }

            $fams = $indi->getFams();
            if (!empty($fams[0]) && !empty($unions[$fams[0]->getFams()])) {
                $unit = new Unit();
                $unit->profile_id = null;
                $unit->kind = 'p';
                $unit->union_id = $unions[$fams[0]->getFams()]->id;
                $profile->units[] = $unit;
            }

            $profiles[] = $profile;
        }

        foreach ($profiles as $k => $p) {
            $result = TableRegistry::get('Profiles')->save($p, ['associated' => ['Units']]);
        }

        return true;
    }

    /**
     * Extract event date with given index from events array
     *
     * @param array $events Events array 'EVEN'
     * @param string $name  Event name (eg 'DEAT', 'BURI', 'MARR', 'BIRT', 'CHR',..)
     * @return null|\PhpGedcom\Record\Date
     */
    public static function getEventDate($events, $name)
    {
        $ret = null;
        if (isset($events[$name])) {
            return $events[$name]->getDate();
        }

        return $ret;
    }

    /**
     * Parse GEDCOM nam tag
     *
     * @param string $nam Value of NAM tag
     * @return array
     */
    public static function parseNam($nam)
    {
        $ret = ['first' => null, 'last' => null, 'add' => null];

        $first = trim(substr($nam, 0, strpos($nam, '/')));
        if (!empty($first)) {
            $ret['first'] = $first;
        }

        $last = trim(substr($nam, strpos($nam, '/') + 1, strrpos($nam, '/') - strpos($nam, '/') - 1));
        if (!empty($last)) {
            $ret['last'] = $last;
        }

        $third = trim(substr($nam, strrpos($nam, '/') + 1));
        if (!empty($third)) {
            $ret['third'] = $third;
        }

        return $ret;
    }
}
