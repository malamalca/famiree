<?php
declare(strict_types=1);

namespace App\Console;

if (!defined('STDIN')) {
    define('STDIN', (int)fopen('php://stdin', 'r'));
}

use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Security;
use Composer\IO\IOInterface;
use Composer\Script\Event;
use Exception;
use Migrations\Migrations;

/**
 * Provides installation hooks for when this application is installed through
 * composer. Customize this class to suit your needs.
 *
 * @psalm-suppress UnusedClass
 */
class Installer
{
    /**
     * An array of directories to be made writable
     */
    public const WRITABLE_DIRS = [
        'logs',
        'tmp',
        'tmp/cache',
        'tmp/cache/models',
        'tmp/cache/persistent',
        'tmp/cache/views',
        'tmp/sessions',
        'tmp/tests',
        'uploads',
        'webroot/img/thumbs',
        'config/Seeds',
    ];

    /**
     * Does some routine installation tasks so people don't have to.
     *
     * @param \Composer\Script\Event $event The composer event object.
     * @throws \Exception Exception raised by validator.
     * @return void
     */
    public static function createUser(Event $event): void
    {
        $io = $event->getIO();
        $rootDir = dirname(dirname(__DIR__));

        if (file_exists($rootDir . '/config/app_local.php')) {
            require $rootDir . '/vendor/autoload.php';
            require $rootDir . '/config/paths.php';

            Configure::config('default', new PhpConfig());
            Configure::load('app', 'default', false);
            Configure::load('app_local', 'default');

            ConnectionManager::setConfig('default', Configure::read('Datasources.default'));

            static::createAdminUser($io, 'default');
        }
    }

    /**
     * Init database
     *
     * @param \Composer\Script\Event $event The composer event object.
     * @throws \Exception Exception raised by validator.
     * @return void
     */
    public static function postInstall(Event $event): void
    {
        $io = $event->getIO();

        $rootDir = dirname(dirname(__DIR__));

        static::createAppLocalConfig($rootDir, $io);
        static::createWritableDirectories($rootDir, $io);

        static::setFolderPermissions($rootDir, $io);
        static::setSecuritySalt($rootDir, $io);

        static::setCookieKeyInFile($rootDir, $io);

        $connected = static::setDatabase($rootDir, $io);

        if ($connected) {
            static::executeMigrations($rootDir, $io);
            static::createAdminUser($io);
        }

        $class = 'Cake\Codeception\Console\Installer';
        if (class_exists($class)) {
            $class::customizeCodeceptionBinary($event);
        }
    }

    /**
     * Does some routine UPDATE tasks so people don't have to.
     *
     * @param \Composer\Script\Event $event The composer event object.
     * @throws \Exception Exception raised by validator.
     * @return void
     */
    public static function postUpdate(Event $event): void
    {
        $io = $event->getIO();
        $rootDir = dirname(dirname(__DIR__));

        if (file_exists($rootDir . '/config/app_local.php')) {
            require $rootDir . '/vendor/autoload.php';
            require $rootDir . '/config/paths.php';

            Configure::config('default', new PhpConfig());
            Configure::load('app', 'default', false);
            Configure::load('app_local', 'default');

            ConnectionManager::setConfig('default', Configure::read('Datasources.default'));

            static::executeMigrations($rootDir, $io, 'default');
        }

        $class = 'Cake\Codeception\Console\Installer';
        if (class_exists($class)) {
            $class::customizeCodeceptionBinary($event);
        }
    }

    /**
     * Create config/app_local.php file if it does not exist.
     *
     * @param string $dir The application's root directory.
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @return void
     */
    public static function createAppLocalConfig(string $dir, IOInterface $io): void
    {
        $appLocalConfig = $dir . '/config/app_local.php';
        $appLocalConfigTemplate = $dir . '/config/app_local.example.php';
        if (!file_exists($appLocalConfig)) {
            copy($appLocalConfigTemplate, $appLocalConfig);
            $io->write('Created `config/app_local.php` file');
        }
    }

