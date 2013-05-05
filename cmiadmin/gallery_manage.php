<?php 
	require_once("config.php");
	isLogin();
	isallow();
	$theme = current_theme();	
	$approved_msg = '';
			
	$name='';
	$message	= "";
	$master_table_name = 'gallery';
	$master_image_path = 'gallery';

	$page_name =  substr(str_replace(array(str_replace('','',$CFG->dirroot),''),'',($_SERVER['SCRIPT_FILENAME'])),'1');
	$page_form = str_replace('manage','form',$page_name);
	$page_title = ucwords(str_replace('_',' ',str_replace('manage.php','',$page_name)));

	$sort			= optional_param('sort', 'added_date', PARAM_TEXT);
	$dir			= optional_param('dir', 'DESC', PARAM_ALPHA);
	$page			= optional_param('page', 0, PARAM_INT);	
	$perpage		= 20;      
	$txtsearch_name	= optional_param('txtsearch_name', '', PARAM_RAW);

	$page_arr		= array(
							'sort'			=> $sort,
							'dir'			=> $dir,
							'perpage'		=> $perpage,
							'txtsearch_name'=> $txtsearch_name
						);

	$page_var = http_build_query($page_arr,'','&amp;');

	if(isset($_GET) && isset($_GET['delete']))
	{
		$status_id= required_param('delete', PARAM_INT);
		$status = 'delete';
		update_status_record($status_id,$status,$master_table_name);
	}

	if(isset($_GET) && isset($_GET['deleteall']))
	{
		$ids = explode(',',optional_param('deleteall', PARAM_RAW));
		foreach($ids as $value){
			$delete_record = new object();
			$delete_record->id = $value;
			$delete_record->status = 'delete';
		
			if(update_record($master_table_name, $delete_record)){
				cmi_add_to_log($master_table_name,'Delete','Deleted photo id='.$value);
				$message = " Photo Deleted Successfully !!";		
			}
		}
	}
	if(isset($_GET) && isset($_GET['declined']) || isset($_GET['approved']))
	{
		$approved = '';
		$ids = optional_param('declined','',PARAM_RAW);
		$idexploded = explode(',',$ids);
		$approved = 'declined';
		if($ids == ''){
			$ids = optional_param('approved','',PARAM_RAW);
			$idexploded = explode(',',$ids);
			$approved = 'approved';
		}

		
		foreach($idexploded as $value){
			$delete_record = new object();
			$delete_record->id = $value;
			$delete_record->approved = $approved;
			
			if(update_record($master_table_name, $delete_record)){

				//$status = str_replace('e','a',$status);
				cmi_add_to_log($master_table_name,$approved,$approved.'d '.$master_table_name.' id: '.$value);
				$message = "$master_table_name Status Updated Successfully !!";		
			}
		}
	}

	if(isset($_GET) && isset($_GET['active_inactive']))
	{
		$status_id			= required_param('active_inactive', PARAM_INT);
		$status				= required_param('status', PARAM_TEXT);
		update_status_record($status_id,$status,$master_table_name);	
	}

	if(isset($_GET) && isset($_GET['approved_declined']))
	{
		$id					= required_param('approved_declined', PARAM_INT);
		$status				= required_param('approved', PARAM_TEXT);

		$update_mode =new object();
		$update_mode->id =$id;
		$update_mode->approved =$status;
		
		update_record($master_table_name, $update_mode);
	}	

	// Carry on with the user listing
	$columns = array("check","Name","image");	
	$column = get_sorting_header($page_arr,$page_name,$theme,$columns);
	extract($column);

	$sort_term		= $sort." ".$dir;				
	$searchfields	= array("name");
	$extraselect	= " status != 'delete'";

	$master_data		= get_cmi_records($master_table_name, true, '*', $txtsearch_name, $searchfields, $extraselect, $sort_term, $page, $perpage);
	$master_count		= get_cmi_records($master_table_name, false, '', $txtsearch_name, $searchfields, $extraselect);

	if (!$master_data)
	{
		$match = array();
		$table = NULL;
		
	}
	else
	{
		$table->head = array ($check,'Name',$image,"status");
		$table->align = array ("center","center", "center","center");
		$table->width = "100%";
		$get_count=count($master_data);
		
		foreach ($master_data as $data)
		{
			
			$approved = 'approved';
			$image = 'inactive.png';
			if($data->approved == 'approved')
			{
				$approved = 'declined';
				$image = 'active.png';
			}
			if($data->type =='image')
			{
				$approved_msg=$approved .'&nbsp;&nbsp;image';
			}
			if($data->type =='video')
			{
				$approved_msg=$approved .'&nbsp;&nbsp;video';
			}

			$approvedbutton = get_buttons($approved_msg,"$page_name?approved_declined=$data->id&approved=$approved&amp;$page_var&amp;page=$page"," theme/$theme/images/$image");

			$name='';
			$type='';
			$display='';

			if($data->name!='' && $data->type=='image')
				{
					$display="<a href='$CFG->siteroot/file.php/$master_image_path/$data->name' rel ='lightbox' title='$data->name'><img src='$CFG->siteroot/file.php/$master_image_path/f1_$data->name' alt='$data->name'/></a>";
				}

				elseif($data->name!='' && $data->type=='video')
				{
					$display="<a href='$CFG->siteroot/file.php/$master_image_path/$data->name' rel ='lightbox' title='$data->name'><img src='$CFG->siteroot/images/video_img.jpg' width='70' height='50' alt='$data->name'/></a>";
					
				}
				elseif($data->name!='' && $data->type=='link')
				{
					$display="<a href='$CFG->siteroot/file.php/$master_image_path/$data->name' rel ='lightbox' title='$data->name'><img src='$CFG->siteroot/images/video_img.jpg' width='70' height='50' alt='$data->name'/></a>";
					
				}
				//print_r($display);

			
			$sql="select * from {$CFG->prefix}fe_users where id=$data->user_id and status='active'";
			$get_id	= get_record_sql($sql);
			
			if($get_id != '')
			{
			 
				if($get_id->user_type !='' && $get_id->user_type =='student')
				{
					$name = get_field('student','first_name','user_id',$get_id->id);
					if($name=='')
					{
						$name=$get_id->email;
					}
				}
				elseif($get_id->user_type!='' && $get_id->user_type=='counselor')
				{
					$name = get_field('counselors','first_name','user_id',$get_id->id);
					
					if($name=='')
					{
						$name=$get_id->email;
					}
				}
				elseif($get_id->user_type!='' && $get_id->user_type=='parent')
				{
					$name = get_field('parent','first_name','user_id',$get_id->id);
					if($name=='')
					{
						$name=$get_id->email;
					}
				}
				elseif($get_id->user_type!='' && $get_id->user_type=='teacher')
				{
					$name = get_field('teacher','first_name','user_id',$get_id->id);
					if($name=='')
					{
						$name=$get_id->email;
					}
				}
				elseif($get_id->user_type!='' && $get_id->user_type=='school')
				{
					$name = get_field('schools','school_name','user_id',$get_id->id);
					if($name=='')
					{
						$name=$get_id->email;
					}
				}
				$deletebutton = get_buttons('Delete',"#","theme/$theme/images/delete.png","onclick=\"confirm_delete($data->id,'$page_name','$page_var','$page'); return false;\"");

				$table->data[] = array ("<input type=\"checkbox\" class=\"check-me\" id=\"deletecheck\" name=\"checkbox[]\" 	value=\"".$data->id."\" >",
					"$name",
					"$display",
					"$approvedbutton $deletebutton"					   
				);
			}
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

	window.addEvent('domready', function()
	{
		new FormCheck('frm_search');		
		show_tipz();		
		get_datepicker();

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
		call_alert_confirm("Are you sure you want to delete this record?",'gallery_manage.php?delete='+val);
			
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
			call_alert_confirm("Are you sure you want to delete selected Photos/videos?",'gallery_manage.php?deleteall='+tot);
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
			call_alert_confirm("Are you sure you want to mark selected records "+val+"?","gallery_manage.php?"+val+"="+tot);
		else{
			Sexy.error("Select atleast one record to mark "+val+"!"); 
			return false;
		}
	}
			
			
		
</script>

	<table width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td valign="top" width="200"><?php load_menu(); ?></td>
		</tr>
		<tr>
			<td>
				<div class="DotedHeader" width="70%">
					Manage <?php echo $page_title;?>:
				</div>				
				<br><br>
				<?php notify($message);?>
				<center>
					<form method="post" name="frm_search" id="frm_search" action="<?php echo $page_name; ?>">
						<table width="90%">
							<tbody>
								<tr>
									<td class="Label" align="center" width="90%">
										<table>
											<!---<tr>
												<td>Name&nbsp;:&nbsp; </td>
												<td>
													<input name="txtsearch_name" id="txtsearch_name" class="txtBox" value="<?php echo $txtsearch_name;?>" type="text">
												</td>
											</tr>
											<tr>
												<td colspan="2">
													<center>
														<input name="btnSearch" id="btnSearch" type="submit" value="Search">
														<input name="btnreset" id="btnreset" type="button" value="Reset" onclick="txtsearch_name.value=''">
														<input name="btnclear" id="btnclear" type="button" value="Clear Search"  onclick="window.location ='<?php echo $page_name; ?>'">
													</center>
												</td>
											</tr>--->
										</table>
									</td>
								</tr>
								<tr><td>&nbsp;</td></tr>
								<tr><td align="center" width="90%"></td></tr>
								<tr>
									<td align="center" width="90%">
										<div>
											<?php
											if (!empty($table)) 
											{
												print_table($table);
												echo '<div align="left">
													<a onclick="confirm_deleteall(); return false;" href="gallery_manage.php?deleteall=1">Delete All</a>
														<a id="approved" onclick="active_inactive(this.id); return false;" href="gallery_manage.php?statusall=active"><img src="theme/'.$theme.'/images/add1.png" alt="active"/></a>
														<a id="declined" onclick="active_inactive(this.id); return false;" href="gallery_manage.php?statusall=inactive"><img src="theme/'.$theme.'/images/delete1.png" alt="active"/></a>
													</div>';
												print_paging_bar($master_count, $page, $perpage,"$page_name?$page_var&amp;");
											}
											else
											{	
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
								</tr>
								<tr><td width="90%"></td></tr>
							</tbody>
						</table>
					</form>
				</center>
			</td>
		</tr>
	</table>
<?php
	print_container_end();
	print_footer();
?>