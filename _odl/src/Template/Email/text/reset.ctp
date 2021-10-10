<?php
use Cake\Core\Configure;
use Cake\Routing\Router;

$reset_url = Router::url(array(
	'prefix' => null,
	'plugin' => null,
	'controller' => 'Users',
	'action' =>	'change_password',
	$reset_key), true
);

echo __('Forgot your password?') . PHP_EOL;
echo PHP_EOL;
echo __('You\'ve received this mail from password reset request on Famiree') . PHP_EOL;
echo __('Please follow this url to change your password:') . PHP_EOL;
echo '   ' . $reset_url . PHP_EOL;
echo PHP_EOL;
echo PHP_EOL;
echo __('Please discard this email if you did not want to change your password.') . PHP_EOL;
echo PHP_EOL;
echo PHP_EOL;
echo __('Best Regards.') . PHP_EOL;