    /**
     * Create the `logs` and `tmp` directories.
     *
     * @param string $dir The application's root directory.
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @return void
     */
    public static function createWritableDirectories(string $dir, IOInterface $io): void
    {
        foreach (static::WRITABLE_DIRS as $path) {
            $path = $dir . '/' . $path;
            if (!file_exists($path)) {
                mkdir($path);
                $io->write('Created `' . $path . '` directory');
            }
        }
    }

    /**
     * Set globally writable permissions on the "tmp" and "logs" directory.
     *
     * This is not the most secure default, but it gets people up and running quickly.
     *
     * @param string $dir The application's root directory.
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @return void
     */
    public static function setFolderPermissions(string $dir, IOInterface $io): void
    {
        // ask if the permissions should be changed
        if ($io->isInteractive()) {
            $validator = function ($arg) {
                if (in_array($arg, ['Y', 'y', 'N', 'n'])) {
                    return $arg;
                }
                throw new Exception('This is not a valid answer. Please choose Y or n.');
            };
            $setFolderPermissions = $io->askAndValidate(
                '<info>Set Folder Permissions ? (Default to Y)</info> [<comment>Y,n</comment>]? ',
                $validator,
                10,
                'Y',
            );

            if (in_array($setFolderPermissions, ['n', 'N'])) {
                return;
            }
        }

        // Change the permissions on a path and output the results.
        $changePerms = function ($path) use ($io): void {
            $currentPerms = fileperms($path) & 0777;
            $worldWritable = $currentPerms | 0007;
            if ($worldWritable == $currentPerms) {
                return;
            }

            $res = chmod($path, $worldWritable);
            if ($res) {
                $io->write('Permissions set on ' . $path);
            } else {
                $io->write('Failed to set permissions on ' . $path);
            }
        };

        $walker = function ($dir) use (&$walker, $changePerms): void {
            $files = array_diff(scandir($dir), ['.', '..']);
            foreach ($files as $file) {
                $path = $dir . '/' . $file;

                if (!is_dir($path)) {
                    continue;
                }

                $changePerms($path);
                $walker($path);
            }
        };

        $walker($dir . '/tmp');
        $changePerms($dir . '/tmp');
        $changePerms($dir . '/logs');
    }

    /**
     * Set the security.salt value in the application's config file.
     *
     * @param string $dir The application's root directory.
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @return void
     */
    public static function setSecuritySalt(string $dir, IOInterface $io): void
    {
        $newKey = hash('sha256', Security::randomBytes(64));
        static::setSecuritySaltInFile($dir, $io, $newKey, 'app_local.php');
    }

    /**
     * Set the security.salt value in a given file
     *
     * @param string $dir The application's root directory.
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @param string $newKey key to set in the file
     * @param string $file A path to a file relative to the application's root
     * @return void
     */
    public static function setSecuritySaltInFile(string $dir, IOInterface $io, string $newKey, string $file): void
    {
        $config = $dir . '/config/' . $file;
        $content = file_get_contents($config);

        $content = str_replace('__SALT__', $newKey, $content, $count);

        if ($count == 0) {
            $io->write('No Security.salt placeholder to replace.');

            return;
        }

        $result = file_put_contents($config, $content);
        if ($result) {
            $io->write('Updated Security.salt value in config/' . $file);

            return;
        }
        $io->write('Unable to update Security.salt value.');
    }

    /**
     * Set the APP_NAME value in a given file
     *
     * @param string $dir The application's root directory.
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @param string $appName app name to set in the file
     * @param string $file A path to a file relative to the application's root
     * @return void
     */
    public static function setAppNameInFile(string $dir, IOInterface $io, string $appName, string $file): void
    {
        $config = $dir . '/config/' . $file;
        $content = file_get_contents($config);
        $content = str_replace('__APP_NAME__', $appName, $content, $count);

        if ($count == 0) {
            $io->write('No __APP_NAME__ placeholder to replace.');

            return;
        }

        $result = file_put_contents($config, $content);
        if ($result) {
            $io->write('Updated __APP_NAME__ value in config/' . $file);

            return;
        }
        $io->write('Unable to update __APP_NAME__ value.');
    }

