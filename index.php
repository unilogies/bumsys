<?php
/**
 * @version 0.1
 * 
 * Minimum PHP Version 7.0
 * Minimum MySQL Version 5.7
 */

// ini_set('memory_limit', '2048M');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Base directory
define('DIR_BASE', __DIR__ . '/');

// load the app
require DIR_BASE . "core/loader.php";


?>
