<?php
// Database Configuration
define('DB_HOST', 'localhost'); // Hostname
define('DB_NAME', 'bumsys_demo'); // Database Name
define('DB_USER', 'root'); // Dtabase user name
define('DB_PASSWORD', ''); // Database User Password

// Create Normal connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// ‍Enable utf8 support
$conn->set_charset("utf8");
$conn->query("SET GLOBAL time_zone = '+6:00';");

/** 
 * From PHP 8.1 the mysql default error mode set to exceptions. 
 * To turn off this add following line
 * 
 * Check: https://php.watch/versions/8.1/mysqli-error-mode
 */
mysqli_report(MYSQLI_REPORT_OFF);

/** For Configure PHP 8.1 behavior in all PHP versions add the following line */
//mysqli_report(MYSQLI_REPORT_ERROR|MYSQLI_REPORT_STRICT);

// Error variable
$get_all_db_error = array();

// Table Prefix variable
$table_prefix = TABLE_PREFIX;

?>