<?php 
	require_once("config.php");
	isLogin();
	//isallow();
	$theme = current_theme(); 

	$message	= "";
	$master_table_name = 'student';
	$master_image_path='gallery';

	$page_name =  substr(str_replace(array(str_replace('','',$CFG->dirroot),''),'',($_SERVER['SCRIPT_FILENAME'])),'1');
	$page_form = str_replace('Detail','form',$page_name);	
	$page_title = ucwords(str_replace('_',' ',str_replace('manage.php','',$page_name)));

	$sort			= optional_param('sort', 'id', PARAM_TEXT);
	$dir			= optional_param('dir', 'ASC', PARAM_ALPHA);
	$page			= optional_param('page', 0, PARAM_INT);	
	$perpage		= 10;     
	
	
	$id				= required_param('id',PARAM_INT);

    $action			= optional_param('action', PARAM_TEXT);
    $edit           = $action=='edit'?true:false;
	
	$txtsearch_name	= optional_param('txtsearch_name', '', PARAM_RAW);

	$page_arr		= array(
							'sort'			=> $sort,
							'dir'			=> $dir,
							'perpage'		=> $perpage,
							'page'		=> $page,
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

    //Updation goes here
    if($_POST && $_POST['action']=="update"){
        $user_id    = required_param('user_id',PARAM_INT);
        $first_name = required_param('first_name',PARAM_TEXT);
        $last_name  = required_param('last_name',PARAM_TEXT);
        $gender     = required_param('gender',PARAM_TEXT);
        $dob_month  = required_param('date_month',PARAM_TEXT);
        $dob_day    = required_param('date_day',PARAM_TEXT);
        $dob_year   = required_param('date_year',PARAM_TEXT);
        $ed_interest= required_param('educational_interest',PARAM_TEXT);
        $address    = required_param('address',PARAM_TEXT);
        $street     = optional_param('street');
        $state      = required_param('state',PARAM_TEXT);
        $city       = required_param('city',PARAM_TEXT);
        $zip_code   = required_param('zip_code',PARAM_INT);

        $student_info                       = new Object();
        $student_info->id                   = $user_id;
        $student_info->first_name           = $first_name;
        $student_info->last_name            = $last_name;
        $student_info->gender               = $gender;
        $student_info->last_name            = $last_name;
        $student_info->date_of_birth        = $dob_month."/".$dob_day."/".$dob_year;
        $student_info->educational_interest = $ed_interest;
        $student_info->address              = $address;
        $student_info->street               = $street;
        $student_info->state                = $state;
        $student_info->city                 = $city;
        $student_info->address              = $address;
        $student_info->zip_code             = $zip_code;

        update_record('student',$student_info);
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
					Student Detail: 
				</div>
				<?php notify($message);?>
				<form method="post" name="student_info" id="student_info" action="<?php echo $page_name."?id=".$id; ?>" >
                    <input type="hidden" name="user_id" value="<?php echo $id; ?>">
                    <input type="hidden" name="action" value="update">
						<table width="90%">
							<tbody>
								
								<div>
								
									<?php
									//$personal_info	= get_record('student','id',$id);
									$sql	= "select first_name,last_name,gender,date_of_birth,educational_interest,	address,street,state,city,zip_code,short_description,long_description,goals_in_life,expectation_from_s4c from student where id=$id";									
									$personal_info=get_record_sql($sql);
									$user_id		= get_field('student','user_id','id',$id);
//									echo "<pre>"; print_r($personal_info);
									//$update_string	='';	
									if($personal_info)
									{
										echo "<h4>Personal Detail:</h4>";
										echo" <table border='0' cellspacing='5' cellpadding='5'>";										
										foreach($personal_info as $key=>$value)
										{									
											if($key=="long_description")
											{
											    $title = 'Description';
											}else{
												$title = ucwords(str_replace('_',' ',$key));
											}
											
											if($value){
												echo"
													<tr>
													<th width='150px'><div align='right'>". $title ."</div></th>
													<td width='3%'>:</td>
													<td width='75%'>";
                                                    if($edit===true){
                                                        if($key=="gender"){
                                                            $options = array("male" =>"Male",
                                                                "female" =>"Female");
                                                            echo get_select_tag("gender",$options,$value,false);
                                                        } else if($key=="date_of_birth"){
                                                            $date_split = explode("/",$value);   // MM/DD/YYYY
                                                            echo get_select_tag("date_month","month",$date_split[0],false);
                                                            echo get_select_tag("date_day","date",$date_split[1],false);
                                                            echo get_select_tag("date_year","year",$date_split[2],false,"",true);
                                                        } else if($key=="educational_interest"){
                                                            $options = array("High_schools" =>"High Schools",
                                                                "Colleges" =>"Colleges",
                                                                "B_Schools" =>"B-Schools");
                                                            echo get_select_tag("educational_interest",$options,$value,false);
                                                        } else if($key=="state"){
                                                            $states = get_records_sql('SELECT id,name FROM '.$CFG->prefix.'state WHERE status="active"');
                                                            echo get_select_tag("state",$states,$value);
                                                        } else if($key=="city"){
                                                            $cities = get_records_sql('SELECT * FROM '.$CFG->prefix.'city WHERE status="active"');
                                                            echo get_select_tag("city",$cities,$value);
                                                        } else {
                                                            echo "<input type='text' name='".$key."' value='".$value."' />";
                                                        }
                                                    } else {
                                                        if($key == 'state' || $key == 'city'){
                                                            echo  get_field($key,'name','status','active','id',$value);
                                                        } else {
                                                            echo $value;
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
                                                    <td><input type='submit' name='update_student_info' value='Update' /></td>
                                                </tr>
                                                ";
                                        }
										echo "</table>";
									}
											
																			?>
										
										
										<?php
										$sql="select degree,year_of_passing,institute_name from academic where student_id=$user_id and status='active'";
										$academic_info=get_records_sql($sql);

										if($academic_info!='')
										{
											echo "<h4>Academic Detail:</h4>";
											echo" <table border='0' cellspacing='5' cellpadding='5'>";
												
												 foreach($academic_info as $object)
													{
                                                        // Little dirty hack for supporting multiple info, since it was missing
                                                        foreach($object as $key=>$value){
                                                            if($value){
                                                                echo"
                                                            <tr>
                                                            <th width='150px'><div align='right'>".ucwords(str_replace('_',' ',$key)) ."</div></th>
                                                            <td width='3%'>:</td>
                                                            <td width='75%'>";

                                                                    if($key == 'year_of_passing'){
                                                                        if($value != ''){
                                                                            echo @date('d F Y',$value);
                                                                        }
                                                                    }else{
                                                                        echo $value;
                                                                    }

                                                                echo "</td>";
                                                                echo "</tr>";
                                                            }
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
					</form>
				</td>
		</tr>
	</table>
<?php
	print_container_end();
	print_footer();
?>
