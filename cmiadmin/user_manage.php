<?php 
	require_once("config.php");
	isLogin();

	if(isset($_POST['change_password']) && ($_POST['change_password']=='true'))
	{
	}
	else
	{
		isallow();
	}

	$theme = current_theme();
	$edit_flag= false;
	$sort         = optional_param('sort', 'first_name', PARAM_TEXT);
    $dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
    $page         = optional_param('page', 0, PARAM_INT);	
	$perpage      = $CFG->perpage; // how many per page
	$search		  = optional_param('search', '', PARAM_RAW);
	$search_email = optional_param('email', '', PARAM_RAW);
	$search_username = optional_param('username', '', PARAM_RAW);

	$message	= "";		
  
	if(isset($_GET) && isset($_GET['delete']))
	{
		$delete_record = new object();
		$delete_record->id = required_param('delete', PARAM_INT);
		$delete_record->status = 'delete';
		
		if(update_record('users', $delete_record)){
			cmi_add_to_log('user','Delete','Deleted user id='.$_GET['delete']);
			$message = "User deleted Successfully !!";		
		}
	}

	if(isset($_GET) && isset($_GET['deleteall']))
	{
		$ids = explode(',',optional_param('deleteall', PARAM_RAW));
		foreach($ids as $value){
			$delete_record = new object();
			$delete_record->id = $value;
			$delete_record->status = 'delete';
		
			if(update_record('users', $delete_record)){
				if(isset($_GET['delete']))
				{
					cmi_add_to_log('user','Delete','Deleted user id='.$_GET['delete']);
				}
				else
				{
					cmi_add_to_log('user','Delete','Deleted user id='.$_GET['deleteall']);
				}
				$message = "User Deleted Successfully !!";		
			}
		}
	}
	
	if(isset($_GET) && isset($_GET['active_inactive']))
	{
		$user_id			= required_param('active_inactive', PARAM_INT);
		$status				= required_param('status', PARAM_TEXT);

		$record				= new object();
		$record->id			= $user_id;
		$record->status		= $status;

		if(update_record('users', $record)){
			//$status = str_replace('e','a',$status);
			cmi_add_to_log('user',$status,$status.'d user id: '.$user_id);
			$message = "User Status Updated Successfully !!";
		}
	}
	if(isset($_GET) && isset($_GET['inactive']) || isset($_GET['active']))
	{
		$status = '';
		$ids = optional_param('inactive','',PARAM_RAW);
		$idexploded = explode(',',$ids);
		$status = 'inactive';
		if($ids == ''){
			$ids = optional_param('active','',PARAM_RAW);
			$idexploded = explode(',',$ids);
			$status = 'active';
		}
		foreach($idexploded as $value){
			$delete_record = new object();
			$delete_record->id = $value;
			$delete_record->status = $status;
		
			if(update_record('users', $delete_record)){
				cmi_add_to_log('user',$status,$status.'d user id: '.$value);
				$message = "User Status Updated Successfully !!";		
			}
		}
	}

	if(isset($_POST['change_password']))
	{
		$user_id		= required_param('user_id', PARAM_TEXT);
		$txtPassword	= required_param('Password', PARAM_RAW);

		$users				= new object();
		$users->id			= $user_id;
		$users->password	= md5($txtPassword);

		if(update_record('users', $users)){
			cmi_add_to_log('user','change_password','Pasword updated for user whose id='.$user_id);
			$message = "User password changed Successfully !!";
		}	
	}

	if(isset($_POST) && !empty($_POST) && !isset($_POST['search']) && !isset($_POST['delete_flag']) && !isset($_POST['change_password']))
	{
		$txtFirstName			= required_param('txtFirstName', PARAM_TEXT);
		$txtLastName			= optional_param('txtLastName', '', PARAM_RAW);		
		$txtEmail				= required_param('txtEmail', PARAM_RAW);
		$txtPhone				= required_param('txtPhone', PARAM_RAW);
		$txtRole				= required_param('role', PARAM_RAW);
		$section				= optional_param('section', PARAM_RAW);
		$added_date 			= time();
		
		/*$variable = "allowed_section_".$txtRole;

		if(isset($CFG->$variable))
		{
			$allowed_section = $CFG->$variable;
		}
		else
		{
			$allowed_section = '';
		}*/

		$users = new object();
		$users->first_name			= $txtFirstName;
		$users->last_name			= $txtLastName;
		$users->email				= $txtEmail;
		$users->phone				= $txtPhone;	
		$users->role_name			= $txtRole;
		//$users->allowed_sections	= $allowed_section;
		$users->allowed_sections	= implode(",",$section);
		$users->added_date			= $added_date;
		//print_r($users);

		if(!empty($users->first_name))
		{
			if(isset($_POST['edit_mode']) && $_POST['edit_mode'] == "true")
			{
				$user_id = required_param('user_id', PARAM_TEXT);
				$users->id = $user_id;
				if(update_record('users', $users)){
					cmi_add_to_log('user','Update','user data is updated of user id='.$user_id);
					$message = "User details updated Successfully !!";
				}

			}
			else
			{		
				$txtUsername		= required_param('txtUsername', PARAM_RAW);
				$txtPassword		= required_param('Password', PARAM_RAW);
				$users->username	= $txtUsername;
				$users->password	= md5($txtPassword);
				if($new = insert_record('users', $users)){
					cmi_add_to_log('user','Insert','New user '.$new.' added');
					$message = "User Created Successfully !!";
				}
			}
		}
	}
	
	
	// Carry on with the user listing
    $columns = array("check","id",  "username", "first_name", "last_name", "email");
	
    foreach ($columns as $column) {
		$string[$column] = get_string("$column","cmi");
		if ($sort != $column) {
			$columnicon = "";
			if ($column == "id") {
				$columndir = "DESC";
			} else {
				$columndir = "ASC";
			}
		} else {
			$columndir = $dir == "ASC" ? "DESC":"ASC";
			if ($column == "id") {
				$columnicon = $dir == "ASC" ? "up":"down";
			} else {
				$columnicon = $dir == "ASC" ? "down":"up";
			}
			$columnicon = "<img src=\"theme/$theme/images/$columnicon.gif\" alt=\"\" />";

		}
		if($column != 'check'){
			$$column = "<a href=\"user_manage.php?sort=$column&amp;dir=$columndir&amp;search=$search\">".$string[$column]."</a>$columnicon";
		}
		else{
			$$column = "$string[$column]";
		}
		
    }

	$sort_term    = $sort." ".$dir;				
	$searchfields = array("first_name");
	$extraselect = " status ='active' AND id != 1 ";

	if($search_email != ''){
		$extraselect .= " AND email = '".$search_email."'";
	}
	if($search_username != ''){
		$extraselect .= " AND username = '".$search_username."'";
	}
	$user_data = get_cmi_records('users', true, '*', $search, $searchfields, $extraselect, $sort_term, $page, $perpage);
	$user_count = get_cmi_records('users', false, '', $search, $searchfields, $extraselect);
	
	 if (!$user_data) {
			$match = array();
			//print_heading(get_string('norecordsfound'));
			$table = NULL;

		} else {

		  $table->head = array ($check,$id, $username, $first_name, $last_name, $email, "", "", "", "");
		  $table->align = array ("left", "left", "left", "left", "left", "center", "center", "center", "center");
		  $table->width = "95%";
			foreach ($user_data as $data) {
					$status = 'active';
					$image = 'delete1.png';
					if($data->status == 'active'){
						$status = 'inactive';
						$image = 'add1.png';
					}
				

				$cpbutton = get_buttons('Change Password',"user_password.php?user_id=$data->id","theme/$theme/images/password_key.png");
				
				$editbutton = get_buttons('Edit User',"user_form.php?edit=$data->id","theme/$theme/images/edit.gif");
				
				$disablebutton = get_buttons('Active/inactive',"user_manage.php?active_inactive=$data->id&status=$status","theme/$theme/images/$image");

				$deletebutton = get_buttons('Delete',"#","theme/$theme/images/delete.png","onclick=\"confirm_delete($data->id); return false;\"");
							

				$table->data[] = array ("<input type=\"checkbox\" class=\"check-me\" id=\"deletecheck\" name=\"checkbox[]\" value=\"".$data->id."\" >",
									"$data->id",
									"$data->username",
									"$data->first_name",
									"$data->last_name",
									"$data->email",
									$cpbutton,
									$editbutton,
									$disablebutton,
									$deletebutton);
			}
			
		}
