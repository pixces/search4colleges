<?php 	
	require_once("config.php");
	$theme = current_theme();	
	isLogin();
	isallow();
		
	$user_id	= $_SESSION['user_id'];
	
	print_header(get_string('cmi','cmi'));
	cmi_include_head('css');
	cmi_include_head('js');
	print_container_start();
?>
<script type="text/javascript">
	window.addEvent('domready', function(){
    new FormCheck('frm_user');
});
</script>

<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td VALIGN="top" width="200">
			<?php load_menu(); ?>
		</td>
	</tr>
	<tr>
		<td>
			<form method="post" action="<?php echo $CFG->wwwroot;?>/user_manage.php" class="long" id="frm_user" name="frm_user">
			<input name="user_id"  type="hidden"  value="<?php echo $user_id;?>"/>
			<input name="change_password"  type="hidden" value="true"/>
			<table width="100%" border="0">
			  <tbody>
				<tr> 
				  <td width="5%"> </td>
				  <td align="center" width="90%" colspan="2"> </td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td class="DotedHeader" width="90%" colspan="2"> Change Password:</td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td style="height: 21px;" width="5%"> </td>
				  <td style="height: 21px;" width="90%" colspan="2"> </td>
				  <td style="height: 21px;" width="5%"> </td>
				</tr>   
				<tr> 
				  <td width="5%"> </td>
				  <td class="Label" width="20%"> Password : &nbsp;</td>
				  <td width="70%"><input type="password" name="Password" value="" maxlength="30" id="Password" style="width: 150px;" class="validate['required','length[5,25]'] txtBox" /></td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td class="Label" width="21%"> Confirm Password : &nbsp;</td>
				  <td width="70%"><input type="password" name="txtCPassword" value="" maxlength="30" id="txtCPassword" style="width: 150px;" class="validate['required','confirm[Password]'] txtBox" /></td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td width="90%" colspan="2"> <input name="btnSave" id="btnSave" type="submit" value="Submit"></td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td width="90%" colspan="2"> </td>
				  <td width="5%"> </td>
				</tr>
			  </tbody>
			</table>
			</form>
		</td>
	</tr>
</table>
<?php
	print_container_end();

	print_footer();
?>