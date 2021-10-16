<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Core\DB;
use App\Core\Table;
use App\Model\Entity\Device;

class ProfilesTable extends Table
{
    public $entityName = '\App\Model\Entity\Profile';
    public $tableName = 'profiles';

    /**
     * Find gender counts
     *
     * @return array Counts
     */
    public function countGenders()
    {
        $ret = ['f' => 0, 'm' => 0];

        $sql = 'SELECT g, COUNT(id) as count FROM profiles GROUP BY g';

        $pdo = DB::getInstance()->connect();
        $data = $pdo->query($sql);
        foreach ($data as $row) {
            $ret[$row['g']] = $row['count'];
        }

        return $ret;
    }

    /**
     * Fetch profiles with concatenated date of birth, ordered by dob
     *
     * @return \Cake\ORM\ResultSet
     */
    public function fetchForBirthdays()
    {
        //Cache::delete('Profiles.birthdays');
        $ret = Cache::remember('Profiles.birthdays', function () {
            $q = $this->find();
            $fieldExpr = $q->newExpr()->add('DATE_ADD(DATE_ADD(MAKEDATE(dob_y, 1), INTERVAL (dob_m)-1 MONTH), INTERVAL (dob_d)-1 DAY)');
            $diffExpr = $q->newExpr()->add('DATEDIFF(DATE_ADD( DATE_ADD( MAKEDATE(YEAR(CURDATE()), 1), INTERVAL (dob_m)-1 MONTH ), INTERVAL (dob_d)-1 DAY ), CURDATE() )');
            $dates = $q
                ->select($this)
                ->select(['dob' => $fieldExpr])
                ->select(['diff' => $diffExpr])
                ->where(['l' => true])
                ->andWhere(function (QueryExpression $whereExpr) use ($fieldExpr) {
                    return $whereExpr->isNotNull($fieldExpr);
                })
                ->andWhere(function (QueryExpression $andWhereExpr) use ($diffExpr) {
                    return $andWhereExpr->gte($diffExpr, 0, 'integer');
                })
                ->order(['diff'])
                ->limit(20)
                ->all();

            return $dates;
        });

        return $ret;
    }
}
