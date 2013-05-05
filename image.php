<?php 

    require_once('cmiadmin/config.php');
    require_once('cmiadmin/lib/filelib.php');

    if (!isset($CFG->filelifetime)) {
        $lifetime = 86400;     // Seconds for files to remain in caches
    } else {
        $lifetime = $CFG->filelifetime;
    }

    // disable moodle specific debug messages
    disable_debugging();

    $relativepath = get_file_argument('file.php');
    $forcedownload = optional_param('forcedownload', 0, PARAM_BOOL);
    
    // relative path must start with '/', because of backup/restore!!!
    if (!$relativepath) {
        error('No valid arguments supplied or incorrect server configuration');
    } else if ($relativepath{0} != '/') {
        error('No valid arguments supplied, path does not start with slash!');
    }

    $pathname = $CFG->dataroot.$relativepath;

    // extract relative path components
    $args = explode('/', trim($relativepath, '/'));
    if (count($args) == 0) { // always at least courseid, may search for index.html in course root
        error('No valid arguments supplied');
    }
  
    // check that file exists
    if (!file_exists($pathname)) {
        not_found();
    }



	include("resize-class.php");
	$resizeObj = new resize($pathname);
	$resizeObj -> resizeImage(224, 110, 'crop');
	$resizeObj -> display($pathname,15,'094E6C');


    /*// ========================================
    // finally send the file
    // ========================================
    session_write_close(); // unlock session during fileserving
    $filename = $args[count($args)-1];
    send_file($pathname, $filename, $lifetime, 0 , false, $forcedownload);*/

    function not_found() {
		global $CFG;
        redirect($CFG->siteroot.'/404.php');
    }
?>
