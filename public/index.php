<?php
require dirname(__DIR__) . '/vendor/autoload.php';

error_reporting(E_ALL);
set_error_handler(['\Core\Error', 'errorHandler']);
set_exception_handler(['\Core\Error', 'exceptionHandler']);

ini_set('default_charset', 'UTF-8');
ini_set('expose_php', 0);
ini_set('session.name', 'hackeps');
ini_set('session.gc_probability', 0);
ini_set('session.gc_divisor', 100);
ini_set('session.cookie_httponly', 1);
ini_set('session.gc_maxlifetime', 3600);
ini_set('zlib_output_compression', 'On');

session_start();

$router = new \Core\Router();

$router->add('', ['namespace' => 'External', 'controller' => 'Index', 'action' => '']);

$router->add('{controller}', ['namespace' => 'External', 'action' => '']);
$router->add('{controller}/', ['namespace' => 'External', 'action' => '']);

$router->add('internal/{controller}', ['namespace' => 'Internal', 'action' => '']);
$router->add('internal/{controller}/', ['namespace' => 'Internal', 'action' => '']);

$router->dispatch($_SERVER['QUERY_STRING']);