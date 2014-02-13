<?php
unset($CFG);

//enabling debug
$CFG->debug = 6143;
$CFG->debugdisplay = false;

$CFG->server_status = 'live';
$CFG->dbtype = 'mysql';
//$CFG->dbhost = 'localhost';
//$CFG->dbname = 'searchg1_search4c';
//$CFG->dbuser = 'searchg1_search4';
//$CFG->dbpass = 'fN#B;O;EUaSg';

$CFG->dbhost = 'localhost';
$CFG->dbname = 'search4c';
$CFG->dbuser = 'root';
$CFG->dbpass = '';


$CFG->dbpersist = false;
$CFG->prefix = '';

##$CFG->wwwroot = "https://www.search4colleges.com/cmiadmin";
##$CFG->siteroot = "https://www.search4colleges.com";


//$CFG->wwwroot = "http://www.search4colleges.com/cmiadmin";
//$CFG->siteroot = "http://www.search4colleges.com";

/* local */
//$CFG->wwwroot = "http://localhost:8888/search4colleges/cmiadmin";
//$CFG->siteroot = "http://localhost:8888/search4colleges";

$CFG->wwwroot = "http://localhost/search4colleges/cmiadmin";
$CFG->siteroot = "http://localhost/search4colleges";



//$CFG->dirroot = "/home3/searchg1/public_html/cmiadmin";
//$CFG->dataroot = '/home3/searchg1/public_html/uploads';

/* local */
//$CFG->dirroot = "/mnt/www/search4colleges/cmiadmin";
//$CFG->dataroot = '/mnt/www/search4colleges/uploads';

$CFG->dirroot = "C:\wamp\www\search4colleges/cmiadmin";
$CFG->dataroot = 'C:\wamp\www\search4colleges/uploads';

$CFG->directorypermissions = 00777;  // try 02777 on a server in Safe Mode


require_once("$CFG->dirroot/lib/setup.php");
require_once("cmi_lib.php");
?>
