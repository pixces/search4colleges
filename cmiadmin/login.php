<?php
	require_once('config.php');
	print_header(get_string('cmi','cmi'));
	$theme = current_theme();

	cmi_include_head('css');
	cmi_include_head('js');

	if(isset($_SESSION['login']) && $_SESSION['login']=="yes")
	{
		redirect("index.php");
	}
	print_container_start();

	require_once('login_form.php');
	print_container_end();

	print_footer();
?>
