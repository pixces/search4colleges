<?php 
	require_once("config.php");
	isLogin();
	//isallow();
	$theme = current_theme(); 
	$message	= "";
	$master_table_name = 'schools';	
	$master_image_path='gallery';

	$page_name =  substr(str_replace(array(str_replace('','',$CFG->dirroot),''),'',($_SERVER['SCRIPT_FILENAME'])),'1');
	$page_form = str_replace('Detail','form',$page_name);	
	$page_title = ucwords(str_replace('_',' ',str_replace('manage.php','',$page_name)));

	$sort			= optional_param('sort', 'id', PARAM_TEXT);
	$dir			= optional_param('dir', 'ASC', PARAM_ALPHA);
	$page			= optional_param('page', 0, PARAM_INT);	
	$perpage		= 10;     
	
	
	$id				= required_param('id',PARAM_INT);	     
	
	$txtsearch_name	= optional_param('txtsearch_name', '', PARAM_RAW);

	$page_arr		= array(
							'sort'			=> $sort,
							'dir'			=> $dir,
							'perpage'		=> $perpage,
							'page'			=> $page,
							'txtsearch_name'=> $txtsearch_name
						);	

	$page_var = http_build_query($page_arr,'','&amp;');	

	if(isset($_GET) && isset($_GET['delete']))
	{
		$status_id= required_param('delete', PARAM_INT);
		$status = 'delete';
		update_status_record($status_id,$status,$master_table_name);
	}

	if(isset($_GET) && isset($_GET['active_inactive']))
	{
		$status_id			= required_param('active_inactive', PARAM_INT);
		$status				= required_param('status', PARAM_TEXT);
		update_status_record($status_id,$status,$master_table_name);	
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
	});
</script>

	<table width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td valign="top" width="200"><?php load_menu(); ?></td>
		</tr>
		<tr>
			<td>
				<div class="DotedHeader" width="70%">
					School Detail: 
				</div>
				<?php notify($message);?>
				<form method="post" name="frm_search" id="frm_search" action="<?php echo $page_name; ?>">
						<table width="90%">
							<tbody>
								<div><h4>School Details:</h4>
								
									<?php
									//$personal_info = get_record('schools','id',$id);
									$personal_info = get_record_sql("select id, 	user_id,school_name,seo_keyword,address,street,city,state,zip_code,phone,added_date,status,about_me,web_url,featured,display,image,logo from {$CFG->prefix}schools where id=$id");
									$user_id		= $personal_info->user_id;

									$update_string ='';	
									if($personal_info){
											echo" <table border='0' cellspacing='5' cellpadding='5'>
												 <tr>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
												  </tr>";
											foreach($personal_info as $key=>$value)
												{
													if(($key != 'id') && ($key != 'status') && ($key != 'user_id'))
													{
														$title='';
														 if($key=="address")
														 {
														   $title="Address Line1";
														 }
														 else if($key=="street"){
														   $title="Address Line2";
														 }else{
														   $title=ucwords(str_replace('_',' ',$key));
														 }	
														
														echo"
															<tr>
															<th width='150px'><div align='right'>".$title ."</div></th>
															<td width='3%'>:</td>
															<td width='75%'>";														
																if($key == 'added_date'){
																	if($value != ''){
																		echo date('m/d/y',$value);
																	}
																}
																else{
																	if($key == 'state' || $key == 'city'){
																		echo  get_field($key,'name','status','active','id',$value);
																	}
																	else
																	{
																		echo $value;
																	}	
																}
															echo "</td>";
															echo "</tr>";
													}
											}
											echo "</table>";
										}
									?>
									
									<?php
									$sql="select name,type,comment from gallery where user_id=$user_id and type='image' and status='active'";
									$get_photo=get_records_sql($sql);
									if($get_photo!='')
									{
										echo "<h4>Photo And Video</h4>";
										echo "<table border='0' cellspacing='5' cellpadding='5'>";
										echo "<tr>";
										echo "<th width='150px'>Image</th>";
										echo "<td width='3%'>:</td>";
										echo "<td width='75%'>";
										foreach($get_photo as $value)
										{
											echo "<a href='$CFG->siteroot/file.php/$master_image_path/$value->name' rel ='lightbox' title='$value->name'><img src='$CFG->siteroot/file.php/$master_image_path/f1_$value->name' alt='$value->name'/></a>";
											echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
												
										}
										echo "</td>";
										echo "</tr>";
										echo "</table>";
									}
									?>
									<?php
									$sql="select name,type,comment from gallery where user_id=$user_id and (type='video' OR type='link') and status='active'";
									$get_photo=get_records_sql($sql);
									if($get_photo!='')
									{
										echo "<table border='0' cellspacing='5' cellpadding='5'>";
										echo "<tr>";
										echo "<th width='150px'>Video</th>";
										echo "<td width='3%'>:</td>";
										echo "<td width='75%'>";
										foreach($get_photo as $value)
										{
											if($value->type == "video"){
												echo "<a href='$CFG->siteroot/file.php/$master_image_path/$value->name' rel ='lightbox' title='$value->name'><img src='$CFG->siteroot/images/video_img.jpg' width='70' height='50' alt='$value->name'/></a>";
											}else{
												echo "<a href='$value->name' rel ='lightbox' title='$value->name'><img src='$CFG->siteroot/images/video_img.jpg' width='70' height='50' alt='$value->name'/></a>";
											}
											echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
												
										}
										echo "</td>";
										echo "</tr>";
										echo "</table>";
									}
									?>
																
								</div>
								
							</tbody>
						</table>


						<?php 
							

						?>

						
					</form>
				</td>



		</tr>
	</table>
<?php
	print_container_end();
	print_footer();
?>