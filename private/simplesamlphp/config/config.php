<?php

if (!ini_get('session.save_handler')) {
    ini_set('session.save_handler', 'file');
}

$host = $_SERVER['HTTP_HOST'];
$db = array(
    'host'      => $_ENV['DB_HOST'],
    'database'  => $_ENV['DB_NAME'],
    'username'  => $_ENV['DB_USER'],
    'password'  => $_ENV['DB_PASSWORD'],
    'port'      => $_ENV['DB_PORT'],
);

/**
 * The Pantheon specific configuration of SimpleSAMLphp
 */

$config = [
    'baseurlpath' => 'https://'. $host .':443/simplesaml/', // SAML should always connect via 443
    'certdir' => 'cert/',
    'logging.handler' => 'errorlog',
    'datadir' => 'data/',
    'tempdir' => $_ENV['HOME'] . '/tmp/simplesaml',

    // Your $config array continues for a while...
    // until we get to the "store.type" value, where we put in DB config...
    'store.type' => 'sql',
    'store.sql.dsn' => 'mysql:host='. $db['host'] .';port='. $db['port'] .';dbname='. $db['database'],
    'store.sql.username' => $db['username'],
    'store.sql.password' => $db['password'],
]
