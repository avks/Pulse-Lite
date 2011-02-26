<?php
/**
Pulse Vote PHP Script
<http://s.technabled.com/PulseVote>
**/

define('PULSE_DIR', 'ENTER_HERE'); // absolute path of the dir where Pulse is; WITHOUT trailing slash

/** DATABASE CONNECTION CONFIGURATION **/
define('HOSTNAME', 'ENTER_HERE'); // hostname of your database; it is localhost in most cases
define('USERNAME', 'ENTER_HERE'); // username of the database
define('PASSWORD', 'ENTER_HERE'); // password for the database
define('DATABASE', 'ENTER_HERE'); // name of the database

@mysql_connect(HOSTNAME, USERNAME, PASSWORD);
@mysql_select_db(DATABASE);

?>