<?php 
	require_once("config.php");
	define('menu_status', 'Manage News');
	isLogin();
	isallow();
	$theme = current_theme();
	$edit_flag= false;
	
	$page_name =  substr(str_replace(array(str_replace('','',$CFG->dirroot),''),'',($_SERVER['SCRIPT_FILENAME'])),'1');
	$page_form = str_replace('manage','form',$page_name);
	$page_title = ucwords(str_replace('_',' ',str_replace('manage.php','',$page_name)));
	
	$sort         = optional_param('sort', 'subject', PARAM_TEXT);
    $dir          = optional_param('dir', 'DESC', PARAM_ALPHA);
    $page         = optional_param('page', 0, PARAM_INT);	
	$perpage      = $CFG->news_letters_per_page=25;        // how many per page
	$search		  = optional_param('search_title', '', PARAM_RAW);
	$search_id	  = optional_param('id', '', PARAM_RAW);
	$search_username = optional_param('username', '', PARAM_RAW);

	$message	= "";	
	
	if(isset($_POST['updatenewsletters'])){
		$hiddenid			= optional_param('hiddenid','', PARAM_TEXT);	
		$subject			= optional_param('subject', PARAM_TEXT);			
		$new_content		= optional_param('new_content', '', PARAM_RAW);
		$added_date 		= time();
		$allowed_sections	= "";
		
		$updtnewsletters = new object();
		
		$updtnewsletters->id						= $hiddenid;	
		$updtnewsletters->subject					= $subject;
		$updtnewsletters->new_content				= $new_content;
		$updtnewsletters->last_modified_date		= $added_date;

		//print_object($updtnewsletters);
		$message = '';
		if(!empty($updtnewsletters)){
			$update = update_record('news_letters', $updtnewsletters);
			if($update){
			  $message =  '<div>News Letters Updated !!</div>';
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
		
		if(update_record('news_letters', $delete_record)){
			cmi_add_to_log('news_letters','Delete','Deleted news_letters id='.$_GET['delete']);
			$message = "News Letters Deleted Successfully !!";		
		}
	}

	if(isset($_GET) && isset($_GET['deleteall']))
	{
		$ids = explode(',',optional_param('deleteall', PARAM_RAW));
		foreach($ids as $value){
			$delete_record = new object();
			$delete_record->id = $value;
			$delete_record->status = 'delete';
		
			if(update_record('news_letters', $delete_record)){
				cmi_add_to_log('news_letters','Delete','Deleted news_letters id='.$value);
				$message = "News Letters Deleted Successfully !!";		
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

		if(update_record('news_letters', $record)){
			//$status = str_replace('e','a',$status);
			cmi_add_to_log('news_letters',$status,$status.'d news_letters id: '.$user_id);
			$message = "News Letters Status Updated Successfully !!";
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
		
			if(update_record('news_letters', $delete_record)){
				//$status = str_replace('e','a',$status);
				cmi_add_to_log('news_letters',$status,$status.'d news_letters id: '.$value);
				$message = "News Letters Status Updated Successfully !!";		
			}
		}
	}
	if(isset($_POST) && !empty($_POST) && !isset($_POST['btnSearch']) && !isset($_POST['delete_flag']) && !isset($_POST['updatenewsletters']))
	{
		$subject			= optional_param('subject', PARAM_TEXT);
		$new_content		= optional_param('new_content', '', PARAM_RAW);
		$added_date 		= time();
		$allowed_sections	= "";
		
		$addnewsletters = new object();
		$addnewsletters->subject					= $subject;
		$addnewsletters->new_content				= $new_content;
		$addnewsletters->added_date					= $added_date;
		$addnewsletters->last_modified_date			= $added_date;
		
		if(!empty($addnewsletters->subject))
		{
			if(isset($_POST['edit_mode']) && $_POST['edit_mode'] == "true")
			{
				$user_id = required_param('user_id', PARAM_TEXT);
				$addnews->id = $user_id;
				if(update_record('news_letters', $addnewsletters)){
					cmi_add_to_log('news_letters','Update','news_letters id='.$user_id);
					$message = "News Letters Details Updated Successfully !!";
				}

			}
			else
			{		
				if($new = insert_record('news_letters', $addnewsletters)){
					cmi_add_to_log('news_letters','Insert','New news_letters '.$subject.' added');
					$message = "News Letters Created Successfully !!";
				}
			}
		}
	}
	
	
	// Carry on with the user listing
    $columns = array("check","id", "subject");
	
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
			$$column = "<a href=\"newsletters_manage.php?sort=$column&amp;dir=$columndir&amp;search=$search\">".$string[$column]."</a>$columnicon";
		}
		else{
			$$column = "$string[$column]";
		}
		
    }

	$sort_term    = $sort." ".$dir;				
	$searchfields = array("subject","new_content");
	$extraselect = " status != 'delete' ";

	if($search != ''){
		$extraselect .= " AND subject like '%".$search."%'";
	}
	if($search_id != ''){
		$extraselect .= " AND id = '".$search_id."'";
	}
	$user_data = get_cmi_records('news_letters', true, '*', $search, $searchfields, $extraselect, $sort_term, $page, $perpage);
	$user_count = get_cmi_records('news_letters', false, '', $search, $searchfields, $extraselect);
	
	 if (!$user_data) {
			$match = array();
			//print_heading(get_string('norecordsfound'));
			$table = NULL;

		} else {

		  $table->head = array ($check, $id, $subject, "Action");
		  $table->align = array ("center", "center", "center", "center");
		  $table->width = "100%";

				foreach ($user_data as $data) 
				{
						$status = 'active';
						$image = 'delete1.png';
						if($data->status == 'active'){
							$status = 'inactive';
							$image = 'add1.png';
				}

				$editbutton = get_buttons('Edit News Letters',"newsletters_form.php?edit=$data->id","theme/$theme/images/edit.gif","rel='lightbox[get_edit_form_$data->id 75% 90%]'");
				
				$showbutton = get_buttons('Send News Letters',"show_newsletters.php?news_id=$data->id","theme/$theme/images/Sent-Mail-icon.png","rel='lightbox[show_edit_form_$data->id 80% 80%]'");
				

				$disablebutton = get_buttons('Active/inactive',"newsletters_manage.php?active_inactive=$data->id&status=$status","theme/$theme/images/$image");

				$deletebutton = get_buttons('Delete',"#","theme/$theme/images/delete.png","onclick=\"confirm_delete($data->id); return false;\"");
				
				$test_emailbutton = get_buttons('Test Email',"newsletter_test_email.php?send_id=$data->id","theme/cmi/images/test_email.png","rel='lightbox[send_form_$data->id 90% 90%]'");
				$dataname = $data->subject;
				
				$table->data[] = array ("<input type=\"checkbox\" class=\"check-me\" id=\"deletecheck\" name=\"checkbox[]\" 	value=\"".$data->id."\" >",
									"$data->id",
									"$dataname",
									$test_emailbutton.$showbutton.$editbutton.$disablebutton.$deletebutton);
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
				call_alert_confirm("Are you sure you want to delete this record?",'newsletters_manage.php?delete='+val);
					
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
					call_alert_confirm("Are you sure you want to delete selected Event?",'newsletters_manage.php?deleteall='+tot);
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
					call_alert_confirm("Are you sure you want to mark selected records "+val+"?","newsletters_manage.php?"+val+"="+tot);
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
			<div class="DotedHeader" width="70%">Manage News Letters:</div>
			<a href="newsletters_form.php" style="float:right;margin-right:60px;" rel="lightbox[set2 75% 90%]" class="clearfix" title="Add <?php echo $page_title; ?>"><img src="<?php echo "theme/$theme/images/add.png" ?>" /></a><br>
		         
        <br>
		<?php notify($message);?>
		<form method="post" name="frm_search" id="frm_search" action="newsletters_manage.php">
		<table width="100%">
             <tbody>
			 <tr>
             <td width="5px"></td>
             <td class="Label" align="center" width="90%">
			 
                <table>
					<tr>
						<td>Subject: </td>
						<td><input name="search_title" id="search_title" class="txtBox" value="<?php echo $search;?>" type="text"></td>
					</tr>
					<tr>
						<td><input name="btnSearch" id="btnSearch" type="submit" value="Search"></td>
						<td><input name="btnReset" id="btnReset" type="reset" value="Clear Search" onClick="location.href='newsletters_manage.php'"></td>
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
								<a onclick="confirm_deleteall(); return false;" href="newsletters_manage.php?deleteall=1">Delete All</a>
									<a id="active" onclick="active_inactive(this.id); return false;" href="newsletters_manage.php?statusall=active"><img src="theme/'.$theme.'/images/add1.png" alt="active"/></a>
									<a id="inactive" onclick="active_inactive(this.id); return false;" href="newsletters_manage.php?statusall=inactive"><img src="theme/'.$theme.'/images/delete1.png" alt="active"/></a>
								</div>';
						print_paging_bar($user_count, $page, $perpage,
										 "newsletters_manage.php?sort=$sort&amp;dir=$dir&amp;search=$search&amp;".$searchdescription."perpage=$perpage&amp;");
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