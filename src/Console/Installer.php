<?php
namespace App\Console;

if (!defined('STDIN')) {
    define('STDIN', fopen('php://stdin', 'r'));
}

use Cake\Datasource\ConnectionManager;
use Cake\Utility\Security;
use Composer\Script\Event;
use Exception;

/**
 * Provides installation hooks for when this application is installed via
 * composer. Customize this class to suit your needs.
 */
class Installer
{

    /**
     * An array of directories to be made writable
     */
    const WRITABLE_DIRS = [
        'logs',
        'tmp',
        'tmp/cache',
        'tmp/cache/models',
        'tmp/cache/persistent',
        'tmp/cache/views',
        'tmp/sessions',
        'tmp/tests',
        'uploads',
        'webroot/img/thumbs'
    ];

    /**
     * Does some routine installation tasks so people don't have to.
     *
     * @param \Composer\Script\Event $event The composer event object.
     * @throws \Exception Exception raised by validator.
     * @return void
     */
    public static function postInstall(Event $event)
    {
        $io = $event->getIO();

        $rootDir = dirname(dirname(__DIR__));

        static::createAppConfig($rootDir, $io);
        static::createWritableDirectories($rootDir, $io);

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
                'Y'
            );

            if (in_array($setFolderPermissions, ['Y', 'y'])) {
                static::setFolderPermissions($rootDir, $io);
            }

            $dbConnectSuccess = false;
            while (!$dbConnectSuccess) {
                $dbHost = $io->ask('<info>Enter database host ?</info> [<comment>localhost</comment>]? ', 'localhost');
                $dbName = $io->ask('<info>Enter database name ?</info> [<comment>famiree</comment>]? ', 'famiree');
                $dbUser = $io->ask('<info>Enter db user ?</info> ');
                $dbPassword = $io->ask('<info>Enter db password ?</info> ');

                $dbConnectSuccess = static::checkDbConnection($dbHost, $dbName, $dbUser, $dbPassword, $io);

                if ($dbConnectSuccess) {
                    static::setDbConfigInFile($dbHost, $dbName, $dbUser, $dbPassword, $rootDir, 'app.php', $io);
                    if (static::importDbSchema($rootDir, 'famiree.sql')) {
                        $io->write('Successfuly imported sql schema.');
                    }
                } else {
                    $io->writeError('Cannot connect to mysql database. Please try again.');
                }
            }
        } else {
            static::setFolderPermissions($rootDir, $io);
        }

        static::setSecuritySalt($rootDir, $io);

        $class = 'Cake\Codeception\Console\Installer';
        if (class_exists($class)) {
            $class::customizeCodeceptionBinary($event);
        }
    }

    /**
     * Create the config/app.php file if it does not exist.
     *
     * @param string $dir The application's root directory.
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @return void
     */
    public static function createAppConfig($dir, $io)
    {
        $appConfig = $dir . '/config/app.php';
        $defaultConfig = $dir . '/config/app.default.php';
        if (!file_exists($appConfig)) {
            copy($defaultConfig, $appConfig);
            $io->write('Created `config/app.php` file');
        }
    }

    /**
     * Create the `logs` and `tmp` directories.
     *
     * @param string $dir The application's root directory.
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @return void
     */
    public static function createWritableDirectories($dir, $io)
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
    public static function setFolderPermissions($dir, $io)
    {
        // Change the permissions on a path and output the results.
        $changePerms = function ($path) use ($io) {
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

        $walker = function ($dir) use (&$walker, $changePerms) {
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
    public static function setSecuritySalt($dir, $io)
    {
        $newKey = hash('sha256', Security::randomBytes(64));
        static::setSecuritySaltInFile($dir, $io, $newKey, 'app.php');
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
    public static function setSecuritySaltInFile($dir, $io, $newKey, $file)
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
    public static function setAppNameInFile($dir, $io, $appName, $file)
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
     * Try to connect to database
     *
     * @param string $dbHost Database host.
     * @param string $db Database name.
     * @param string $dbUser Mysql username.
     * @param string $dbPassword Mysql password.
     * @param \Composer\IO\IOInterface $io IO interface to write to console.
     * @return bool
     */
    public static function checkDbConnection($dbHost, $db, $dbUser, $dbPassword, $io)
    {
        try {
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
            $result = $connection->connect();

            return $result;
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
    public static function setDbConfigInFile($dbHost, $dbName, $dbUser, $dbPassword, $dir, $file, $io)
    {
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
     * Import sql schema from config/schema/famiree.sql
     *
     * @param string $dir The application's root directory.
     * @param string $file A path to a file relative to the application's root
     * @return bool
     */
    public static function importDbSchema($dir, $file)
    {
        $config = $dir . '/config/schema/' . $file;
        $content = file_get_contents($config);

        $result = false;

        if ($content) {
            $connection = ConnectionManager::get('install');
            $result = (bool)$connection->execute($content);
        }

        return $result;
    }
}
