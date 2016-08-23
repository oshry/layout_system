<?php
session_start();
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];
$page = str_replace('/layout_system/', '', $path);
$paths = explode('/', $page);
//module namespace
$module_name = "{{module%d}}";
define('DOCROOT', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);
define('BASE', "http://" . $_SERVER['SERVER_NAME']."/layout_system/");

// -- Locale setup ---------------------------------------------

// Set the default locale.
// @link  http://php.net/setlocale
setlocale(LC_ALL, 'en_US.utf-8');

// Set the default time zone.
// @link  http://php.net/timezones
date_default_timezone_set('UTC');

// Set the MB extension encoding to the same character set
// @link  http://www.php.net/manual/function.mb-substitute-character.php
mb_internal_encoding('none');

ini_set('auto_detect_line_endings', true);
