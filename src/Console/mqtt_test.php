<?php
declare(strict_types=1);

require dirname(dirname(__DIR__)) . '/config/bootstrap.php';

use App\Lib\Mqtt;

$ret = Mqtt::publish();

var_dump($ret);
