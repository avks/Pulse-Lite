<?php
/**
Pulse Lite Voting Script
http://s.technabled.com/PulseVote
**/
session_start();
ob_start();
error_reporting(E_ALL-E_NOTICE);

define('PULSE_DIR', 'ENTER_PATH_HERE'); // absolute path of the dir where Pulse is; WITHOUT trailing slash

/** DATABASE CONNECTION CONFIGURATION **/
define('HOSTNAME', 'ENTER_HOST_HERE'); // hostname of your database; it is localhost in most cases
define('USERNAME', 'ENTER_USER_NAME_HERE'); // username of the database
define('PASSWORD', 'ENTER_PASSWORD_HERE'); // password for the database
define('DATABASE', 'ENTER_DB_NAME_HERE'); // name of the database

@mysql_connect(HOSTNAME, USERNAME, PASSWORD);
@mysql_select_db(DATABASE);

?>