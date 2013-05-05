<?php  
	require_once('config.php');
	cmi_add_to_log('user','logout','User '.$_SESSION['user_id'].' has logged out.');
	$_SESSION['login']='';
	redirect("login.php");
?>