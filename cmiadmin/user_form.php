<?php 	
	require_once("config.php");
	$theme = current_theme();
	isLogin();
	//isallow();
	$edit_flag				= false;
	$caption_txt			= "Add";
	$txtFirstName			= "";
	$txtLastName			= "";
	$txtEmail				= "";
	$txtPhone				= "";
	$txtUsername			= "";
	$txtPassword			= "";
	$chkAllowedSections		= array();	
	$sectionchecked			= "";
	$role_name 				= "";
	$txtConfirmPassword     = "";
	if(isset($_GET) && isset($_GET['edit']))
	{
		$caption_txt	= "Edit";
		$edit_flag		= true;
		$edit_id		= required_param('edit', PARAM_INT);
		$edit_data		= get_record('users', 'id', $edit_id);

		$txtFirstName			= $edit_data->first_name;
		$txtLastName			= $edit_data->last_name;				
		$txtEmail				= $edit_data->email;
		$txtPhone				= $edit_data->phone;
		$txtUsername			= $edit_data->username;		
		$role_name				= $edit_data->role_name;		
		$chkAllowedSections		= explode(",", $edit_data->allowed_sections);				
	}
    $sql = "select * from ".$CFG->prefix."section where parent_id=0 AND status='active'";
	$section_data		= get_records_sql($sql);		
	
	foreach($section_data as $section)
	{
		if($section->section_name != 'Logout' && $section->section_name != 'Manage Site')
		{
			if($section->id != '' ){
				if(in_array($section->id, $chkAllowedSections)){		
					$checked= 'checked="checked"';
				}
				else{
					$checked = "";
				}
				$sectionchecked .="<input $checked type=\"checkbox\" name=\"chkAllowedSections[]\" value=\"".$section->id."\" >".$section->section_name."<br>";
			}
		}
	}

	print_header(get_string('cmi','cmi'));
	cmi_include_head('css');
	cmi_include_head('js');
	print_container_start();
	?>
<script type="text/javascript">
			window.addEvent('domready', function(){
					new FormCheck('frm_user');
			});
			
			function get_section(id,flag){
		   	if(id != '' && id!='admin'){
				var req = new Request({
				 method: 'get',
				 url: 'ajax_handler.php',
				 data: { 'id' : id,'flag' : 'allow_section'<?php if(isset($_GET) && isset($_GET['edit'])){ echo ','."'edit'".':' .$_GET['edit'];} ?> },
				 onRequest: function() { },
				 onComplete: function(response) { 
					$(flag).innerHTML = response;
				}
			 
			}).send();	
			}else{
			$(flag).innerHTML = '';
			}
		}
</script>