?>
<?php 
	print_header(get_string('cmi','cmi'));
	cmi_include_head('css');
	cmi_include_head('js');

	print_container_start();

	?>
		<script type="text/javascript">
			window.addEvent('domready', function(){
				new FormCheck('frm_search');

				//call Motools tipz initialization
				show_tipz();
				if($('check')){ 
				 $('check').addEvent('click', function() {
					if($('check').get('rel') == 'yes' || $('check').get('rel') == 'null')
					{
						do_check = false;
						$('check').set('checked','').set('rel','no');
					}
					else{
						do_check = true;
						$('check').set('checked','true').set('rel','yes');
					}
					$$('.check-me').set('checked',do_check) 
				 });
				}
			});
			function reset_all()
			{
				$('frm_search').reset();
					
			}			
			function confirm_delete(val)
			{
				call_alert_confirm("Are you sure you want to delete this record?",'user_manage.php?delete='+val);
					
			}
			function confirm_deleteall(val)
			{
				var total = '';
				$$('.check-me').each(function(el){
					 if(el.checked == true){
						total += el.value+',';
					 }
					 
				});
				tot = total.substring(0,total.length-1);
				if(total != '')
					call_alert_confirm("Are you sure you want to delete selected user?",'user_manage.php?deleteall='+tot);
				else{
					Sexy.error('Select atleast one record to delete!'); 
					return false;
				}
			}
			function active_inactive(val)
			{
				var total = '';
				$$('.check-me').each(function(el){
					 if(el.checked == true){
						total += el.value+',';
					 }
					 
				});
				tot = total.substring(0,total.length-1);
				if(total != '')
					call_alert_confirm("Are you sure you want to mark selected records "+val+"?","user_manage.php?"+val+"="+tot);
				else{
					Sexy.error("Select atleast one record to mark "+val+"!"); 
					return false;
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
			<div class="DotedHeader" width="70%">Manage Users:</div><br>
			<a href="user_form.php" style="float:right;margin-right:60px;" class="clearfix"><img src="<?php echo "theme/$theme/images/add.png" ?>" /></a><br>
		<?php notify($message);?>
		<form method="post" name="frm_search" id="frm_search" action="user_manage.php">
		<table width="100%">
             <tbody>
			 <tr>
             <td width="5px"></td>
             <td class="Label" align="center" width="90%">
			 
                <table>
					<tr>
						<td>Name: </td>
						<td><input name="search" id="search" class="txtBox" value="<?php echo $search;?>" type="text"></td>
					</tr>
					<tr>
						<td>Email: </td>
						<td><input name="email" id="email" class="validate['email'] txtBox" value="<?php echo $search_email;?>" type="text"></td>
					</tr>
					<tr>
						<td>Username: </td>
						<td><input name="username" id="username" class="txtBox" value="<?php echo $search_username;?>" type="text"></td>
					</tr>
					<tr>
						<td><input name="btnSearch" id="btnSearch" value="Search" type="submit"> </td>
						<td><input name="btnSearch" id="btnSearch" type="Reset" value="Clear"></td>
					</tr>
				</table>
				</form>
			</td>
             <td width="5%"></td>
             </tr>
			 <tr><td>&nbsp;</td></tr>
              <tr>
             <td width="5%"></td>
             <td align="center" width="90%">
                 </td>
             <td width="5%"></td>
             </tr>
             <tr>
             <td width="5%"></td>
             <td align="center" width="90%">
                 <div>
				<?php
					if (!empty($table)) {
						print_table($table);
						$searchemail = $action = '';
						if($search_email != ''){
							$searchemail = "email=$search_email&amp;";
						}
						if($search_username != ''){
							$searchusername = "username=$search_username&amp;";
						}
						echo '<div align="left">
								<a onclick="confirm_deleteall(); return false;" href="user_manage.php?deleteall=1">Delete All</a>
									<a id="active" onclick="active_inactive(this.id); return false;" href="user_manage.php?statusall=active"><img src="theme/'.$theme.'/images/add1.png" alt="active"/></a>
									<a id="inactive" onclick="active_inactive(this.id); return false;" href="user_manage.php?statusall=inactive"><img src="theme/'.$theme.'/images/delete1.png" alt="active"/></a>
								</div>';
						print_paging_bar($user_count, $page, $perpage,
										 "user_manage.php?sort=$sort&amp;dir=$dir&amp;search=$search&amp;".$searchemail.$search_username."perpage=$perpage&amp;");
					}
					else
					{	
						//print_heading('No Records Found');
						?>
						<table width= "600">
							<tr>
								<td align="center">No Records Found</td>
							</tr>
						</table>
					<?php
					}
				?>
				</div>
             </td>
             <td width="5%"></td>
             </tr>
             <tr>
             <td width="5%"></td>
             <td width="90%"></td>
             <td width="5%"></td>
             </tr>
             </tbody></table>
			 </form>
			</td>
	</tr>
</table>
<?php
	print_container_end();

	print_footer();
?>