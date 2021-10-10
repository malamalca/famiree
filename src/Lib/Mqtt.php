<?php
declare(strict_types=1);

namespace App\Lib;

use App\Core\Configure;
use App\Model\Table\SettingsTable;
use Bluerhinos\phpMQTT;

class Mqtt
{
    /**
     * Publish message to MQTT server
     *
     * @param array $message Publish message
     * @return bool
     */
    public static function publish($message = null)
    {
        $SettingsTable = new SettingsTable();

        $options = [
            'server' => $SettingsTable->get('mqtt_server')->value,
            'port' => $SettingsTable->get('mqtt_port', Configure::read('Mqtt.port'))->value,
            'username' => $SettingsTable->get('mqtt_username')->value,
            'password' => $SettingsTable->get('mqtt_password')->value,
            'mdns_name' => $SettingsTable->get('mdns_name', Configure::read('Mqtt.mdns_name'))->value,
            'topic' => Configure::read('Mqtt.topic'),
            'message_ring' => Configure::read('Mqtt.message_ring'),
        ];

        if (empty($options['server'])) {
            return false;
        }

        $mqtt = new phpMQTT($options['server'], $options['port'] ?? 1883, $options['mdns_name']);

        if ($mqtt->connect(true, null, $options['username'], $options['password'])) {
            $mqtt->publish($options['topic'], $message ?? $options['message_ring'], 0, false);
            $mqtt->close();

            return true;
        } else {
            return false;
        }
    }
}
