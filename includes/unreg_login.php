<script type="text/javascript" >
	window.addEvent('domready', function() {			
		new FormCheck('login_user',{display:{showErrors:1}});
	});	
</script>
<form name="login_user" id="login_user" action="<?php echo $CFG->siteroot;?>/login.php" method="POST">
<h3 style="margin-left:40px;">Members Login</h3>
<input name="txtUsername" type="text" onfocus="if (this.value=='Email') this.value = ''" onblur="if (this.value=='') this.value = 'Email'" value="Email" class="validate['required','email'] txtBox""/>
<input name="txtPass" type="password" value="Password" onblur="if (this.value=='') this.value = 'Password'" onfocus="if (this.value=='Password') this.value = ''"  class="validate['required'] txtBox"/>
<div class="submit-button2">

<input  type="image" style="width:43px; height:19px; " value="Login" src="<?php echo $CFG->siteroot;?>/images/search_btn.gif" alt="login"  />

</div>
<p><a href="<?php echo $CFG->siteroot;?>/forgot_password.html">Forgot your password?</a></p>
</form>

