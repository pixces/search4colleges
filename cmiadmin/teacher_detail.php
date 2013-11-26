<?php 
	require_once("config.php");
	isLogin();
	//isallow();
	$theme = current_theme(); 
	$message	= "";
	$master_table_name = 'teacher';	
	$master_image_path = 'gallery';

	$page_name =  substr(str_replace(array(str_replace('','',$CFG->dirroot),''),'',($_SERVER['SCRIPT_FILENAME'])),'1');
	$page_form = str_replace('Detail','form',$page_name);	
	$page_title = ucwords(str_replace('_',' ',str_replace('manage.php','',$page_name)));

	$sort			= optional_param('sort', 'id', PARAM_TEXT);
	$dir			= optional_param('dir', 'ASC', PARAM_ALPHA);
	$page			= optional_param('page', 0, PARAM_INT);	
	$perpage		= 10;     
	
	
	$id				= required_param('id',PARAM_INT);
    $edit           = $_GET['action']=='edit'?true:false;
	
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


    // Updation goes here
    if($_POST && $_POST['action']=='update'){
        //echo "<pre>"; print_r($_POST); exit;

        $user_id        = required_param('user_id',PARAM_INT);
        $first_name     = required_param('first_name',PARAM_TEXT);
        $last_name      = required_param('last_name',PARAM_TEXT);
        $gender         = required_param('gender',PARAM_TEXT);
        $dob_month      = required_param('dob_month',PARAM_TEXT);
        $dob_day        = required_param('dob_day',PARAM_TEXT);
        $dob_year       = required_param('dob_year',PARAM_TEXT);
        $address        = required_param('address',PARAM_TEXT);
        $street         = optional_param('street');
        $state          = required_param('state',PARAM_TEXT);
        $city           = required_param('city',PARAM_TEXT);
        $zip_code       = required_param('zip_code',PARAM_INT);
        $about_me       = optional_param('about_me');
        $qualification  = optional_param('qualification');
        $college_name   = optional_param('college_name');
        $experience     = optional_param('experience');
        $primary_phone  = optional_param('primary_phone');
        $private_email  = optional_param('private_email');
        $comments       = optional_param('comments');


        $professor_info                 = new Object();
        $professor_info->id             = $id;
        $professor_info->user_id        = $user_id;
        $professor_info->first_name     = $first_name;
        $professor_info->last_name      = $last_name;
        $professor_info->gender         = $gender;
        $professor_info->last_name      = $last_name;
        $professor_info->date_of_birth  = $dob_month."/".$dob_day."/".$dob_year;
        $professor_info->address        = $address;
        $professor_info->street         = $street;
        $professor_info->state          = $state;
        $professor_info->city           = $city;
        $professor_info->address        = $address;
        $professor_info->zip_code       = $zip_code;
        $professor_info->about_me       = $about_me;
        $professor_info->qualification  = $qualification;
        $professor_info->college_name   = $college_name;
        $professor_info->experience     = $experience;
        $professor_info->primary_phone  = $primary_phone;
        $professor_info->private_email  = $private_email;
        $professor_info->comments       = $comments;

        update_record('teacher',$professor_info);
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
					Teacher Detail: 
				</div>
				<?php notify($message);?>
				<form method="post" name="frm_search" id="frm_search" action="<?php echo $page_name.'?id='.$id; ?>">
                    <input type="hidden" name="action" value="update"/>
						<table width="90%">
							<tbody>
								<div><h4>Personal Detail:</h4>
								
									<?php
									$personal_info	= get_record('teacher','id',$id);
									$user_id		= $personal_info->user_id;
									$update_string ='';
                                    echo "<input type='hidden' name='user_id' value='".$user_id."'/>";
									if($personal_info){
											echo" <table border='0' cellspacing='5' cellpadding='5'>
												 <tr>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
													<td>&nbsp;</td>
												  </tr>";
											foreach($personal_info as $key=>$value)
												{
													if(($key != 'id') && ($key != 'status') && ($key !='user_id'))
													{
														echo"
															<tr>
															<th width='150px'><div align='right'>".ucwords(str_replace('_',' ',$key)) ."</div></th>
															<td width='3%'>:</td>
															<td width='75%'>";
																if($edit==true){
                                                                    if($key == 'added_date'){
                                                                        if($value != ''){
                                                                            echo date('d/m/y',$value);
                                                                        }
                                                                    } else if($key=="date_of_birth"){
                                                                        $date_split = explode("/",$value);   // MM/DD/YYYY
                                                                        echo get_select_tag("dob_month","month",$date_split[0],false);
                                                                        echo get_select_tag("dob_day","date",$date_split[1],false);
                                                                        echo get_select_tag("dob_year","year",$date_split[2],false,"",true);
                                                                    } else if($key=="gender"){
                                                                        $options = array("male" =>"Male",
                                                                            "female" =>"Female");
                                                                        echo get_select_tag("gender",$options,$value,false);
                                                                    } else if($key == 'state'){
                                                                        $states = get_records_sql('SELECT * FROM '.$CFG->prefix.'state WHERE status="active"');
                                                                        echo get_select_tag("state",$states,$value);
                                                                    } else if($key == 'city'){
                                                                        $cities = get_records_sql('SELECT * FROM '.$CFG->prefix.'city WHERE status="active"');
                                                                        echo get_select_tag("city",$cities,$value);
                                                                    } else if($key=="about_me"){
                                                                        echo "
                                                                            <textarea name='".$key."'>".$value."</textarea>
                                                                        ";
                                                                    } else {
                                                                        echo "
                                                                            <input type='text' name='".$key."' value='".$value."'>
                                                                        ";
                                                                    }


                                                                } else {
                                                                    if($key == 'added_date'){
                                                                        if($value != ''){
                                                                            echo date('d/m/y',$value);
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
                                                                }
															echo "</td>";
															echo "</tr>";
													}
											}
                                            if($edit===true){
                                                echo "
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                            <td><input type='submit' name='update_parent_info' value='Update' /></td>
                                                        </tr>
                                                        ";
                                            }
											echo "</table>";
										}
									?>
									<?php
									$sql="select name,type,comment from gallery where user_id=$user_id and type='image' and status='active'";
									$get_photo=get_records_sql($sql);
									if($get_photo!='')
									{
										echo "<h4>Photo And Video:</h4>";
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
					</form>
				</td>
		</tr>
	</table>
<?php
	print_container_end();
	print_footer();
?>
