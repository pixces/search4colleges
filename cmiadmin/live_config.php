<?php
unset($CFG);

$CFG->server_status = 'live';
$CFG->dbtype = 'mysql';
$CFG->dbhost = 'localhost';
$CFG->dbname = 'searchg1_search4c';
$CFG->dbuser = 'searchg1_search4';
$CFG->dbpass = 'fN#B;O;EUaSg';
$CFG->dbpersist = false;
$CFG->prefix = '';

$CFG->wwwroot = "http://67.20.99.138/cmiadmin";
$CFG->siteroot = "http://67.20.99.138";
$CFG->dirroot = "/home3/searchg1/public_html/cmiadmin";
$CFG->dataroot = '/home3/searchg1/public_html/uploads';
$CFG->directorypermissions = 00777;  // try 02777 on a server in Safe Mode

require_once("$CFG->dirroot/lib/setup.php");
require_once("cmi_lib.php");
?>