<?php 
	require_once('cmiadmin/config.php');
	//require_once('includes/index_header.php'); 
	$msg ='Please Login  ';
	
	if(isset($_POST['txtUsername']) && isset($_POST['txtPass']))
	{
		
		$username = strtolower(required_param('txtUsername', PARAM_TEXT));
		$password = required_param('txtPass', PARAM_TEXT);
		$pass=md5($password);

		$sql="SELECT * FROM {$CFG->prefix}fe_users where email='".$username."' and password='".$pass."'";
		
		$user_data = get_record_sql($sql);
		
		if(empty($user_data))
		{
			header('Location:index.html?m=1');
			
		}
		else
		{
			if($user_data->status == 'active'){
			
				if($user_data->isapproved ==1)
				{
					if($user_data->email == $username)
					{
						
						if($user_data->password == $pass)
						{	
							
							$_SESSION['user_login']			='yes';
							$_SESSION['user_username']		= $user_data->email;
							$_SESSION['s4c_user_id']		= $user_data->id;
							$_SESSION['user_type']			= $user_data->user_type;
							$_SESSION['user_login_time']	= date('m/d/Y h:i:s A', time());						
							$site_redirect = $CFG->siteroot.'/my_account.html'; 
							header("Location: $site_redirect");
						}
						else
						{					 
						   header('Location:index.html?m=1');

						}
					}
					else
					{
						   header('Location:index.html?m=1');
					}
				}
				else
				{
					header('Location:index.html?m=3');
				}
			}
			else{
				header('Location:index.html?m=2');
			}
		}
	}
?>