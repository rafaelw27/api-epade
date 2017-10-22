<?php

//if not defined
//if we call you from consolo include auoload
if (php_sapi_name() === 'cli') {
    if (!defined('APP_PATH')) {
        define('APP_PATH', realpath('.'));
    }

    require_once __DIR__ . '../../../vendor/autoload.php';
} else {
    if (!defined('APP_PATH')) {
        define('APP_PATH', realpath('..'));
    }
}

//ENV Variables
$dotenv = new Dotenv\Dotenv(__DIR__ . '/../../');
$dotenv->load();

return new \Phalcon\Config([

    //CLI
    'appName' => 'Incursion',
    'version' => '0',
    'tasksDir' => APP_PATH . '/cli/tasks/',
    'annotationsAdapter' => 'memory',
    'printNewLine' => true,

    'database' => [
        'adapter' => 'Mysql',
        'host' => getenv('DATABASE_HOST'),
        'username' => getenv('DATABASE_USER'),
        'password' => getenv('DATABASE_PASS'),
        'dbname' => getenv('DATABASE_NAME'),

        'dbfinanceHost' => getenv('FINANCE_DATABASE_HOST'),
        'dbfinanceUser' => getenv('FINANCE_DATABASE_USER'),
        'dbfinanceName' => getenv('FINANCE_DATABASE_NAME'),
        'dbfinancePass'=> getenv('FINANCE_DATABASE_PASS'),

        'dbfinanceNameEd' => getenv('FINANCE_DATABASE_NAME_ED'),
        'persistant' => false,
    ],
    'application' => [
        'version' => '0.5.0',
        'siteName' => getenv('DOMAIN'),
        'siteUrl' => getenv('URL'),
        'controllersDir' => APP_PATH . '/app/controllers/',
        'modelsDir' => APP_PATH . '/app/models/',
        'libraryDir' => APP_PATH . '/app/library/',
        'cacheDir' => APP_PATH . '/app/cache/',
        'viewsDir' => APP_PATH . '/app/views/',
        'voltDir' => APP_PATH . '/app/cache/volt/',
        'baseUri' => '/',
        'production' => getenv('PRODUCTION'),
        'debug' => ['profile' => getenv('DEBUG_PROFILE'), 'logQueries' => getenv('DEBUG_QUERY'), 'logRequest' => getenv('DEBUG_REQUEST')],
        'hmacSecurity' => getenv('HMCA_SECURITY'),
        'uploadDir' => '',
    ],
    'namespace' => [
        'controller' => 'Epade\Controllers',
        'models' => 'Epade\Models',
    ],
    'memcache' => [
        'host' => getenv('MEMCACHE_HOST'),
        'port' => getenv('MEMCACHE_PORT'),
    ],
    'cdn' => [
        'url' => getenv('CDN_URL'),
    ],
    'beanstalk' => [
        'host' => getenv('BEANSTALK_HOST'),
        'port' => getenv('BEANSTALK_PORT'),
        'prefix' => getenv('BEANSTALK_PREFIX'),
    ],
    'redis' => [
        'host' => getenv('REDIS_HOST'),
        'port' => getenv('REDIS_PORT'),
    ],
    'elasticSearch' => [
        'hosts' => getenv('ELASTIC_HOST'), //change to pass array
    ],
    'incursio' => [
        'url' => getenv('INCURSIO_URL'),
    ],
    'email' => [
        'driver' => 'smtp',
        'host' => getenv('EMAIL_HOST'),
        'port' => getenv('EMAIL_PORT'),
        'username' => getenv('EMAIL_USER'),
        'password' => getenv('EMAIL_PASS'),
        'from' => [
            'email' => 'info@gewaer.io',
            'name' => 'Gewaer',
        ],
        'debug' => [
            'from' => [
                'email' => 'noreply@gewaer.io',
                'name' => 'Gewaer',
            ],
        ],
    ],
    'noauth' => [
        'GET' => [

        ],
    ],
]);
