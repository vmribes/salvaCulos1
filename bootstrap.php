<?php
require "vendor/autoload.php";

use App\Josep\Config;
use App\Registry;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;


$configXML = new \App\Alex\Config(__DIR__ . '/./config.xml');
$configJson = new \App\Josep\Config(__DIR__ . '/./config.json');

Registry::setPDO($configXML);

// create a log channel
$log = new Logger('movies');
$log->pushHandler(new StreamHandler(__DIR__ . "/./app.log", Logger::DEBUG));
$log->pushHandler(new FirePHPHandler());
Registry::set(Registry::LOGGER, $log);

$router = new AltoRouter();

Registry::set(Registry::ROUTER, $router);

// map homepage
$router->map('GET', '/', 'MovieController#list', 'movie_list');

// dynamic named route
$router->map('GET|POST', '/movies/[i:id]/edit', "MovieController#edit", 'movie_edit');


$router->map('GET', '/movies/[i:id]/view', 'MovieController#view', 'movie_view');

$router->map('GET', '/movies/create', 'MovieController#create', 'movie_create');
$router->map('POST', '/movies/createStore', 'MovieController#createStore', 'movie_createStore');

$router->map('GET|POST', '/movies/[i:id]/delete', "MovieController#delete", 'movie_delete');

//UserController
$router->map('GET|POST', "/login", "UserController#login", "user_login");

$router->map('GET', "/logout", "UserController#logout", "user_logout");

$router->map('GET|POST', "/register", "UserController#register", "user_register");
