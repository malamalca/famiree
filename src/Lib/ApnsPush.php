<?php
declare(strict_types=1);

namespace App\Lib;

use App\Core\Configure;
use App\Core\Log;
use Pushok\AuthProvider;
use Pushok\Client;
use Pushok\Notification;
use Pushok\Payload;
use Pushok\Payload\Alert;
use Ramsey\Uuid\Uuid;

class ApnsPush
{
    /**
     * Send voip push notification to phones (devices)
     *
     * @param array $devices Array of receiving devices
     * @param string $doorbellName Doorbels name that will appear on phones call notification dialog
     * @return bool
     */
    public static function sendNotification($devices, $doorbellName)
    {
        $options = [
            'key_id' => Configure::read('Apns.key_id'), // The Key ID obtained from Apple developer account
            'team_id' => Configure::read('Apns.team_id'), // The Team ID obtained from Apple developer account
            'app_bundle_id' => Configure::read('Apns.app_bundle_id'), // The bundle ID for app obtained from Apple developer account
            'private_key_path' => Configure::read('Apns.private_key_path'), // Path to private key
            'private_key_secret' => Configure::read('Apns.private_key_secret'), // Private key secret
        ];

        // Be aware of thing that Token will stale after one hour, so you should generate it again.
        // Can be useful when trying to send pushes during long-running tasks
        $authProvider = AuthProvider\Token::create($options);

        $alert = Alert::create()->setTitle('Throbell Ringing');
        $alert = $alert->setBody('Someone is at the Door!');

        $payload = Payload::create()->setAlert($alert);

        //set notification sound to default
        $payload->setSound('default');

        //add custom value to your notification, needs to be customized
        $payload->setCustomValue('UUID', Uuid::uuid4()->toString());
       // $payload->setCustomValue('id', $device->id);                                // pair code
        $payload->setCustomValue('title', $doorbellName);

        $payload->setPushType('voip');

        $notifications = [];
        foreach ((array)$devices as $device) {
            $payload->setCustomValue('id', $device->id);
            $notifications[] = new Notification($payload, $device->token);
        }

        // If you have issues with ssl-verification, you can temporarily disable it. Please see attached note.
        // Disable ssl verification
        // $client = new Client($authProvider, $production = false, [CURLOPT_SSL_VERIFYPEER=>false] );
        $client = new Client($authProvider, $production = false);
        $client->addNotifications($notifications);

        $responses = $client->push(); // returns an array of ApnsResponseInterface (one Response per Notification)

        foreach ($responses as $response) {
            Log::info('Voip Push', [
                'token' => $response->getDeviceToken(),
                'apns_id' => $response->getApnsId(),
                // Status code. E.g. 200 (Success), 410 (The device token is no longer active for the topic.)
                'status' => $response->getStatusCode(),
                // E.g. Unregistered
                'error_reason' => $response->getErrorReason(),
                // E.g. The device token is no longer active for the topic.
                'error_phrase' => $response->getReasonPhrase(),
                // E.g. The device token is inactive for the specified topic.
                'error_descript' => $response->getErrorDescription(),
                '410_timestamp' => $response->get410Timestamp(),
            ]);
        }

        return true;
    }
}
