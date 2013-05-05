<?php

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'searchg1_fbcon');
define('DB_PASSWORD', 'Asdf1234asdf');
define('DB_DATABASE', 'searchg1_fbcon');
$connection = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD) or die(mysql_error());
$database = mysql_select_db(DB_DATABASE) or die(mysql_error());
?>
