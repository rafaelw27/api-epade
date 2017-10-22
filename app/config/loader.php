<?php

$loader = new \Phalcon\Loader();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader->registerNamespaces([
    'Mesocom\Controllers' => $config->application->controllersDir, //migrate from mesocom to incursio namespace
    'Epade\Controllers' => $config->application->controllersDir,
    'Mesocom\Models' => $config->application->modelsDir,
    'Epade\Models' => $config->application->modelsDir,
    'Mesocom' => $config->application->libraryDir,
    'Incursio' => $config->application->libraryDir,
    'Baka\Database' => '/home/baka/database/src/',
    'Baka\CustomFields' => '/home/baka/customfields/src/',
    'Baka\CustomFields\Models' => '/home/baka/customfields/src/models/',
    'Baka\Http' => '/home/baka/http/src/',
    'Baka\Auth' => '/home/baka/auth/src/',
    'Baka\Auth\Models' => '/home/baka/auth/src/models/',
    'Baka\SaaS' => '/home/baka/saas/src/',
    'Baka\SaaS\Models' => '/home/baka/saas/src/models/',
    'Baka\Hmac' => '/home/baka/baka-phalconphp-hmac/src/',
    'Baka\Hmac\Models' => '/home/baka/baka-phalconphp-hmac/src/models/',
    'Baka\Mail' => '/home/baka/mail/src/',
]);

$loader->register();
