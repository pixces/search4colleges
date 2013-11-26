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
    if($_POST && $_POST['action']==="edit"){
        $user_id            = required_param('user_id',PARAM_TEXT);
        $additional_id      = required_param('additional',PARAM_TEXT);
        $school_name        = required_param('school_name',PARAM_TEXT);
        $seo_keyword        = required_param('seo_keyword',PARAM_TEXT);
        $state              = required_param('state',PARAM_TEXT);
        $address            = required_param('address',PARAM_TEXT);
        $street             = required_param('street',PARAM_TEXT);
        $city               = required_param('city',PARAM_TEXT);
        $phone              = required_param('phone',PARAM_TEXT);
        $url                = required_param('web_url',PARAM_TEXT);
        $zip_code           = optional_param('zip_code');
        $department         = optional_param('department');
        $department_head    = optional_param('department_head');
        $department_email   = optional_param('department_email');
        $map                = optional_param('location_google_map');
        $history            = optional_param('history');
        $board_members      = optional_param('board_members');
        $campus_type        = optional_param('campus_type');
        $only_for_local     = optional_param('only_for_local');
        $population         = optional_param('population');
        $affiliation        = optional_param('affiliation');

        // UPLOAD LOGO IMAGE
        $target_path = $CFG->dataroot.'/schools/';
        if(isset($_FILES['logo']['name']) && !empty($_FILES['logo']['name'])){
            $school_logo = upload_image('logo',$target_path);
            if($school_logo)
                $update_school->logo = $school_logo;
            else
                $updateproduct->logo = '';
        }
        $logo = $school_logo;

        // UPDATE SCHOOLS BASIC INFO
        $school_info                = new Object();
        $school_info->id            = $id;
        $school_info->user_id       = $user_id;
        $school_info->school_name   = $school_name;
        $school_info->seo_keyword   = $seo_keyword;
        $school_info->address       = $address;
        $school_info->street        = $street;
        $school_info->state         = $state;
        $school_info->city          = $city;
        $school_info->zip_code      = $zip_code;
        $school_info->phone         = $phone;
        $school_info->web_url       = $url;
        $school_info->logo          = $logo;

        update_record('schools',$school_info);

        // UPDATE SCHOOLS ADDITIONAL INFO
        $school_additional                      = new Object();
        $school_additional->id                  = $additional_id;
        $school_additional->location_google_map = $map;
        $school_additional->profile             = $history;
        $school_additional->board_members       = $board_members;
        $school_additional->type_of_campus_area = $campus_type;
        $school_additional->student_population  = $population;
        $school_additional->only_for_local      = $only_for_local;
        $school_additional->department          = $department;
        $school_additional->department_head     = $department_head;
        $school_additional->department_email    = $department_email;
        $school_additional->affiliations_id     = $affiliation;

        update_record('schools_additional',$school_additional);

    }


    //$personal_info = get_record('schools','id',$id);
    $personal_info = get_record_sql("select id,user_id,school_name,seo_keyword,address,street,city,state,zip_code,phone,added_date,status,about_me,web_url,featured,display,image,logo from {$CFG->prefix}schools where id=$id");
    $user_id		= $personal_info->user_id;
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

    <link rel="stylesheet" href="<?php echo $CFG->siteroot;?>/editor/ckeditor/samples/sample.css" type="text/css">
    <script type="text/javascript" src="<?php echo $CFG->siteroot;?>/editor/ckeditor/ckeditor.js"></script>
    <script type="text/javascript" src="<?php echo $CFG->siteroot;?>/editor/ckeditor/samples/sample.js"></script>
    <script type="text/javascript" src="<?php echo $CFG->siteroot;?>/editor/ckeditor/ckfinderinit.js"></script>


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
                <form method="post" action='<?php echo $page_name."?id=".$personal_info->id;?>' id="profile_form" name="profile_form" enctype="multipart/form-data"/>
						<table width="90%">
							<tbody>
								<div><h4>School Details:</h4>
								
									<?php

									$update_string ='';	
									if($personal_info){
                                        $additional_info = get_record_sql('Select * from '.$CFG->prefix.'schools_additional where school_id = '.$personal_info->user_id.' and status = "active"');
                                            if($edit===true){ ?>
                                                    <input type="hidden" name="id" value=<?php if(isset($personal_info->id)) echo $personal_info->id; ?> />
                                                    <input type="hidden" name="user_id" value=<?php if(isset($personal_info->user_id)) echo $personal_info->user_id; ?> />
                                                    <input type="hidden" name="additional" value=<?php if(isset($additional_info->id)) echo $additional_info->id; ?> />
                                                    <input type="hidden" name="action" value="edit" />

                                                    <!-- School -->
                                                    <div class="schoolmain_box">
                                                        <table width="100%" border="0" cellpadding="5" cellspacing="0" >
                                                            <tr>
                                                                <td width="200" bgcolor="#f9f9f9" class="property_class">School/College :	</td>
                                                                <td bgcolor="#f9f9f9">
                                                                <span class="value_class">
                                                                    <input class="validate['required'] profile_inputs" value="<?php echo isset($personal_info->school_name) ? $personal_info->school_name : '' ?>" type="text" name="school_name"/>
                                                                </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td width="200" bgcolor="#f9f9f9" class="property_class">SEO  :	</td>
                                                                <td bgcolor="#f9f9f9">
                                                                <span class="value_class">
                                                                    <input class="validate['required','seo'] profile_inputs" value="<?php echo isset($personal_info->seo_keyword) ? $personal_info->seo_keyword : '' ?>" type="text" name="seo_keyword"/>
                                                                </span>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                        <!-- Logo -->
                                                        <br />

                                                        <div class="Logo_main_box">
                                                            <table width="100%" border="0" cellpadding="5" cellspacing="0" >
                                                                <tr>
                                                                    <td width="200" bgcolor="#f9f9f9" class="property_class">Logo :</td>
                                                                    <td width="32" bgcolor="#f9f9f9"><img src="file.php/schools/f1_<?php if(isset($personal_info->logo)) echo $personal_info->logo; ?>" alt="<?php echo isset($personal_info->school_name) ? $personal_info->school_name : '' ?>" title="<?php echo isset($personal_info->school_name) ? $personal_info->school_name : '' ?>"/></td>
                                                                    <td bgcolor="#f9f9f9"> <input type="file" name="logo" class="profile_inputs_file" /></td>
                                                                </tr>
                                                            </table>
                                                        </div>

                                        <!-- Address -->

                                                        <div class="address_details_main">

                                                            <div><h3>Address :</h3></div>
                                                            <table width="100%" border="0" cellpadding="5" cellspacing="0">
                                                                <tr>
                                                                    <td bgcolor="#f9f9f9">Address Line 1 :</td>
                                                                    <td bgcolor="#f9f9f9"><input class="profile_inputs" name="address" value="<?php echo isset($personal_info->address) ? $personal_info->address : '' ?>" type="text" /></td>
                                                                </tr>
                                                                <tr>
                                                                    <td bgcolor="#f9f9f9">Address Line 2 :</td>
                                                                    <td bgcolor="#f9f9f9"><input class="profile_inputs" name="street" value="<?php echo isset($personal_info->street) ? $personal_info->street : '' ?>" type="text" /></td>
                                                                </tr>
                                                                <tr>
                                                                    <td bgcolor="#f9f9f9">Zip code :</td>
                                                                    <td bgcolor="#f9f9f9"><input class="profile_inputs" name="zip_code" value="<?php echo isset($personal_info->zip_code) ? $personal_info->zip_code : '' ?>" type="text" /></td>
                                                                </tr>
                                                                <tr>
                                                                    <td width="200" bgcolor="#f9f9f9">State :</td>
                                                                    <td bgcolor="#f9f9f9">
                                                                        <?php $states = get_records_sql('SELECT * FROM '.$CFG->prefix.'state WHERE status="active"');
                                                                        echo get_select_tag("state",$states,$personal_info->state);
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td bgcolor="#f0f0f0">City :</td>
                                                                    <td bgcolor="#f0f0f0">
                                                                        <?php
                                                                        $cities = get_records_sql('SELECT * FROM '.$CFG->prefix.'city WHERE status="active"');
                                                                        echo get_select_tag("city",$cities,$personal_info->city);
                                                                        ?>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td bgcolor="#f9f9f9">Zip code :</td>
                                                                    <td bgcolor="#f9f9f9"><input class="profile_inputs" name="zip_code" value="<?php echo isset($personal_info->zip_code) ? $personal_info->zip_code : '' ?>" type="text" /></td>
                                                                </tr>
                                                                <tr>
                                                                    <td bgcolor="#f0f0f0">URL :</td>
                                                                    <td bgcolor="#f0f0f0"><input class="profile_inputs validate['required','url']" name="web_url" value="<?php echo isset($personal_info->web_url) ? $personal_info->web_url : '' ?>" type="text" /></td>
                                                                </tr>
                                                            </table>

                                                        </div>

                                        <!-- Contact Detail -->
                                                        <div class="contact_details_main">
                                                            <div><h3>Contact Details : <span style='color:#900;font-family: "Trebuchet MS";font-size: 12px;'>Contact Details are required, if left blank leads will not be sent.</span></h3></div>
                                                            <table width="100%" border="0" cellpadding="5" cellspacing="0">
                                                                <tr>
                                                                    <td width="200" bgcolor="#f9f9f9">Department :</td>
                                                                    <td bgcolor="#f9f9f9"><input name="department" class="profile_inputs" name="department" value="<?php echo isset($additional_info->department) ? $additional_info->department : '' ?>" type="text" /></td>
                                                                </tr>
                                                                <tr>
                                                                    <td bgcolor="#f0f0f0">Department Head :</td>
                                                                    <td bgcolor="#f0f0f0"><input name="department_head" class="profile_inputs" name="department_head" value="<?php echo isset($additional_info->department_head) ? $additional_info->department_head : '' ?>" type="text" /></td>
                                                                </tr>
                                                                <tr>
                                                                    <td bgcolor="#f9f9f9">Email id ( Leads will be sent to this email ) :</td>
                                                                    <td bgcolor="#f9f9f9"><input name="department_email" class="validate['email'] profile_inputs" class="profile_inputs"  value="<?php echo isset($additional_info->department_email) ? $additional_info->department_email : '' ?>" type="text" /></td>
                                                                </tr>
                                                                <tr>
                                                                    <td bgcolor="#f0f0f0">Phone No. :</td>
                                                                    <td bgcolor="#f0f0f0"><input class="validate['required'] profile_inputs" name="phone" value="<?php echo isset($personal_info->phone) ? $personal_info->phone : '' ?>" type="text" /></td>
                                                                </tr>


                                                            </table>

                                                            <!-- Location GOOGLE map -->

                                                            <div class="google_map_view_box"><span class="property_class" style="width:200px; display:block; margin-right:0; float:left;">Google map:</span>
                                                            <span class="value_class">
                                                                <?php if($additional_info->location_google_map!=''){ ?>
                                                                    <a target="_blank" href="<?php echo isset($additional_info->location_google_map) ? $additional_info->location_google_map : '' ?>"> View </a>
                                                                <?php }else{ echo '&nbsp; ';}?>
                                                            </span>
                                                            <span style="margin:5px" class="value_class">
                                                                <input class="profile_inputs" name="location_google_map" value="<?php echo isset($additional_info->location_google_map) ? $additional_info->location_google_map : '' ?>" type="text" /> (Short URL)
                                                            </span>
                                                            </div>

                                                        </div>

                                                    <!-- HIstory -->
                                                    <div class="history_main">
                                                        <div><h3>History :</h3></div>
                                                        <!-- <div><input value="<?php echo isset($personal_info->school_name) ? $personal_info->school_name : '' ?>" type="text" /></div>-->

                                                        <textarea name="history" id="history" class="editor"><?php echo isset($additional_info->profile)?$additional_info->profile:'';?></textarea>
                                                        <script type="text/javascript">
                                                            init_ckfinder('history');
                                                        </script>
                                                    </div>

                                                    <!-- Board members -->
                                                    <div class="board_members_main">
                                                        <div><h3>Board members :</h3></div>
                                                        <!-- <div><input value="<?php echo isset($personal_info->school_name) ? $personal_info->school_name : '' ?>" type="text" /></div>-->
                                                        <textarea name="board_members" id="board_members" class="editor"><?php echo isset($additional_info->board_members)?$additional_info->board_members:'';?></textarea>
                                                        <script type="text/javascript">
                                                            init_ckfinder('board_members');
                                                        </script>
                                                    </div>

                                                    <!-- Type of Campus area -->
                                                    <div class="college_box_main">
                                                        <div class="college_left1"><h3>Type of Campus area :</h3></div>
                                                        <div class="college_right1">
                                                            <!--<select name="campus_type" class="profile_inputs_select">
                                                          <option value="Rural" <?php if($additional_info->type_of_campus_area == 'Rural') echo "selected='selected'";?>>Rural</option>
                                                          <option value="Urban" <?php if($additional_info->type_of_campus_area == 'Urban') echo "selected='selected'";?>>Urban</option>
                                                      </select>-->
                                                            <?php
                                                            $selectid  = isset($additional_info->type_of_campus_area)? $additional_info->type_of_campus_area : ""; ?>
                                                            <select name="campus_type" class="only_for_local">
                                                                <?php get_data('campus_type','type',$selectid);?>
                                                            </select>

                                                            <!-- Institution Type -->
                                                        </div>
                                                        <div class="college_left2"><h3>Institution Type :</h3>
                                                        </div>
                                                        <div class="college_right2">
                                                            <select name="institution_type" class="only_for_local">
                                                                <?php
                                                                $sql = "select type from {$CFG->prefix}school_type where status='active'";
                                                                $school_type_qry = get_records_sql($sql);
                                                                if(isset($school_type_qry))
                                                                {
                                                                    foreach($school_type_qry as $school_type_val)
                                                                    {
                                                                        ?>
                                                                        <option value="<?php echo $school_type_val->type; ?>" <?php if($additional_info->institution_type==$school_type_val->type){ echo "selected";} ?>><?php echo $school_type_val->type; ?></option>
                                                                    <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                            <!--<input class="profile_inputs" name="institution_type" value="<?php echo isset($additional_info->institution_type)? $additional_info->institution_type : '' ?>" type="text" />-->
                                                        </div>
                                                        <div></div>
                                                        <!-- Student Population -->
                                                        <?php
                                                        $selectid  = isset($additional_info->student_population)? $additional_info->student_population : ""; ?>
                                                        <div class="college_left1"><h3>Student Population :</h3>
                                                        </div>
                                                        <div class="college_right1">
                                                            <!--<select name="population" class="profile_inputs_select">
                                                          <?php get_data('school_student_population','title',$selectid);?>
                                                      </select>-->
                                                            <input class="profile_inputs" name="population" value="<?php echo isset($additional_info->student_population)? $additional_info->student_population : '' ?>" type="text" onkeypress="return isNumberKey(event)"/>
                                                        </div>
                                                        <div></div>
                                                        <!-- Affiliations/ Accreditation -->
                                                        <?php
                                                        $selectid  = isset($additional_info->affiliations_id)? $additional_info->affiliations_id : ""; ?>
                                                        <div class="college_left2"><h3>Affiliations/ Accreditation :</h3>
                                                        </div>
                                                        <div class="college_right2">
                                                            <select name="affiliation" class="only_for_locals">
                                                                <?php get_data('schools_affiliations_accreditation','title',$selectid);?>
                                                            </select></div>
                                                        <div></div>
                                                        <!-- Student Body -->
                                                        <?php
                                                        $selectid  = isset($additional_info->student_body)? $additional_info->student_body : ""; ?>
                                                        <div class="college_left1"><h3>Student Body :</h3>
                                                        </div>
                                                        <div class="college_right1">
                                                            <select name="student_body" class="only_for_locals">
                                                                <?php get_data('school_student_body','title',$selectid) ?>
                                                            </select></div>
                                                        <!-- Cultural Diversity -->
                                                        <?php
                                                        $selectid  = isset($additional_info->cultural_diversity)? $additional_info->cultural_diversity : ""; ?>
                                                        <div class="college_left2"><h3>Cultural Diversity :</h3>
                                                        </div>
                                                        <div class="college_right2">
                                                            <select name="cultural_diversity" class="only_for_locals">
                                                                <?php get_data('school_cultural_diversity','title',$selectid) ?>
                                                            </select></div>
                                                        <!-- ONLY FOR LOCALS -->


                                                        <div class="college_left1"><h3>Only for Locals:</h3>
                                                        </div>
                                                        <div class="college_right1">
                                                            <select name="only_for_local" class="only_for_locals">
                                                                <?php
                                                                $local_data = array();
                                                                $local_data[0]='Mostly in-state';
                                                                $local_data[1]='Mostly out-of-state';
                                                                $local_data[2]='Balanced';
                                                                $local_data[3]='Any';

                                                                foreach($local_data as $ldata)
                                                                {
                                                                    ?>
                                                                    <option value="<?php echo $ldata; ?>" <?php if($additional_info->only_for_local==$ldata){ echo "selected";} ?>>
                                                                        <?php echo $ldata; ?>
                                                                    </option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                      <div></div>
                                                      <br />
                                                        <div style="width:100%; float:left;"><input type="submit" name="update_school" value="Update"/></div>
                                                      <div class="clear"></div>
                                                      <div class="clear"></div>
                                                    <!-- << inner content>> -->
                                                  </div>
                                                <?php
                                            } else {
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