    /**
     * Set database properties
     *
     * @param string $dir The application's root directory.
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @return bool
     */
    public static function setDatabase(string $dir, IOInterface $io): bool
    {
        $io->write('ENTER DATABASE CONNECTION');

        $dbConnectSuccess = false;
        $numRetries = 0;

        while (!$dbConnectSuccess && $numRetries < 10) {
            $dbHost = $io->ask('<info>Enter database host ?</info> [<comment>localhost</comment>]? ', 'localhost');
            $dbName = $io->ask('<info>Enter database name ?</info> [<comment>famiree_new</comment>]? ', 'famiree_new');
            $dbUser = $io->ask('<info>Enter db user ?</info> ');
            $dbPassword = $io->ask('<info>Enter db password ?</info> ');

            $dbConnectSuccess = static::checkDbConnection($dbHost, $dbName, (string)$dbUser, (string)$dbPassword, $io);

            if ($dbConnectSuccess) {
                static::setDbConfigInFile(
                    $dbHost,
                    $dbName,
                    (string)$dbUser,
                    (string)$dbPassword,
                    $dir,
                    'app_local.php',
                    $io,
                );
            } else {
                $io->writeError('Cannot connect to mysql database. Please try again.');
            }

            $numRetries++;
        }

        return $dbConnectSuccess;
    }

    /**
     * Execute migrations to create tables.
     *
     * @param string $dir The application's root directory.
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @param string $connection Connection name
     * @return void
     */
    public static function executeMigrations(string $dir, IOInterface $io, string $connection = 'install'): void
    {
        if (!defined('ROOT')) {
            define('ROOT', $dir);
        }

        if (!defined('DS')) {
            define('DS', '/');
        }

        $migrations = new Migrations(['connection' => $connection]);
        $migrations->migrate();
    }

    /**
     * Create admin user.
     *
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @param string $connection Connection name
     * @return void
     */
    public static function createAdminUser(IOInterface $io, string $connection = 'install'): void
    {
        /** @var \Cake\Database\Connection $conn */
        $conn = ConnectionManager::get($connection);

        if ($conn && $conn->getDriver()->connect()) {
            /*$io->write('CREATE ADMIN USER');

            $adminName = $io->ask(
                '<info>Enter admin\'s display name ?</info> [<comment>Administrator</comment>]? ',
                'Administrator'
            );
            $adminEmail = $io->ask('<info>Enter admin\'s email ?</info> ');

            $adminUsername = $io->ask('<info>Enter admin\'s username ?</info> [<comment>admin</comment>]? ', 'admin');
            $adminPassword = $io->ask('<info>Enter admin\'s password ?</info> ');

            $companyTitle = $io->ask('<info>Enter Company Title ?</info> ');
            $companyMatNo = $io->ask('<info>Enter Company Assigned No. ?</info> ');
            $companyTaxNo = $io->ask('<info>Enter Company Tax No. ?</info> ');

            $adminPassword = (new DefaultPasswordHasher())->hash($adminPassword);

            $userId = Text::uuid();
            $companyId = Text::uuid();

            $conn->execute(
                'INSERT INTO users (id, company_id, name, email, username, passwd, privileges) VALUES ' .
                    '(:id, :company_id, :title, :email, :username, :pass, 2)',
                [
                    'id' => $userId,
                    'company_id' => $companyId,
                    'title' => $adminName,
                    'email' => $adminEmail,
                    'username' => $adminUsername,
                    'pass' => $adminPassword,
                ],
                [
                    'id' => 'string',
                    'company_id' => 'string',
                    'title' => 'string',
                    'email' => 'string',
                    'username' => 'string',
                    'pass' => 'string',
                ]
            );

            $conn->execute(
                'INSERT INTO contacts (id, owner_id, kind, title, mat_no, tax_no) VALUES ' .
                    '(:id, :owner_id, "C", :title, :mat_no, :tax_no)',
                [
                    'id' => $companyId,
                    'owner_id' => $companyId,
                    'title' => $companyTitle,
                    'mat_no' => $companyMatNo,
                    'tax_no' => $companyTaxNo,
                ],
                [
                    'id' => 'string',
                    'owner_id' => 'string',
                    'title' => 'string',
                    'mat_no' => 'string',
                    'tax_no' => 'string',
                ]
            );*/
        } else {
            $io->writeError('Cannot connect to mysql database to create admin user');
        }
    }

