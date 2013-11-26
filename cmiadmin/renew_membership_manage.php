<?php 
	require_once("config.php");
	define('menu_status', 'Manage Schools');
	$master_table_name = 'schools';	
	isLogin();
	isallow();
	$theme = current_theme();
	$edit_flag= false;
	$page_var='';
	$display='';
	
	$page_name =  substr(str_replace(array(str_replace('','',$CFG->dirroot),''),'',($_SERVER['SCRIPT_FILENAME'])),'1');
	$page_form = str_replace('manage','form',$page_name);
	$page_title = ucwords(str_replace('_',' ',str_replace('manage.php','',$page_name)));
	
	$sort         = optional_param('sort', 'id', PARAM_TEXT);
    $dir          = optional_param('dir', 'DESC', PARAM_ALPHA);
    $page         = optional_param('page', 0, PARAM_INT);	
	$perpage      = $CFG->news_letters_per_page=25;        // how many per page
	$search		  = optional_param('search_title', '', PARAM_RAW);
	$search_id	  = optional_param('id', '', PARAM_RAW);
	$search_username = optional_param('username', '', PARAM_RAW);
	$txtsearch_name	= optional_param('txtsearch_name', '', PARAM_RAW);
	$txtsearch_address=optional_param('txtsearch_address','',PARAM_RAW);

	$message	= "";	
	
  
	if(isset($_GET) && isset($_GET['delete']))
	{
		$delete_record = new object();
		$delete_record->id = required_param('delete', PARAM_INT);
		$delete_record->status = 'delete';
		
		if(update_record('schools', $delete_record)){
			cmi_add_to_log('schools','Delete','Deleted membership id='.$_GET['delete']);
			$message = "schools Deleted Successfully !!";		
		}
	}

	if(isset($_GET) && isset($_GET['deleteall']))
	{
		$ids = explode(',',optional_param('deleteall', PARAM_RAW));
		foreach($ids as $value){
			$delete_record = new object();
			$delete_record->id = $value;
			$delete_record->status = 'delete';
		
			if(update_record('schools', $delete_record)){
				cmi_add_to_log('schools','Delete','Deleted schools id='.$value);
				$message = " schools Deleted Successfully !!";		
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

		if(update_record('schools', $record)){
			//$status = str_replace('e','a',$status);
			cmi_add_to_log('schools',$status,$status.'d schools id: '.$user_id);
			$message = "schools Status Updated Successfully !!";
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
		
			if(update_record('schools', $delete_record)){
				//$status = str_replace('e','a',$status);
				cmi_add_to_log('schools',$status,$status.'d schools id: '.$value);
				$message = "schools Status Updated Successfully !!";		
			}
		}
	}
	if(isset($_GET) && isset($_GET['active_inactive']))
	{
		$status_id			= required_param('active_inactive', PARAM_INT);
		$status				= required_param('status', PARAM_TEXT);		
		update_status_record($status_id,$status,$master_table_name);	
	}
	
	if(isset($_GET) && isset($_GET['featured']))
	{
		$id			= required_param('id', PARAM_INT);

		$update_mode =new object();
		$update_mode->id =$id;
		$update_mode->featured ='featured';	
		if($update_mode)
		{
			update_record($master_table_name, $update_mode);
		}
		
		$sql = "select * from {$CFG->prefix}schools where status='active' and featured = 'featured' and id!=$id"  ;
		$check_featured=get_records_sql($sql);			
		if(($check_featured))
		{
			foreach($check_featured as $records)
			{				
				$add_mode	= new object();
				$add_mode->id = $records->id;
				$add_mode->featured = 'no';
				update_record('schools',$add_mode);	
			}	
		}
		
	}
	if(isset($_GET) && isset($_GET['view_hide_school_id']))
	{
		$id					= required_param('view_hide_school_id', PARAM_INT);
		$display			= required_param('view_hide', PARAM_RAW);

		$update_mode =new object();
		$update_mode->id =$id;
		$update_mode->display =$display;	
		if($update_mode)
		{
			update_record($master_table_name, $update_mode);
		}					
		
	}
	
	// Carry on with the user listing
  $columns = array("check",'id','school_name','Website Name', 'educational_interest', 'address', 'goals_in_life');
	
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
			$$column = "<a href=\"renew_membership_manage.php?sort=$column&amp;dir=$columndir&amp;search=$search\">".$string[$column]."</a>$columnicon";
		}
		else{
			$$column = "$string[$column]";
		}
		
    }

	$sort_term    = $sort." ".$dir;				
	$searchfields	= array("school_name");
	$extraselect = " status != 'delete' ";
	$email='';

	if($search != ''){
		$extraselect .= " AND school_name like '%".$search."%'";
	}
	if($search_id != ''){
		$extraselect .= " AND id = '".$search_id."'";
	}
	if($txtsearch_name!='')
	{
		$extraselect .=" AND school_name LIKE '%".$txtsearch_name."%'";
	}
	if($txtsearch_address!='')
	{
		$extraselect .= " AND address LIKE '%".$txtsearch_address."%'";
	}
	// filter expired college from feuser
	    $currentDate = time();
	    $dateData = get_records_sql("SELECT id,added_date FROM fe_users WHERE status='active' and id NOT IN (SELECT school_id FROM school_membership WHERE status='active' and expiryon> ".time().")");
		$exp_date = array();
		foreach($dateData as $dateDataval)
		{
			$login30Day = strtotime('+30days', $dateDataval->added_date);
			if($currentDate > $login30Day){
			$exp_date[] = $dateDataval->id;
			}
		}
		//print_r($exp_date);
	// filter end 
	$user_data = get_cmi_records('schools', true, '*', $search, $searchfields, $extraselect, $sort_term, $page, $perpage);
	$user_count = get_cmi_records('schools', false, '', $search, $searchfields, $extraselect);
	
	 if (!$user_data) {
			$match = array();
			//print_heading(get_string('norecordsfound'));
			$table = NULL;

		} else {

			$table->head = array ($id,$school_name,"<a href='JavaScript:void(0);'>Email</a>",'Website Name', $address,"");
			$table->align = array ("center", "center", "center", "center", "center" ,"center");
		  $table->width = "100%";

				foreach ($user_data as $data) 
				{
						$status = 'active';
						$image = 'delete1.png';
						if($data->status == 'active'){
							$status = 'inactive';
							$image = 'add1.png';
				}
					$featured = 'Featured';
					$featured_image = 'unavailable.png';
				if($data->featured == 'featured')
			{
				$featured = 'Not Featured';
				$featured_image = 'available.png';
			}
				
				$view_hide ='1';
				$view_hide_image = 'view_icon.jpg';
				if($data->display == '1' && $data->id)
				{
					$view_hide = '0';
					$view_hide_image = 'hide_icon.jpg';
				}	

				$editbutton = get_buttons('Edit Membership ',"membership_form.php?edit=$data->id","theme/$theme/images/edit.gif","rel='lightbox[get_edit_form_$data->id 75% 90%]'");
				
				$disablebutton = get_buttons('Active/inactive',"renew_membership_manage.php?active_inactive=$data->id&status=$status","theme/$theme/images/$image");
				$featured_link = get_buttons("$featured","$page_name?id=$data->id&featured=yes&$page_var&amp;page=$page"," theme/$theme/images/$featured_image");
			
			
				$viewhide = get_buttons('View/Hide to users',"$page_name?view_hide_school_id=$data->id&view_hide=$view_hide&amp;$page_var&amp;page=$page"," theme/$theme/images/$view_hide_image");

				$deletebutton = get_buttons('Delete',"#","theme/$theme/images/delete.png","onclick=\"confirm_delete($data->id); return false;\"");					
				
				$schools = get_record('fe_users','id',$data->user_id);
				$email = (isset($schools->email))?$schools->email:'';
				if(in_array($data->user_id,$exp_date))
				{
				   $table->rowclass[] = "redclass";
				}else{
				$table->rowclass[] = "";
				}
				$table->data[] = array (
					"$data->id",
					"$data->school_name",					
					"$email",
					"$data->web_url",
					"$data->address",
					"<a href='renew_membership.php?id=".$data->id."' rel='lightbox[set_".$data->id." 75% 90%]'>Renew/Add membership</a>",
				);
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
			
			function confirm_delete(val)
			{
				call_alert_confirm("Are you sure you want to delete this record?",'renew_membership_manage.php?delete='+val);
					
			}
			function confirm_send(val)
			{
				call_alert_confirm("Are you sure you want to Send this News Letter?",'show_newsletters.php?show_id='+val);
					
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
					call_alert_confirm("Are you sure you want to delete selected Event?",'renew_membership_manage.php?deleteall='+tot);
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
					call_alert_confirm("Are you sure you want to mark selected records "+val+"?","renew_membership_manage.php?"+val+"="+tot);
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
			<div class="DotedHeader" width="70%">Manage <?php echo $page_title;?>:</div>
			<!--<a href="membership_form.php" style="float:right;margin-right:60px;" rel="lightbox[set2 75% 90%]" class="clearfix" title="Add <?php echo $page_title; ?>"><img src="<?php echo "theme/$theme/images/add.png" ?>" /></a><br>-->
		         
        <br>
		<?php notify($message);?>
		<form method="post" name="frm_search" id="frm_search" action="<?php echo $page_name; ?>">
		<table width="100%">
             <tbody>
			 <tr>
             <td width="5px"></td>
             <td class="Label" align="center" width="90%">
			 
                <table>
					<tr>
						<td>Name&nbsp;:&nbsp; </td>
						<td>
							<input name="txtsearch_name" id="txtsearch_name" class="txtBox" value="<?php echo $txtsearch_name;?>" type="text">
						</td>
					</tr>
					<tr>
						<td>Address&nbsp;:&nbsp; </td>
						<td>
							<input name="txtsearch_address" id="txtsearch_address" class="txtBox" value="<?php echo $txtsearch_address;?>" type="text">
						</td>
					</tr>
					<tr>
						<td><input name="btnSearch" id="btnSearch" type="submit" value="Search"></td>
						<td><input name="btnReset" id="btnReset" type="reset" value="Clear Search" onClick="location.href='renew_membership_manage.php'"></td>
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
						print_paging_bar($user_count, $page, $perpage,
										 "renew_membership_manage.php?sort=$sort&amp;dir=$dir&amp;search=$search&amp;".$searchdescription."perpage=$perpage&amp;");
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
