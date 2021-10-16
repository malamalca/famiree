<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Core\DB;
use App\Core\Table;
use App\Model\Entity\Device;

class PostsTable extends Table
{
    public $entityName = '\App\Model\Entity\Post';
    public $tableName = 'posts';

}
