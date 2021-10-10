<?php
declare(strict_types=1);

require dirname(dirname(__DIR__)) . '/config/bootstrap.php';

use App\Core\Configure;
use App\Core\Log;
use App\Lib\ApnsPush;
use App\Lib\Mqtt;
use App\Model\Table\DevicesTable;
use App\Model\Table\SettingsTable;
use PiPHP\GPIO\GPIO;
use PiPHP\GPIO\Pin\InputPinInterface;

// Create a GPIO object
$gpio = new GPIO();

// Retrieve pin 18 and configure it as an input pin
$pin = $gpio->getInputPin(Configure::read('Doorbel.gpio_pin'));

// Configure interrupts for both rising and falling edges
$pin->setEdge(InputPinInterface::EDGE_RISING);

// Create an interrupt watcher
$interruptWatcher = $gpio->createWatcher();

// Register a callback to be triggered on pin interrupts
$interruptWatcher->register($pin, function (InputPinInterface $pin, $value) {
    Log::info('Pin ' . $pin->getNumber() . ' changed to: ' . $value);

    $context = [
        'http' => ['method' => 'GET' ],
        'ssl' => [ 'verify_peer' => false, 'verify_peer_name' => false, 'allow_self_signed' => true],
    ];
    $imageData = file_get_contents(Configure::read('Doorbel.snapshot_url'), false, stream_context_create($context));

    if ($imageData) {
        file_put_contents(PHOTOS . strftime('%Y%m%d%H%M%S', time()) . '.jpg', $imageData);
    }

    if ($value == 1) {
        // play ringback in background
        exec('aplay -q ' . Configure::read('Doorbel.sound_file') . '> /dev/null 2>/dev/null &');

        $SettingsTable = new SettingsTable();
        $doorbellName = $SettingsTable->get('name')->value ?? Configure::read('App.defaultName');

        // mqtt
        Mqtt::publish();

        // fetch devices
        $devicesTable = new DevicesTable();
        $devices = $devicesTable->getDevices();

        ApnsPush::sendNotification($devices, $doorbellName);
    }

    // Returning false will make the watcher return false immediately
    return true;
});

// Watch for interrupts, timeout after 5000ms (5 seconds)
while ($interruptWatcher->watch(5000));
