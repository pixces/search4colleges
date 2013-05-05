<?php  
	require_once('config.php');
	$username = required_param('txtUsername', PARAM_TEXT);
	$password = required_param('txtPass', PARAM_TEXT);
	
	$user_data = get_record("users", "username", $username);
	if(empty($user_data))
	{
		error("Invalid Username !!!");
	}

	if($user_data->username == $username)
	{
		if($user_data->password == md5($password))
		{
			if($user_data->status == 'active')
			{
			    $_SESSION['login']				= 'yes';
				$_SESSION['username']			= $user_data->username;
				$_SESSION['user_id']			= $user_data->id;
				$_SESSION['allowed_sections']	= $user_data->allowed_sections;
				$_SESSION['login_time']			= date('m/d/Y h:i:s A', time());

				cmi_add_to_log('User Management','login','user '.$_SESSION['user_id'].' logged in');
				redirect("index.php");
			}
			else if($user_data->status == 'inactive')
			{
				cmi_add_to_log('User Management','login','user '.$_SESSION['user_id'].' Account Inactive tried Loggin In');
				error("Account Inactive !!!");
			}
			else if($user_data->status == 'delete')
			{
				cmi_add_to_log('User Management','login','user '.$_SESSION['user_id'].' Account Deleted tried Loggin In');
				error("Account Deleted !!!");
			}
		}
		else
		{
			error("Invalid Password!!!");
		}
	}
	else
	{
		error("Invalid Username !!!");
	}
?>