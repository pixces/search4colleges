<?php 
	require_once("config.php");
	define('menu_status', 'Manage News');
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
	//$search		  = optional_param('search_title', '', PARAM_RAW);
	$search_id	  = optional_param('id', '', PARAM_RAW);
	$search_username = optional_param('username', '', PARAM_RAW);
	$txtsearch_name	= optional_param('txtsearch_name', '', PARAM_RAW);
	$txtsearch_address=optional_param('txtsearch_address','',PARAM_RAW);

	$message	= "";	
	
  	$page_arr		= array(
							'sort'			=> $sort,
							'dir'			=> $dir,
							'perpage'		=> $perpage,
							'page'			=> $page,
							'txtsearch_name'=> $txtsearch_name,
							'txtsearch_address'=> $txtsearch_address,
						);	

	$page_var = http_build_query($page_arr,'','&amp;');	
	if(isset($_GET) && isset($_GET['delete']))
	{
		$delete_record = new object();
		$delete_id =  required_param('delete', PARAM_INT);
		
		$school_data = get_record('schools','id',$delete_id);

		//print_object($school_data);

		if(isset($school_data->user_id) && $school_data->user_id != ''){
			

			//Delete from Gallery 

			$gallery_data = get_records('gallery','user_id',$school_data->user_id);

			if(!empty($gallery_data)){
				foreach($gallery_data as $gallery){

					$gallery_delete = "DELETE from ".$CFG->prefix."gallery where id = ".$gallery->id;
					execute_sql($gallery_delete,false);
				}
			}

			//Delete from Message 
				
			$message_sql = "select * from ".$CFG->prefix."messages where receiver_id = ".$school_data->user_id." OR sender_id = ".$school_data->user_id;

			$message_data = get_records_sql($message_sql); 

			if(!empty($message_data)){
				foreach($message_data as $message){
					
					$message_delete = "DELETE  from ".$CFG->prefix."messages where id = ".$message->id;
					execute_sql($message_delete,false);
				}
			}

			//blog record will be deleted if he added
			
			$blog_sql = "select * from ".$CFG->prefix."blog where user_id = ".$school_data->user_id;

			$blog_data = get_records_sql($blog_sql); 

			if(!empty($blog_data)){
				foreach($blog_data as $blog){
					
					$blog_delete = "DELETE  from ".$CFG->prefix."blog where id = ".$blog->id;
					execute_sql($blog_delete,false);
				}
			}

			//blog_comment record will be deleted if he added 
			
			$blog_comment_sql = "select * from ".$CFG->prefix."blog_comment where user_id = ".$school_data->user_id;
			$blog_comment_data = get_records_sql($blog_comment_sql); 
			if(!empty($blog_comment_data)){
				foreach($blog_comment_data as $blog_comment){
					
					$blog_comment_delete = "DELETE  from ".$CFG->prefix."blog_comment where id = ".$blog_comment->id;
					execute_sql($blog_comment_delete,false);
				}
			}

			//Delete from schools_additional
			$schools_additional_delete = "DELETE from ".$CFG->prefix."schools_additional where school_id = ".$school_data->user_id;
			execute_sql($schools_additional_delete,false);
			
			//Delete from schools_brochures
			$schools_brochures_delete = "DELETE from ".$CFG->prefix."schools_brochures where school_id = ".$school_data->user_id;
			execute_sql($schools_brochures_delete,false);
			
			//Delete from schools_enquiry
			$schools_enquiry_delete = "DELETE from ".$CFG->prefix."schools_enquiry where school_id = ".$school_data->user_id;
			execute_sql($schools_enquiry_delete,false);
			
			//Delete from schools_news
			$schools_news_delete = "DELETE from ".$CFG->prefix."schools_news where school_id = ".$school_data->user_id;
			execute_sql($schools_news_delete,false);
			
			//  Delete from schools_view_count
			$schools_view_count_delete = "DELETE from ".$CFG->prefix."schools_view_count where user_id = ".$school_data->user_id;
			execute_sql($schools_view_count_delete,false);
			
			//  Delete from school_admission_details
			$school_admission_details_delete = "DELETE from ".$CFG->prefix."school_admission_details where school_id = ".$school_data->user_id;
			execute_sql($school_admission_details_delete,false);
			
			//  Delete from school_club_mm
			$school_club_mm_delete = "DELETE from ".$CFG->prefix."school_club_mm where school_id = ".$school_data->user_id;
			execute_sql($school_club_mm_delete,false);
			
			//  Delete from school_contacts
			$school_contacts_delete = "DELETE from ".$CFG->prefix."school_contacts where school_id = ".$school_data->user_id;
			execute_sql($school_contacts_delete,false);
			
			//  Delete from school_culture_campus_life
			$school_culture_campus_life_delete = "DELETE from ".$CFG->prefix."school_culture_campus_life where school_id = ".$school_data->user_id;
			execute_sql($school_culture_campus_life_delete,false);
			
			//  Delete from school_culture_campus_life_sports
			$school_culture_campus_life_sports_delete = "DELETE from ".$CFG->prefix."school_culture_campus_life_sports where school_id = ".$school_data->user_id;
			execute_sql($school_culture_campus_life_sports_delete,false);
			
			//  Delete from school_exam
			$school_exam_delete = "DELETE from ".$CFG->prefix."school_exam where school_id = ".$school_data->user_id;
			execute_sql($school_exam_delete,false);
			
			//  Delete from school_image_video
			$school_image_video_delete = "DELETE from ".$CFG->prefix."school_image_video where school_id = ".$school_data->user_id;
			execute_sql($school_image_video_delete,false);
			
			//  Delete from school_major
			$school_major_delete = "DELETE from ".$CFG->prefix."school_major where school_id = ".$school_data->user_id;
			execute_sql($school_major_delete,false);
			
			//  Delete from school_membership
			$school_membership_delete = "DELETE from ".$CFG->prefix."school_membership where school_id = ".$school_data->user_id;
			execute_sql($school_membership_delete,false);
			
			//	Delete from school_scholarship
			$school_scholarship_delete = "DELETE from ".$CFG->prefix."school_scholarship where school_id = ".$school_data->user_id;
			execute_sql($school_scholarship_delete,false);
			
			//  Delete from schools
			$schools_delete = "DELETE from ".$CFG->prefix."schools where id = ".$school_data->user_id;
			execute_sql($schools_delete,false);
			
			//  Delete from Fe_users
			$fetable_delete = "DELETE from ".$CFG->prefix."fe_users where id = ".$school_data->user_id;
			execute_sql($fetable_delete,false);
		    $message = "schools Deleted Successfully !!";	
			
		
		}
		
	}

	if(isset($_GET) && isset($_GET['deleteall']))
	{
		$ids = explode(',',optional_param('deleteall', PARAM_RAW));
		foreach($ids as $value){
			$delete_record = new object();
		$school_data = get_record('schools','id',$value);

		
		if(isset($school_data->user_id) && $school_data->user_id != ''){
			

			//Delete from Gallery 

			$gallery_data = get_records('gallery','user_id',$school_data->user_id);

			if(!empty($gallery_data)){
				foreach($gallery_data as $gallery){

					$gallery_delete = "DELETE from ".$CFG->prefix."gallery where id = ".$gallery->id;
					execute_sql($gallery_delete,false);
				}
			}

			//Delete from Message 
				
			$message_sql = "select * from ".$CFG->prefix."messages where receiver_id = ".$school_data->user_id." OR sender_id = ".$school_data->user_id;

			$message_data = get_records_sql($message_sql); 

			if(!empty($message_data)){
				foreach($message_data as $message){
					
					$message_delete = "DELETE  from ".$CFG->prefix."messages where id = ".$message->id;
					execute_sql($message_delete,false);
				}
			}

			//blog record will be deleted if he added
			
			$blog_sql = "select * from ".$CFG->prefix."blog where user_id = ".$school_data->user_id;

			$blog_data = get_records_sql($blog_sql); 

			if(!empty($blog_data)){
				foreach($blog_data as $blog){
					
					$blog_delete = "DELETE  from ".$CFG->prefix."blog where id = ".$blog->id;
					execute_sql($blog_delete,false);
				}
			}

			//blog_comment record will be deleted if he added 
			
			$blog_comment_sql = "select * from ".$CFG->prefix."blog_comment where user_id = ".$school_data->user_id;

			$blog_comment_data = get_records_sql($blog_comment_sql); 

			if(!empty($blog_comment_data)){
				foreach($blog_comment_data as $blog_comment){
					
					$blog_comment_delete = "DELETE  from ".$CFG->prefix."blog_comment where id = ".$blog_comment->id;
					execute_sql($blog_comment_delete,false);
				}
			}
			
			
			//Delete from schools_additional
			$schools_additional_delete = "DELETE from ".$CFG->prefix."schools_additional where school_id = ".$school_data->user_id;
			execute_sql($schools_additional_delete,false);
			
			//Delete from schools_brochures
			$schools_brochures_delete = "DELETE from ".$CFG->prefix."schools_brochures where school_id = ".$school_data->user_id;
			execute_sql($schools_brochures_delete,false);
			
			//Delete from schools_enquiry
			$schools_enquiry_delete = "DELETE from ".$CFG->prefix."schools_enquiry where school_id = ".$school_data->user_id;
			execute_sql($schools_enquiry_delete,false);
			
			//Delete from schools_news
			$schools_news_delete = "DELETE from ".$CFG->prefix."schools_news where school_id = ".$school_data->user_id;
			execute_sql($schools_news_delete,false);
			
			//  Delete from schools_view_count
			$schools_view_count_delete = "DELETE from ".$CFG->prefix."schools_view_count where user_id = ".$school_data->user_id;
			execute_sql($schools_view_count_delete,false);
			
			//  Delete from school_admission_details
			$school_admission_details_delete = "DELETE from ".$CFG->prefix."school_admission_details where school_id = ".$school_data->user_id;
			execute_sql($school_admission_details_delete,false);
			
			//  Delete from school_club_mm
			$school_club_mm_delete = "DELETE from ".$CFG->prefix."school_club_mm where school_id = ".$school_data->user_id;
			execute_sql($school_club_mm_delete,false);
			
			//  Delete from school_contacts
			$school_contacts_delete = "DELETE from ".$CFG->prefix."school_contacts where school_id = ".$school_data->user_id;
			execute_sql($school_contacts_delete,false);
			
			//  Delete from school_culture_campus_life
			$school_culture_campus_life_delete = "DELETE from ".$CFG->prefix."school_culture_campus_life where school_id = ".$school_data->user_id;
			execute_sql($school_culture_campus_life_delete,false);
			
			//  Delete from school_culture_campus_life_sports
			$school_culture_campus_life_sports_delete = "DELETE from ".$CFG->prefix."school_culture_campus_life_sports where school_id = ".$school_data->user_id;
			execute_sql($school_culture_campus_life_sports_delete,false);
			
			//  Delete from school_exam
			$school_exam_delete = "DELETE from ".$CFG->prefix."school_exam where school_id = ".$school_data->user_id;
			execute_sql($school_exam_delete,false);
			
			//  Delete from school_image_video
			$school_image_video_delete = "DELETE from ".$CFG->prefix."school_image_video where school_id = ".$school_data->user_id;
			execute_sql($school_image_video_delete,false);
			
			//  Delete from school_major
			$school_major_delete = "DELETE from ".$CFG->prefix."school_major where school_id = ".$school_data->user_id;
			execute_sql($school_major_delete,false);
			
			//  Delete from school_membership
			$school_membership_delete = "DELETE from ".$CFG->prefix."school_membership where school_id = ".$school_data->user_id;
			execute_sql($school_membership_delete,false);
			
			//	Delete from school_scholarship
			$school_scholarship_delete = "DELETE from ".$CFG->prefix."school_scholarship where school_id = ".$school_data->user_id;
			execute_sql($school_scholarship_delete,false);
			
			//  Delete from schools
			$schools_delete = "DELETE from ".$CFG->prefix."schools where id = ".$school_data->user_id;
			execute_sql($schools_delete,false);
			
			//  Delete from Fe_users
			$fetable_delete = "DELETE from ".$CFG->prefix."fe_users where id = ".$school_data->user_id;
			execute_sql($fetable_delete,false);
		    $message = "schools Deleted Successfully !!";	
		
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
			$fe_id=get_field('schools','user_id','id',$user_id);
			$record->id		= $fe_id;
			update_record('fe_users', $record);
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
				$fe_id=get_field('schools','user_id','id',$value);
				$delete_record->id		= $fe_id;
				update_record('fe_users', $delete_record);
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
		//$update_mode->featured ='featured';
        $update_mode->featured = $_GET['featured'];	
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
  $columns = array("check",'id','school_name','Website Name', 'educational_interest', 'address', 'goals_in_life', 'expectation_from_s4c');
	
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
			$$column = "<a href=\"school_manage.php?sort=$column&amp;dir=$columndir&amp;search=$search\">".$string[$column]."</a>$columnicon";
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
		
		$extraselect .= " AND school_name LIKE '%".$txtsearch_name."%'";
		
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
	$user_data = get_cmi_records('schools', true, '*', $search, $searchfields, $extraselect, $sort_term, $page, $perpage);
	$user_count = get_cmi_records('schools', false, '', $search, $searchfields, $extraselect);
	
	 if (!$user_data) {
			$match = array();
			//print_heading(get_string('norecordsfound'));
			$table = NULL;

		} else {

			$table->head = array ($check,$id,$school_name,"<a href='JavaScript:void(0);'>Email</a>",'Website Name', $address,"","","");
			$table->align = array ("center", "center", "center", "center", "center", "center" ,"center","center");
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
				
				$disablebutton = get_buttons('Active/inactive',"school_manage.php?active_inactive=$data->id&status=$status","theme/$theme/images/$image");
				if(empty($data->featured) || $data->featured=="no")
				{
				     $featured="featured";
				}else{
				     $featured="no";
				}
				$featured_link = get_buttons("$featured","$page_name?id=$data->id&featured=$featured&$page_var&amp;page=$page"," theme/$theme/images/$featured_image");
			
			
				$viewhide = get_buttons('View/Hide to users',"$page_name?view_hide_school_id=$data->id&view_hide=$view_hide&amp;$page_var&amp;page=$page"," theme/$theme/images/$view_hide_image");

				$deletebutton = get_buttons('Delete',"#","theme/$theme/images/delete.png","onclick=\"confirm_delete($data->id); return false;\"");					
				
				$schools = get_record('fe_users','id',$data->user_id);
				$email = (isset($schools->email))?$schools->email:'';
				$table->data[] = array ("<input type=\"checkbox\" class=\"check-me\" id=\"deletecheck\" name=\"checkbox[]\" 	value=\"".$data->id."\" >",
					"$data->id",
					"$data->school_name",					
					"$email",
					"$data->web_url",
					"$data->address",
					"<a href='school_detail.php?id=".$data->id."'>View Detail</a>",
				   "<a href='school_leads_detail.php?id=".$data->user_id."'>Leads<br />Detail</a>",
					$viewhide.$featured_link.$disablebutton . $deletebutton
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
				call_alert_confirm("Are you sure you want to delete this record?",'school_manage.php?delete='+val);
					
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
					call_alert_confirm("Are you sure you want to delete selected Event?",'school_manage.php?deleteall='+tot);
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
					call_alert_confirm("Are you sure you want to mark selected records "+val+"?","school_manage.php?"+val+"="+tot);
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
						<td><input name="btnReset" id="btnReset" type="reset" value="Clear Search" onClick="location.href='school_manage.php'"></td>
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
								<a onclick="confirm_deleteall(); return false;" href="school_manage.php?deleteall=1">Delete All</a>
									<a id="active" onclick="active_inactive(this.id); return false;" href="school_manage.php?statusall=active"><img src="theme/'.$theme.'/images/add1.png" alt="active"/></a>
									<a id="inactive" onclick="active_inactive(this.id); return false;" href="school_manage.php?statusall=inactive"><img src="theme/'.$theme.'/images/delete1.png" alt="active"/></a>
								</div>';
						print_paging_bar($user_count, $page, $perpage,
										 "school_manage.php?sort=$sort&amp;dir=$dir&amp;txtsearch_address=$txtsearch_address&amp;txtsearch_name=$txtsearch_name&amp;".$searchdescription."perpage=$perpage&amp;");
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