<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td VALIGN="top">
			<?php load_menu(); ?>
		</td>
	</tr>
	<tr>
		<td>

			<form method="post" action="<?php echo $CFG->wwwroot;?>/user_manage.php" class="long" id="frm_user" name="frm_user">
			<?php
				if($edit_flag)
				{
					echo '<input name="edit_mode" type="hidden" value="true"/>';
					echo '<input name="user_id"  type="hidden"  value="'.$edit_data->id.'"/>';	
				}
			?>	
			<table width="100%" border="0">
			  <tbody>
				<tr> 
				  <td width="5%"> </td>
				  <td align="center" width="90%" colspan="2"> </td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td class="DotedHeader" width="90%" colspan="2"> <?php echo $caption_txt;?> User:</td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td style="height: 21px;" width="5%"> </td>
				  <td style="height: 21px;" width="90%" colspan="2"> </td>
				  <td style="height: 21px;" width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td class="Label" width="20%"> First Name : &nbsp;</td>
				  <td width="70%"><input name="txtFirstName" value="<?php echo $txtFirstName;?>" maxlength="30" id="txtFirstName" style="width: 150px;" type="text" class="validate['required','length[3,-1]','nodigit'] txtBox" /></td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td class="Label" width="20%"> Last Name : &nbsp;</td>
				  <td width="70%"><input name="txtLastName" value="<?php echo $txtLastName;?>" maxlength="30" id="txtLastName" style="width: 150px;" type="text" class="txtBox validate['required']" /></td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
				  <td width="5%"> </td>
				</tr>	
				<tr> 
				  <td width="5%"> </td>
				  <td class="Label" width="20%"> Email : &nbsp;</td>
				  <td width="70%"><input name="txtEmail" value="<?php echo $txtEmail;?>" maxlength="30" id="txtEmail" style="width: 150px;" type="text" class="validate['required','email'] txtBox" /></td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
				  <td width="5%"> </td>
				</tr>	
				<tr> 
				  <td width="5%"> </td>
				  <td class="Label" width="20%"> Phone : &nbsp;</td>
				  <td width="70%"><input name="txtPhone" value="<?php echo $txtPhone;?>" maxlength="30" id="txtPhone" style="width: 150px;" type="text" class="txtBox validate['required','phone']" /></td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
				  <td width="5%"> </td>
				</tr>	
			  <?php if($edit_flag == false){?>
				<tr> 
				  <td width="5%"> </td>
				  <td class="Label" width="20%"> Username : &nbsp;</td>
				  <td width="70%">
				  <input name="txtUsername" value="" maxlength="30" id="txtUsername" style="width: 150px;" type="text" class="validate['required'] txtBox" /></td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
				  <td width="5%"> </td>
				</tr>	
				<tr> 
				  <td width="5%"> </td>
				  <td class="Label" width="20%"> Password : &nbsp;</td>
				  <td width="70%"><input type="password" name="Password" value="" maxlength="30" id="Password" style="width: 150px;" type="text" class="validate['required','length[5,25]'] txtBox" /></td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td class="Label" width="20%"> Confirm Password : &nbsp;</td>
				  <td width="70%"><input type="password" name="txtConfirmPassword" maxlength="30" id="txtConfirmPassword" value="<?php echo $txtConfirmPassword?>" style="width: 150px;" type="text" class="validate['required','confirm[Password]'] txtBox" /></td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
				  <td width="5%"> </td>
				</tr>
				<?php } ?>	
				<tr> 
				  <td width="5%"> </td>
				  <td class="Label" width="20%"> Role &nbsp;</td>
				  <td width="70%">
				  <select id="role" name="role" class="txtBox validate['required']" onchange="get_section(this.value,'allow_section')">
				  <?php get_role($role_name); ?>
				  </select></td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				 <td width="5%"> </td>
				  <td class="Label" width="20%" valign="top"> Allow Section &nbsp;</td>
				  <td width="100%" colspan="2" id="allow_section">
                  <?php
					if($_GET['edit'])
					{
						$sql = "SELECT allowed_sections FROM ".$CFG->prefix."users WHERE status = 'active' AND id='".$_GET['edit']."'";
						$user_section = get_record_sql($sql);
						if(!empty($user_section->allowed_sections))
						{
							$allow_arr=explode(",",$user_section->allowed_sections);
							$sql = "SELECT * FROM ".$CFG->prefix."section WHERE status = 'active' AND parent_id=0 AND section_name!='Logout' ORDER BY section_name ASC";
							$section_data = get_records_sql($sql);
							echo '<table>';
							echo '<tr><td colspan="3"><strong><font color="#F6C739">Only checked section will be shown by Mormal user</br>Note:First checked main menu and then sub menu of that section.</font></strong></td></tr>';
							echo '<tr>';
							$i=1;
							 foreach($section_data as $section)
							 {
								$html='<td><input name="section[]" type="checkbox" value="'.$section->id.'"';
								if(in_array($section->id,$allow_arr)){ $html.='checked="checked"';}
								echo $html.='/><strong>'.$section->section_name.'</strong></td>';
								$sql = "SELECT * FROM ".$CFG->prefix."section WHERE status = 'active' AND parent_id=$section->id AND section_name!='Logout' ORDER BY section_name ASC";
								$child_data = get_records_sql($sql);
								foreach($child_data as $cdata)
								{
								if($i%3=='0'){ echo '</tr><tr>';	}
								$i=$i+1;
								$html='<td><input name="section[]" type="checkbox" value="'.$cdata->id.'" ';
								if(in_array($cdata->id,$allow_arr)){ $html.='checked="checked"';}
								echo $html.='/>'.$cdata->section_name.'</td>';
								}
								echo '</tr><tr><td colspan="3">&nbsp;</td></tr><tr>';
								$i=1;
							 }
							echo '</tr>';
							echo '</table>';
					    }
					}
					?>
				  </td>
				 
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
				  <td width="5%"> </td>
				</tr>
				<!-- <tr> 
				  <td width="5%"> </td>
				  <td class="Label" width="20%"> Allowed Sections : &nbsp;</td>
				  <td width="70%"><?php //echo $sectionchecked;?></td>
				  <td width="5%"> </td>
				</tr>
				<tr> 
				  <td width="5%"> </td>
				  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
				  <td width="5%"> </td>
				</tr> -->
				<tr> 
				  <td width="5%"> </td>
				  <td width="90%" colspan="2"><input name="btnSave" id="btnSave" type="Submit" value="Save"></td>
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
