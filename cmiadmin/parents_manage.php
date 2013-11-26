<?php 
	require_once("config.php");
	define('menu_status', 'Manage News');
	$master_table_name = 'parent';	
	isLogin();
	isallow();
	$theme = current_theme();
	$edit_flag= false;
	
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
		$delete_id = required_param('delete', PARAM_INT);
		
		$parent_data = get_record('parent','id',$delete_id);

		if(isset($parent_data->user_id) && $parent_data->user_id != ''){
            /* Delete profile image, photos and videos uploaded by user */
            // Get fe_user info
            $user_info = get_record('fe_users','id',$parent_data->user_id);
            delete_images($user_info->image,'personal',true);

			// Delete from Gallery 

			$gallery_data = get_records('gallery','user_id',$parent_data->user_id);

			if(!empty($gallery_data)){
				foreach($gallery_data as $gallery){
                    // Delete each video/image from users gallery
                    if($gallery->type =="image") {
                        delete_images($gallery->name,'gallery',true);
                    } else {
                        delete_images($gallery->name,'gallery',false);
                    }

					$gallery_delete = "DELETE from ".$CFG->prefix."gallery where id = ".$gallery->id;
					execute_sql($gallery_delete,false);
				}
			}

			// Delete from Message 
				
			$message_sql = "select * from ".$CFG->prefix."messages where receiver_id = ".$parent_data->user_id." OR sender_id = ".$parent_data->user_id;

			$message_data = get_records_sql($message_sql); 

			if(!empty($message_data)){
				foreach($message_data as $message){
					
					$message_delete = "DELETE  from ".$CFG->prefix."messages where id = ".$message->id;
					execute_sql($message_delete,false);
				}
			}
			
			
					
			/* Delete from achievement */
			$achievement_delete = "DELETE  from ".$CFG->prefix."achievement where user_id = ".$parent_data->user_id;
			execute_sql($achievement_delete,false);
			
			/* Delete from blog */
			$blog_delete = "DELETE  from ".$CFG->prefix."blog where user_id = ".$parent_data->user_id;
			execute_sql($blog_delete,false);
		
			/* Delete from blog_comment */
//			$blog_comment_delete = "DELETE  from ".$CFG->prefix."blog_comment where user_id = ".$parent_data->user_id;
//			execute_sql($blog_comment_delete,false);

			/* Delete from save_search */
			$save_search_delete = "DELETE  from ".$CFG->prefix."save_search where user_id = ".$parent_data->user_id;
			execute_sql($save_search_delete,false);
			
			/* Delete from friends */
		   $friends_delete = "DELETE  from ".$CFG->prefix."friends where (user_id = ".$parent_data->user_id." || friend_id = ".$parent_data->user_id.")";
			execute_sql($friends_delete,false);
			
			/* Delete from parent */
			$student_delete = "DELETE  from ".$CFG->prefix."parent where user_id = ".$parent_data->user_id;
			execute_sql($student_delete,false);
			
			/* Delete from fe_users */
			$fe_users_delete = "DELETE  from ".$CFG->prefix."fe_users where id = ".$parent_data->user_id;
			execute_sql($fe_users_delete,false);
			$message = "parent Deleted Successfully !!";	
		}
		
		
	}

	if(isset($_GET) && isset($_GET['deleteall']))
	{
		$ids = explode(',',optional_param('deleteall', PARAM_RAW));
		foreach($ids as $value){
			$delete_record = new object();
			$delete_id = $value;
			$parent_data = get_record('parent','id',$delete_id);

			if(isset($parent_data->user_id) && $parent_data->user_id != ''){


                /* Delete profile image, photos and videos uploaded by user */
                // Get fe_user info
                $user_info = get_record('fe_users','id',$parent_data->user_id);
                delete_images($user_info->image,'personal',true);

				// Delete from Gallery 

				$gallery_data = get_records('gallery','user_id',$parent_data->user_id);

				if(!empty($gallery_data)){
					foreach($gallery_data as $gallery){
                        // Delete each video/image from users gallery
                        if($gallery->type =="image") {
                            delete_images($gallery->name,'gallery',true);
                        } else {
                            delete_images($gallery->name,'gallery',false);
                        }

						$gallery_delete = "DELETE from ".$CFG->prefix."gallery where id = ".$gallery->id;
						execute_sql($gallery_delete,false);
					}
				}

				// Delete from Message 
					
				$message_sql = "select * from ".$CFG->prefix."messages where receiver_id = ".$parent_data->user_id." OR sender_id = ".$parent_data->user_id;

				$message_data = get_records_sql($message_sql); 

				if(!empty($message_data)){
					foreach($message_data as $message){
						
						$message_delete = "DELETE  from ".$CFG->prefix."messages where id = ".$message->id;
						execute_sql($message_delete,false);
					}
				}
			/* Delete from achievement */
			$achievement_delete = "DELETE  from ".$CFG->prefix."achievement where user_id = ".$parent_data->user_id;
			execute_sql($achievement_delete,false);
			
			/* Delete from blog */
			$blog_delete = "DELETE  from ".$CFG->prefix."blog where user_id = ".$parent_data->user_id;
			execute_sql($blog_delete,false);
		
			/* Delete from blog_comment */
//			$blog_comment_delete = "DELETE  from ".$CFG->prefix."blog_comment where user_id = ".$parent_data->user_id;
//			execute_sql($blog_comment_delete,false);

			/* Delete from save_search */
			$save_search_delete = "DELETE  from ".$CFG->prefix."save_search where user_id = ".$parent_data->user_id;
			execute_sql($save_search_delete,false);
			
			/* Delete from friends */
		   $friends_delete = "DELETE  from ".$CFG->prefix."friends where (user_id = ".$parent_data->user_id." || friend_id = ".$parent_data->user_id.")";
			execute_sql($friends_delete,false);
			
			/* Delete from parent */
			$student_delete = "DELETE  from ".$CFG->prefix."parent where user_id = ".$parent_data->user_id;
			execute_sql($student_delete,false);
			
			/* Delete from fe_users */
			$fe_users_delete = "DELETE  from ".$CFG->prefix."fe_users where id = ".$parent_data->user_id;
			execute_sql($fe_users_delete,false);
			$message = "parent Deleted Successfully !!";

			
				
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

		if(update_record('parent', $record)){
		
		    $fe_id=get_field('parent','user_id','id',$user_id);
			$record->id		= $fe_id;
			update_record('fe_users', $record);
			//$status = str_replace('e','a',$status);
			cmi_add_to_log('parent',$status,$status.'d parent id: '.$user_id);
			$message = "parent Status Updated Successfully !!";
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
		
			if(update_record('parent', $delete_record)){
			
				$fe_id=get_field('parent','user_id','id',$value);
				$delete_record->id		= $fe_id;
				update_record('fe_users', $delete_record);
				//$status = str_replace('e','a',$status);
				cmi_add_to_log('parent',$status,$status.'d parent id: '.$value);
				$message = "parent Status Updated Successfully !!";		
			}
		}
	}	
	
	// Carry on with the user listing
   $columns = array("check",'id', 'first_name','date_of_birth', 'educational_interest', 'address', 'goals_in_life', 'expectation_from_s4c');
	
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
			$$column = "<a href=\"parents_manage.php?sort=$column&amp;dir=$columndir&amp;search=$search\">".$string[$column]."</a>$columnicon";
		}
		else{
			$$column = "$string[$column]";
		}
		
    }

	$sort_term    = $sort." ".$dir;				
	$searchfields	= array("first_name");
	$extraselect = " status != 'delete' ";
	$email='';

	if($search != ''){
		$extraselect .= " AND first_name like '%".$search."%'";
	}
	if($search_id != ''){
		$extraselect .= " AND id = '".$search_id."'";
	}
	if($txtsearch_name!='')
	{
		
		$txtsearch1 = preg_replace ("/\s+/", " ", trim($txtsearch_name));
		$txtsearch1 = explode(" ",$txtsearch1);
		if(isset($txtsearch1[1]))
			{
			$extraselect .=" AND (first_name LIKE '%".$txtsearch1[0]."%' || last_name LIKE '%".$txtsearch1[1]."%' || first_name LIKE '%".$txtsearch1[1]."%' || last_name LIKE '%".$txtsearch1[0]."%')";
			}else{
			$extraselect .=" AND (first_name LIKE '%".$txtsearch_name."%' || last_name LIKE '%".$txtsearch_name."%')";
			}
			
	}
	
	if($txtsearch_address!='')
	{
		//$extraselect .= " AND address LIKE '%".$txtsearch_address."%'";
		$sql = "SELECT * FROM {$CFG->prefix}city WHERE name like '%".$txtsearch_address."%'";
		$city_data = get_records_sql($sql);
		
	    if($city_data)
		{
			$city_ids = array();
			foreach($city_data as $city)
			{
				if(in_array($city->id,$city_ids))
				{
					//continue
				}
				else
				{
					$city_ids[] = $city->id;
				}
			}
			$c_ids = implode(',',$city_ids);
			
			if(!empty($c_ids)){
			   $city_id = " || city IN(".$c_ids.")";
			}
		}
		$sql = "SELECT * FROM {$CFG->prefix}state WHERE name like '%".$txtsearch_address."%'";
		$state_data = get_records_sql($sql);
		
	    if($state_data)
		{
			$state_ids = array();
			foreach($state_data as $state)
			{
				if(in_array($state->id,$state_ids))
				{
					//continue
				}
				else
				{
					$state_ids[] = $state->id;
				}
			}
			$s_ids = implode(',',$state_ids);
			if(!empty($s_ids)){
			   $state_id = " || state IN(".$s_ids.")";
			}
		}
	
	$extraselect .=" AND (address LIKE '%".$txtsearch_address."%' || zip_code LIKE '%".$txtsearch_address."%' $city_id $state_id)";
		
	}
	$user_data = get_cmi_records('parent', true, '*', $search, $searchfields, $extraselect, $sort_term, $page, $perpage);
	$user_count = get_cmi_records('parent', false, '', $search, $searchfields, $extraselect);
	
	 if (!$user_data) {
			$match = array();
			//print_heading(get_string('norecordsfound'));
			$table = NULL;

		} else {

		  $table->head = array ($check,$id, $first_name,"<a href='JavaScript:void(0);'>Email</a>", $date_of_birth, $address,"","","");
		  $table->align = array ("center", "center", "center", "center","Center");
		  $table->width = "100%";

				foreach ($user_data as $data) 
				{
						$status = 'active';
						$image = 'delete1.png';
						if($data->status == 'active'){
							$status = 'inactive';
							$image = 'add1.png';
				}

				$editbutton = get_buttons('Edit Membership ',"membership_form.php?edit=$data->id","theme/$theme/images/edit.gif","rel='lightbox[get_edit_form_$data->id 75% 90%]'");
				
				$disablebutton = get_buttons('Active/inactive',"parents_manage.php?active_inactive=$data->id&status=$status","theme/$theme/images/$image");

				$deletebutton = get_buttons('Delete',"#","theme/$theme/images/delete.png","onclick=\"confirm_delete($data->id); return false;\"");					
				
				$parent = get_record('fe_users','id',$data->user_id);
				$email = (isset($parent->email))?$parent->email:'';
				$table->data[] = array ("<input type=\"checkbox\" class=\"check-me\" id=\"deletecheck\" name=\"checkbox[]\" 	value=\"".$data->id."\" >",
					"$data->id",
					"$data->first_name",
					"$email",
					date('d/m/Y',strtotime($data->date_of_birth)),
					"$data->address",
					"<a href='parent_detail.php?id=".$data->id."'>View Detail</a>",
                    "<a href='parent_detail.php?id=".$data->id."&action=edit'>Edit Detail</a>",
					$disablebutton . $deletebutton
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
				call_alert_confirm("Are you sure you want to delete this record?",'parents_manage.php?delete='+val);
					
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
					call_alert_confirm("Are you sure you want to delete selected Event?",'parents_manage.php?deleteall='+tot);
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
					call_alert_confirm("Are you sure you want to mark selected records "+val+"?","parents_manage.php?"+val+"="+tot);
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
						<td><input name="btnReset" id="btnReset" type="reset" value="Clear Search" onClick="location.href='parents_manage.php'"></td>
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
								<a onclick="confirm_deleteall(); return false;" href="parents_manage.php?deleteall=1">Delete All</a>
									<a id="active" onclick="active_inactive(this.id); return false;" href="parents_manage.php?statusall=active"><img src="theme/'.$theme.'/images/add1.png" alt="active"/></a>
									<a id="inactive" onclick="active_inactive(this.id); return false;" href="parents_manage.php?statusall=inactive"><img src="theme/'.$theme.'/images/delete1.png" alt="active"/></a>
								</div>';
						print_paging_bar($user_count, $page, $perpage,
										 "parents_manage.php?sort=$sort&amp;dir=$dir&amp;search=$search&amp;".$searchdescription."perpage=$perpage&amp;");
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
