<?php
declare(strict_types=1);

require dirname(dirname(__DIR__)) . '/config/bootstrap.php';

use App\Core\Configure;
use App\Lib\ApnsPush;
use App\Model\Table\DevicesTable;
use App\Model\Table\SettingsTable;

$devicesTable = new DevicesTable();

$id = $argv[1] ?? '7f2b5';
if (empty($id)) {
    die('Specify Device Id!' . PHP_EOL);
}
$device = $devicesTable->get($id);

if (empty($device)) {
    die('No Device!!');
}

$SettingsTable = new SettingsTable();
$doorbellName = $SettingsTable->get('name', Configure::read('App.defaultName'));

echo $device->token . PHP_EOL;

ApnsPush::sendNotification('', $device->token, 'DoorbellName');