    /**
     * Try to connect to database
     *
     * @param string $dbHost Database host.
     * @param string $db Database name.
     * @param string $dbUser Mysql username.
     * @param string $dbPassword Mysql password.
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @return bool
     */
    public static function checkDbConnection(
        string $dbHost,
        string $db,
        string $dbUser,
        string $dbPassword,
        IOInterface $io,
    ): bool {
        try {
            ConnectionManager::drop('install');

            ConnectionManager::setConfig('install', [
                'className' => 'Cake\Database\Connection',
                'driver' => 'Cake\Database\Driver\Mysql',
                'persistent' => false,
                'host' => $dbHost,
                'username' => $dbUser,
                'password' => $dbPassword,
                'database' => $db,
                'timezone' => 'UTC',
                'flags' => [],
                'cacheMetadata' => true,
                'log' => false,
                'quoteIdentifiers' => true,
                'url' => null,
            ]);
            /** @var \Cake\Database\Connection $connection */
            $connection = ConnectionManager::get('install');
            $connection->getDriver()->connect();

            return $connection->getDriver()->isConnected();
        } catch (Exception $connectionError) {
            $errorMsg = $connectionError->getMessage();
            $io->writeError($errorMsg);
        }

        return false;
    }

    /**
     * Set the dbconfig in a given file
     *
     * @param string $dbHost Database host.
     * @param string $dbName Database name.
     * @param string $dbUser Mysql username.
     * @param string $dbPassword Mysql password.
     * @param string $dir The application's root directory.
     * @param string $file A path to a file relative to the application's root
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @return void
     */
    public static function setDbConfigInFile(
        string $dbHost,
        string $dbName,
        string $dbUser,
        string $dbPassword,
        string $dir,
        string $file,
        IOInterface $io,
    ): void {
        $config = $dir . '/config/' . $file;
        $content = file_get_contents($config);

        $content = str_replace('__DBHOST__', $dbHost, $content, $count);
        $content = str_replace('__DATABASE__', $dbName, $content, $count);
        $content = str_replace('__DBUSER__', $dbUser, $content, $count);
        $content = str_replace('__DBPASS__', $dbPassword, $content, $count);

        $result = file_put_contents($config, $content);
        if ($result) {
            $io->write('Updated Datasources.default values in config/' . $file);

            return;
        }
        $io->write('Unable to update Datasources.default values.');
    }

    /**
     * Set the security.cookieKey value in a given file
     *
     * @param string $dir The application's root directory.
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @param string $file A path to a file relative to the application's root
     * @return void
     */
    public static function setCookieKeyInFile(string $dir, IOInterface $io, string $file = 'app_local.php'): void
    {
        $config = $dir . '/config/' . $file;
        $content = file_get_contents($config);

        $content = str_replace('__COOKIEKEY__', hash('sha256', Security::randomBytes(64)), $content, $count);

        if ($count == 0) {
            $io->write('No Security.cookieKey placeholder to replace.');

            return;
        }

        $result = file_put_contents($config, $content);
        if ($result) {
            $io->write('Updated Security.cookieKey value in config/' . $file);

            return;
        }
        $io->write('Unable to update Security.cookieKey value.');
    }
}
