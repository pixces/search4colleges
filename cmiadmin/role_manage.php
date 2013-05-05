<?php 
	require_once("config.php");
	isLogin();
	isallow();
	$theme = current_theme();
	$edit_flag= false;
	$sort         = optional_param('sort', 'rolename', PARAM_TEXT);
    $dir          = optional_param('dir', 'ASC', PARAM_ALPHA);
    $page         = optional_param('page', 0, PARAM_INT);	
	$perpage      = $CFG->perpage; // how many per page
	$search		  = optional_param('search', '', PARAM_RAW);
	$search_email = optional_param('email', '', PARAM_RAW);
	$search_username = optional_param('username', '', PARAM_RAW);

	$message	= "";	
		
	if(isset($_POST['updaterole'])){
		$hiddenid = optional_param('hiddenid','', PARAM_TEXT);	
		$rolename = optional_param('rolename','', PARAM_RAW);
		$rolename = str_replace(" ","_",strtolower($rolename));		
		
		$updateproduct = new object();
	
		$updateproduct->id = $hiddenid;
		$updateproduct->rolename = $rolename;
		
		$message = '';
		if(!empty($updateproduct)){
			$update = update_record('role', $updateproduct);
			if($update){
			  $message =  '<div>'.get_string("roleupdated","cmi").'</div>';
			}
			else{
				$message = '';
			}
		}
	}
  
	if(isset($_GET) && isset($_GET['delete']))
	{
		$delete_record = new object();
		$delete_record->id = required_param('delete', PARAM_INT);
		$delete_record->status = 'delete';
		
		if(update_record('role', $delete_record)){
			cmi_add_to_log('Role Management','Delete','Deleted role id='.$_GET['delete']);
			$message = "Role Deleted Successfully !!";		
		}
	}

	if(isset($_GET) && isset($_GET['deleteall']))
	{
		$ids = explode(',',optional_param('deleteall', PARAM_RAW));
		foreach($ids as $value){
			$delete_record = new object();
			$delete_record->id = $value;
			$delete_record->status = 'delete';
		
			if(update_record('role', $delete_record)){
				cmi_add_to_log('role','Delete','Deleted role id='.$value);
				$message = "Role Deleted Successfully !!";		
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

		if(update_record('role', $record)){
			//$status = str_replace('e','a',$status);
			cmi_add_to_log('Role Management',$status,$status.'d status id: '.$user_id);
			$message = "Role Status Updated Successfully !!";
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
		
			if(update_record('role', $delete_record)){
				//$status = str_replace('e','a',$status);
				cmi_add_to_log('Role Management',$status,$status.'d status id: '.$value);
				$message = "Role Status Updated Successfully !!";		
			}
		}
	}
	if(isset($_POST) && !empty($_POST) && !isset($_POST['search']) && !isset($_POST['delete_flag']) && !isset($_POST['updaterole']))
	{
		$rolename			= optional_param('rolename', PARAM_TEXT);
		$rolename 			= str_replace(" ","_",strtolower($rolename));
		$added_date 		= time();
		$allowed_sections	= "";

		if(!empty($chkAllowedSections))
		{
			$allowed_sections =implode(',',$chkAllowedSections);
		}
		
		$role = new object();
		
		$role->rolename	= $rolename;
		$role->added_date	= $added_date;

		if(!empty($role->rolename))
		{
			if(isset($_POST['edit_mode']) && $_POST['edit_mode'] == "true")
			{
				//$user_id = required_param('user_id', PARAM_TEXT);
				//$users->id = $user_id;
				if(update_record('role', $role)){
					cmi_add_to_log('role Management','Update','role id='.$_GET['edit']);
					$message = "Role Details Updated Successfully !!";
				}

			}
			else
			{		
				if($new = insert_record('role', $role)){
					cmi_add_to_log('Role Management','Insert','New role '.$new.' added');
					$message = "Role Created Successfully !!";
				}
			}
		}
	}
	
	
	// Carry on with the user listing
    $columns = array("check","id", "rolename");
	
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
			$$column = "<a href=\"role_manage.php?sort=$column&amp;dir=$columndir&amp;search=$search\">".$string[$column]."</a>$columnicon";
		}
		else{
			$$column = "$string[$column]";
		}
		
    }

	$sort_term    = $sort." ".$dir;				
	$searchfields = array("rolename");
	$extraselect = " status !='delete' ";

	if($search_email != ''){
		$extraselect .= " AND email = '".$search_email."'";
	}
	if($search_username != ''){
		$extraselect .= " AND username = '".$search_username."'";
	}
	$user_data = get_cmi_records('role', true, '*', $search, $searchfields, $extraselect, $sort_term, $page, $perpage);
	$user_count = get_cmi_records('role', false, '', $search, $searchfields, $extraselect);
	
	 if (!$user_data) {
			$match = array();
			//print_heading(get_string('norecordsfound'));
			$table = NULL;

		} else {

		  $table->head = array ($check,$id, $rolename,"", "", "");
		  $table->align = array ("left", "left", "left", "left", "left", "center", "center", "center", "center");
		  $table->width = "100%";
			foreach ($user_data as $data) {
					$status = 'active';
					$image = 'delete1.png';
					if($data->status == 'active'){
						$status = 'inactive';
						$image = 'add1.png';
					}
				$editbutton = get_buttons('Edit role',"role_form.php?edit=$data->id","theme/$theme/images/edit.gif");
				
				$disablebutton = get_buttons('Active/inactive',"role_manage.php?active_inactive=$data->id&status=$status","theme/$theme/images/$image");

				$deletebutton = get_buttons('Delete',"#","theme/$theme/images/delete.png","onclick=\"confirm_delete($data->id); return false;\"");
				$dataname = ucwords(str_replace("_"," ",$data->rolename));
								
				$table->data[] = array ("<input type=\"checkbox\" class=\"check-me\" id=\"deletecheck\" name=\"checkbox[]\" value=\"".$data->id."\" >",
									"$data->id",
									"$dataname",
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
				call_alert_confirm("Are you sure you want to delete this record?",'role_manage.php?delete='+val);
					
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
					call_alert_confirm("Are you sure you want to delete selected role?",'role_manage.php?deleteall='+tot);
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
					call_alert_confirm("Are you sure you want to mark selected records "+val+"?","role_manage.php?"+val+"="+tot);
				else{
					Sexy.error("Select atleast one record to mark "+val+"!"); 
					return false;
				}
			}
			
			
		</script>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td VALIGN="top" width="200">
			<?php load_menu(); ?>
		</td>
	</tr>
	<tr>
		<td>

			<div class="DotedHeader" width="70%">Manage role:</div><br>
			<a href="role_form.php" style="float:right;margin-right:60px;" class="clearfix"><img src="<?php echo "theme/$theme/images/add.png" ?>" /></a><br>

		<?php notify($message);?>
		<form method="post" name="frm_search" id="frm_search" action="role_manage.php">
		<table width="100%">
             <tbody>
			 <tr>
             <td width="5px"></td>
             <td class="Label" align="center" width="90%">
			 
                <table>
					<tr>
						<td>Name: </td>
						<td><input name="search" id="search" class="txtBox" value="<?php echo $search;?>" type="text"></td>
						<td><input name="btnSearch" id="btnSearch" type="Submit" value="Search"></td>
						<td><input name="btnSearch" id="btnSearch" type="Reset" value="Clear"></td>
					</tr>
				</table>
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
						$searchname = $searchdescription = '';
						if($search_username != ''){
							$searchdescription = "description=$description&amp;";
						}
						echo '<div align="left">
								<a onclick="confirm_deleteall(); return false;" href="role_manage.php?deleteall=1">Delete All</a>
									<a id="active" onclick="active_inactive(this.id); return false;" href="role_manage.php?roleall=active"><img src="theme/'.$theme.'/images/add1.png" alt="active"/></a>
									<a id="inactive" onclick="active_inactive(this.id); return false;" href="role_manage.php?roleall=inactive"><img src="theme/'.$theme.'/images/delete1.png" alt="active"/></a>
								</div>';
						print_paging_bar($user_count, $page, $perpage,
										 "role_manage.php?sort=$sort&amp;dir=$dir&amp;search=$search&amp;".$searchdescription."perpage=$perpage&amp;");
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