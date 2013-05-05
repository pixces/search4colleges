<?php

include_once("lib/mysql.class.inc");
include_once("config.php");
require($CFG->dirroot.'/lib/pclzip/pclzip.lib.php');
require($CFG->dirroot.'/lib/filelib.php');
require_once 'lib/phpmailer/class.phpmailer.php';

$backup	= new MyBackUp(); 

//SERVER CONFIG
if(!empty($CFG->dbhost))
	$backup->server	= $CFG->dbhost; 
if(!empty($server['port']))
	$backup->port	= $server['port'];
if(!empty($CFG->dbuser))
	$backup->usern	= $CFG->dbuser;

$backup->userp	= $CFG->dbpass;
$backup->dbase	= $CFG->dbname;

$backUpFolder	= $CFG->dataroot."/db_backup";
if(!file_exists($backUpFolder)){
	mkdir($backUpFolder);	
}
chmod($backUpFolder,0777); 

$backup->filename	= $backUpFolder."/".$CFG->dbname."_".date("Y_M_d").".sql";
$backup->email		= $backUpFolder."/backup.zip";

//Calling generator Function
if(!$backup->BackUp())
{
	echo $backup->error; //On error this function returns back. Error details will be in this variable.
}
else
{
	cmi_add_to_log('Misc','Backup','Backup taken by id='.$_SESSION['user_id']);

	$archive = new PclZip($backUpFolder."/backup.zip");
	$v_list = $archive->create($backup->filename,PCLZIP_OPT_REMOVE_PATH,$backUpFolder);
	if ($v_list == 0) {
	 die("Error : ".$archive->errorInfo(true));
	}

	$mail = new PHPMailer(true);
	$mail->IsMail();
	$mail->Subject = 'Back Up';
	$mail->FromName = 'CMI Admin Panel';
	$mail->From = $CFG->backup_frommail;
	$mail->AddAddress($CFG->backup_tomail);			
	$body = 'Back Up File Attached';
	$mail->AddAttachment($backup->email);
	$mail->Body = $body;
	$mail->Send($body);

	send_file($backup->filename,'backup.sql',0);
}
	
?>
