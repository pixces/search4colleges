<?php
if(isset($_SESSION['user_type']) && !empty($_SESSION['user_type'])){ 
	$user_info = check_login(); 
	//print_object($user_info);
?>

<strong>
<?php
	if($_SESSION['user_type'] == 'school'){
		echo "<a href='".$CFG->siteroot."/my_account.html' title='Click here to view My Account Page'>".ucwords($user_info->school_name).'</a>';
	}
	else if($_SESSION['user_type'] =='staff'){
		echo "<a href='".$CFG->siteroot."/my_account.html' title='Click here to view My Account Page'>".ucwords($user_info->name).'</a>';
	}
	else{
		
		echo "<a href='".$CFG->siteroot."/my_account.html' title='Click here to view My Account Page'>".ucwords($user_info->first_name).'</a>';
	}
?>

</strong>
<p><strong><a href="<?php echo $CFG->siteroot;?>/logout.html">Logout</a></strong></p>
<?php } else 
{
if(isset($_GET['m'])){
	if($_GET['m'] == '1'){
		echo "<div style='color:red;font-size:12px;'>The username or password you entered is incorrect.</div>";
	}
	if($_GET['m'] == '2'){
		echo "<div style='color:red;font-size:12px;'><b>Admin has inactivated your account.</b><br /> For queries email at <a href='mailto:info@search4colleges.com'>info@search4colleges.com</a></div>";
	}
	if($_GET['m'] == '3'){
		echo "<div style='color:red;font-size:12px;'><b>Need Account Verification. Please verify your account to login.</b><br /> For queries email at <a href='mailto:info@search4colleges.com'>info@search4colleges.com</a></div>";
	}
}
?>
<form name="login_user" id="login_user" action="<?php echo $CFG->siteroot;?>/login.php" method="POST">
<input name="txtUsername" type="text" onfocus="if (this.value=='Email') this.value = ''" onblur="if (this.value=='') this.value = 'Email'" value="Email" class="validate['required','email'] txtBox""/>
<input name="txtPass" type="password" value="Password" onblur="if (this.value=='') this.value = 'Password'" onfocus="if (this.value=='Password') this.value = ''"  class="validate['required'] txtBox"/>
<div class="submit-button">
<input  type="image" style="width:43px; height:19px; " value="Login" src="<?php echo $CFG->siteroot;?>/images/search_btn.gif" alt="login"  />
</div>
<p><a href="<?php echo $CFG->siteroot;?>/forgot_password.html" >Forgot your password?</a></p>
</form>
<?php  } ?>
