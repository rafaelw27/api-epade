<?php

use Baka\Hmac\Models\Keys;
use Incursio\Models\UserKeysMappers;
use Incursio\Models\Users;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonoLogger;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\DI\FactoryDefault;
use Phalcon\Logger;
use Phalcon\Mvc\Model\Metadata\Files as MetaDataAdapter;
use Phalcon\Mvc\Model\Metadata\Memory as MemoryMetaDataAdapter;
use Zoho\CRM\ZohoClient;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * Database connection of financesfactory_fm
 */
$di->set('dbfinanceEd', function () use ($config, $di) {
    
        //db connection
        $connection = new DbAdapter(array(
            'host' => $config->database->dbfinanceHost,
            'username' => $config->database->dbfinanceUser,
            'password' => $config->database->dbfinancePass,
            'dbname' => $config->database->dbfinanceNameEd,
            'charset' => 'utf8',
        ));

    return $connection;
});

/**
 * Database connection of financesfactory_fm
 */
$di->set('dbfinancefm', function () use ($config, $di) {
    
        //db connection
        $connection = new DbAdapter(array(
            'host' => $config->database->dbfinanceHost,
            'username' => $config->database->dbfinanceUser,
            'password' => $config->database->dbfinancePass,
            'dbname' => $config->database->dbfinanceName,
            'charset' => 'utf8',
        ));

    return $connection;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function () use ($config, $di) {

    //db connection
    $connection = new DbAdapter(array(
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname,
        'charset' => 'utf8',
        'persistant' => false,
    ));

    //profile sql queries
    if ($config->application->debug['logQueries']) {
        $eventsManager = new \Phalcon\Events\Manager();

        //Listen all the database events
        $eventsManager->attach('db', function ($event, $connection) use ($di) {
            if ($event->getType() == 'beforeQuery') {
                $sqlVariables = $connection->getSQLVariables();
                if (count($sqlVariables)) {
                    $di->getLog('sql')->addInfo($connection->getSQLStatement() . ' BINDS =>', $sqlVariables);
                } else {
                    $di->getLog('sql')->addInfo($connection->getSQLStatement());
                }
            }
        });

        //Assign the eventsManager to the db adapter instance
        $connection->setEventsManager($eventsManager);
    }

    return $connection;
});

/**
 * Redis configuration
 */
$di->set('redis', function () use ($config) {
    //Connect to redis
    $redis = new Redis();
    $redis->connect($config->redis->host, $config->redis->port);
    $redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);

    return $redis;
});

// Start the session
$di->setShared('session', function () use ($config) {
    $memcache = new \Phalcon\Session\Adapter\Memcache(array(
        'host' => $config->memcache->host, // mandatory
        'post' => $config->memcache->port, // optional (standard: 11211)
        'lifetime' => 8600, // optional (standard: 8600)
        'prefix' => 'naruhodo', // optional (standard: [empty_string]), means memcache key is my-app_31231jkfsdfdsfds3
        'persistent' => false, // optional (standard: false)
    ));

    //only start the session if its not already started
    if (!isset($_SESSION)) {
        $memcache->start();
    }

    return $memcache;

    /*
forma vieja de usar sessiones
$session = new SessionAdapter();
$session->start();
return $session;*/
});

/**
 * Set the models cache service
 * Cache for models
 */
$di->set('modelsCache', function () use ($config) {

    //si no estamos en producto 0 cache
    if (!$config->application->production) {
        $frontCache = new \Phalcon\Cache\Frontend\None();
        $cache = new Phalcon\Cache\Backend\Memory($frontCache);
    } else {
        //Cache data for one day by default
        $frontCache = new \Phalcon\Cache\Frontend\Data(array(
            "lifetime" => 86400,
        ));

        //Memcached connection settings
        $cache = new \Phalcon\Cache\Backend\Memcache($frontCache, array(
            "host" => $config->memcache->host,
            "port" => $config->memcache->port,
        ));
    }

    return $cache;
});

/**
 * Set up the flash service
 */
$di->set('flash', function () {
    return new \Phalcon\Flash\Session();
});

$di->set('queue', function () use ($config) {

    //Connect to the queue
    $queue = new Phalcon\Queue\Beanstalk\Extended([
        'host' => $config->beanstalk->host,
        'prefix' => $config->beanstalk->prefix,
    ]);

    return $queue;
});

$di->set('config', $config);

/**
 * System Log using monolog
 */
$di->set('log', function ($file = 'debug') use ($config, $di) {

    // Create the logger
    $logger = new MonoLogger('Intras.API');
    // Now add some handlers
    $logger->pushHandler(new StreamHandler(APP_PATH . "/logs/" . $file . '.log', Logger::DEBUG));
    $logger->pushHandler(new FirePHPHandler());

    return $logger;
});

$di->set('purifier', function () use ($config) {
    //require_once($config->application->vendorDir . 'ezyang/htmlpurifier/library/HTMLPurifier.auto.php');

    $hpConfig = \HTMLPurifier_Config::createDefault();
    $hpConfig->set('HTML.Allowed', '');

    return new \HTMLPurifier($hpConfig);
});

/**
 * service to get the CDN for the service. ¿why a service ? we can have multiple cdn we need a way to hand
 */
$di->set('cdn', function () use ($config) {
    return $config->cdn->url;
});

/**
 * UserData dependency injection for the system
 *
 * @return \Baka\Auth\Models\Sessions
 */
$di->set('userData', function () use ($config, $di) {
    $session = new \Baka\Auth\Models\Sessions();
    $request = new \Phalcon\Http\Request();
    $url = pathinfo($di->get('router')->getRewriteUri());

    //on no production server lets send kaiioken
    if (!$config->application->production) {
        $userData = Users::findFirst(getenv('TESTUSER'));
        $userData->loggedIn = true;
        $userData->setCompany($request->getHeader('PUBLICKEY'));

        return $userData;
    } elseif ($request->getHeader('PUBLICKEY') || strpos($url['dirname'], 'receivers')) {
        //get the userData
        if ($request->getHeader('PUBLICKEY')) {
            $key = Keys::findFirstByPublic($request->getHeader('PUBLICKEY'));
            $userData = Users::findFirst($key->users_id);
            $userData->loggedIn = true;
            $userData->setCompany($key->public);

            return $userData;
        } elseif (strpos($url['dirname'], 'receivers')) {
            $userKeyMapper = UserKeysMappers::findFirstByKey_mapper($url['basename']);
            $userData = Users::findFirst($userKeyMapper->users_id);
            $userData->loggedIn = true;
            $userData->setCompany(Keys::findFirstByUsers_id($userKeyMapper->users_id)->public);

            return $userData;
        } else {
            throw new Exception("Trying to access user date without a public key");
        }
    }
    //for some strange reason you didnt send a key so, we need to kill everything off for this session

    throw new Exception("Trying to access user date without a public key");
    //return \Baka\Auth\Models\Sessions::start(1, $request->getClientAddress());
});
/**
 * email
 */
$di->set('mail', function () use ($config, $di) {

    //setup
    $mailer = new \Baka\Mail\Manager($config->email->toArray());

    return $mailer->createMessage();
});

/**
 * Zoho
 */
$di->set('zoho', function () use ($config) {
    $zohoClient = new ZohoClient(getenv('ZOHO_DEFAULT_KEY'));

    return $zohoClient;
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () use ($config) {
    if (!$config->application->production) {
        return new MemoryMetaDataAdapter();
    }

    return new MetaDataAdapter([
        'metaDataDir' => APP_PATH . '/app/cache/metaData/',
    ]);
},
    true
);
