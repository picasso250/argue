<?php

define('ROOT', dirname(__DIR__));
define('ROOT_VIEW', ROOT.'/view');

require ROOT.'/vendor/autoload.php';
require ROOT.'/lib.php';
require ROOT.'/action.php';

use DebugBar\StandardDebugBar;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Handler\JsonResponseHandler;

$dotenv = new Dotenv\Dotenv(ROOT);
$dotenv->load();

$config_file = ROOT.'/config.ini';
if (!is_file($config_file) || !is_readable($config_file))
    die("no config.ini");
$config = parse_ini_file($config_file);

if (isset($_ENV['DEBUG'])&& $_ENV['DEBUG']) {
    $debugbar = new StandardDebugBar();
    $debugbarRenderer = $debugbar->getJavascriptRenderer();
    $debugbar->addCollector(new DebugBar\DataCollector\ConfigCollector($config));

    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);

    // Add a special handler to deal with AJAX requests with an
    // equally-informative JSON response. Since this handler is
    // first in the stack, it will be executed before the error
    // page handler, and will have a chance to decide if anything
    // needs to be done.
    if (Whoops\Util\Misc::isAjaxRequest()) {
        $j = new JsonResponseHandler;
        $j->addTraceToOutput(true);
        $whoops->pushHandler($j);
    }

    $whoops->register();
}

session_start();

$dotenv->required(['DATABASE_DSN', 'DB_USER', 'DB_PASS'])->notEmpty();
ORM::configure($_ENV['DATABASE_DSN']);
ORM::configure('username', $_ENV['DB_USER']);
ORM::configure('password', $_ENV['DB_PASS']);

$cur_user = cur_user();

$found = regex_router([
    'GET /' => 'action_index',
    'GET /install' => 'action_install',
    'POST /new_argue' => 'action_new_argue',
    'GET /a/(\d+)' => 'action_argue',
    'POST /ajax_do' => 'action_ajax_do',
    '/login' => 'action_login',
]);
if (!$found) {
    http_response_code(404);
    die("404");
}