<?php

use craft\test\TestSetup;
use Dotenv\Dotenv;

ini_set('date.timezone', 'UTC');

// The phpdotenv release Craft pins prints deprecation notices on PHP 8.4, and any output before
// Craft starts its session fails the run with "headers already sent".
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);
Dotenv::createUnsafeImmutable(__DIR__)->safeLoad();

define('CRAFT_ROOT_PATH', dirname(__DIR__));
define('CRAFT_TESTS_PATH', __DIR__);
define('CRAFT_STORAGE_PATH', __DIR__.'/_craft/storage');
define('CRAFT_TEMPLATES_PATH', __DIR__.'/_craft/templates');
define('CRAFT_CONFIG_PATH', __DIR__.'/_craft/config');
define('CRAFT_MIGRATIONS_PATH', __DIR__.'/_craft/migrations');
define('CRAFT_TRANSLATIONS_PATH', __DIR__.'/_craft/translations');
define('CRAFT_VENDOR_PATH', dirname(__DIR__).'/vendor');

if (! is_dir(CRAFT_STORAGE_PATH.'/logs')) {
    mkdir(CRAFT_STORAGE_PATH.'/logs', 0777, true);
}

TestSetup::configureCraft();
