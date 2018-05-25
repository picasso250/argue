<?php

define('ROOT', dirname(__DIR__));
define('ROOT_VIEW', ROOT.'/view');

require ROOT.'/vendor/autoload.php';
require ROOT.'/lib.php';
require ROOT.'/action.php';

use DebugBar\StandardDebugBar;

$dotenv = new Dotenv\Dotenv(ROOT);
$dotenv->load();

$config_file = ROOT.'/config.ini';
if (!is_file($config_file) || !is_readable($config_file))
    die("no config.ini");
$config = parse_ini_file($config_file);

if ($_ENV['DEBUG']) {
    $debugbar = new StandardDebugBar();
    $debugbarRenderer = $debugbar->getJavascriptRenderer();
    $debugbar->addCollector(new DebugBar\DataCollector\ConfigCollector($config));

    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}

session_start();

$dotenv->required(['DATABASE_DSN', 'DB_USER', 'DB_PASS'])->notEmpty();
ORM::configure($_ENV['DATABASE_DSN']);
ORM::configure('username', $_ENV['DB_USER']);
ORM::configure('password', $_ENV['DB_PASS']);
if ($_ENV['DEBUG']) {
    $pdo = new DebugBar\DataCollector\PDO\TraceablePDO(ORM::get_db());
    $debugbar->addCollector(new DebugBar\DataCollector\PDO\PDOCollector($pdo));
}

regex_router([
    'GET /' => 'action_index',
    'GET /install' => 'action_install',
    'POST /new_argue' => 'action_new_argue',
    'GET /a/(\d+)' => 'action_argue',
    'POST /ajax_do' => 'action_ajax_do',
]);