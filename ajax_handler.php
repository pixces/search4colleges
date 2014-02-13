<?php
	require_once("calendar/inc_calendar.class.php");
	require_once('cmiadmin/config.php');


if(isset($_POST['flag']) && $_POST['flag']=='alter_calendar_detail')
{
$oCalendar = new inc_calendar();

	if(isset($_POST['month']))
		$month = $_POST['month'];
	if(isset($_POST['year']))
		$year = $_POST['year'];
	$oCalendar->dateNow($month,$year);

	$oCalendar->showThisMonth();


}

	if (isset($_GET['image']) && $_GET['image'] != ''){
		
		$images = trim($_GET['image'],",");
		$images = explode(",",$images);
		
		foreach($images as $data){
			$sortorder = get_sort_order('gallery');
			$fancy = new object();
			$fancy->image = $data;
			$fancy->caption = '';
			$fancy->sort_order = $sortorder;
			$fancy->status = 'active';
			$fancy->added_date = time();

			if(!empty($fancy)){
				echo $insert = insert_record('gallery',$fancy);
			}
		}
		
	}

	if (isset($_GET['deleteid']) && $_GET['deleteid'] != '0'){
		
		$sql  = "Delete From ".$CFG->prefix."gallery where id = ".$_GET['deleteid'];
		
		if(execute_sql($sql))
			echo "deleted record";
		
	}
    if(isset($_POST['flag']) and $_POST['flag'] == "delete_comment")
	{
		$sql  = "Delete From ".$CFG->prefix."blog_comment where id = ".$_POST['id'];
		if(execute_sql($sql))
			echo "deleted record";
		
	}
	if(isset($_POST['sort_order'])){
		
		$ids = explode('|',$_POST['sort_order']);
	
		foreach($ids as $index=>$id)
		{
			if($id != '')
			{
				$fancy = new object();
				$fancy->id = $id;
				$fancy->sort_order= $index;
				update_record('gallery',$fancy);
			}
		}
		echo '';
	}
	if(isset($_GET['flag']) and $_GET['flag'] == "statename_form")
	{
		$country_id = $_GET['id'] ;
		$parentsql = "SELECT id,name,country_id FROM ".$CFG->prefix."state WHERE status = 'active'AND country_id = '".$country_id."' ORDER BY name";;
		$parent_zone = get_records_sql($parentsql);
		$selected = '';
		$html = '';
		$html .= "<option value='0'>Select State</option>";
		if(!empty($parent_zone))
		{
			foreach($parent_zone as $data)
			{
					if($_GET['id'] == $data->country_id)
					{
						$selected = 'selected=selected';
					}
					else
						$selected ='';							
					$html .= "<option value=".$data->id ." $selected>".$data->name."</option>";
			}
		}
		echo $html;
	}
	//check exists
	if(isset($_POST['check_exist']))
	{
		$like_object = new object();
		$like_object->table 	 = optional_param('table','',PARAM_TEXT);
		$like_object->field 	 = optional_param('field','',PARAM_TEXT);
		$like_object->value 	 = optional_param('value','',PARAM_TEXT);
		$like_object->current_id = optional_param('current_id','',PARAM_TEXT);
		
		$return_value = check_exist($like_object);
		
		if(empty($return_value))
		{
			//exist
			echo 1;exit;
		}
		else
		{
			//not exist	
			echo 0;exit;
		}
	}
	if (isset($_POST['flag']) && $_POST['flag']=='check_email'){
		//$user_data = get_record("fe_users", "email", $_POST['email'],'status','active');
		$user_data = get_record("fe_users", "email", $_POST['email']);
        if(!empty($user_data))
		{
			echo  "yes";
		}
		exit;
	}

	if (isset($_POST['flag']) && $_POST['flag']=='check_email_confirm'){
		
		$email = strtolower($_POST['email']);
		$confirm_email  = strtolower($_POST['confirm_email']);
		if(($email != '' && $confirm_email != '') && $email != $confirm_email)
		{
			echo  "yes";
		}
		exit;
	}
	
	if (isset($_POST['flag']) && $_POST['flag']=='check_current_password'){
		$password = md5($_POST['current_password']);
		$user_id = $_SESSION['s4c_user_id'];
		$sql = "select * from {$CFG->prefix}fe_users where id=$user_id and password='$password'";

		$user_data = get_record_sql($sql);
		if(empty($user_data))
		{
			echo  "yes";
		}
		//exit;
	}
	if (isset($_POST['flag']) && $_POST['flag']=='email_exists'){
	//
		$user_data = get_record("fe_users", "email", $_POST['emailid']);

		if(!empty($user_data))
		{
			echo  "yes";
		}
		exit;
	}

        
        
        // here is the personal details form 
        /*
         * que horror 
         */
	if(isset($_POST['flag']) && $_POST['flag']=='edit_personal_detail') { 
		$personal_info = get_personal_info($_SESSION['s4c_user_id']);
		echo '<form name="personal_detail_form" id="personal_detail_form" method="post" onsubmit="">';
		echo '<table width="500" border="123"  >';
		if($personal_info){
			echo" 
				 <tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				 </tr>";
				 
			foreach($personal_info as $key=>$value){	
			
			                $title='';
							 if($key=="address")
							 {
							   $title="Address Line1";
							 }
							 else if($key=="street"){
							   $title="Address Line2";
							 }else if($key=="proffession")
							 {
							   $title="profession";
							 }else{
							   $title=ucwords(str_replace('_',' ',$key));
							 }				
				echo"
					<tr>
						<td width='22%'><div align='right'>".$title."</div></td>
						<td width='75%'>";
							if($key == 'date_of_birth'){
								//date("m")  , date("d")+1, date("Y"));								
								
								$day_month_year = explode('/',$value);
								
								
								$day	= date("d",time());
								$month	= date("m",time());
								$year	= date("Y",time());
								//echo  " $day : $month : $year ";
								$num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
								echo "<select name='date_month' id='date_month' >";
								$month_array = array('Select Month','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
								foreach($month_array as $key=>$value){
									if($key != 0){
										if(isset($day_month_year[0]) && $month == $day_month_year[0]){
											echo "<option value='$key' selected>$value</option>";
										}else{
											echo "<option value='$key'>$value</option>";
										}
									}
								}
								echo "</select> ";


								echo "<select name='date_day' id='date_day' >";
									for($i = 1; $i<=$num; $i++){
										if(isset($day_month_year[1]) && $i == $day_month_year[1]){
											echo "<option value='$i' selected >$i</option>";
										}else{
											echo "<option value='$i'>$i</option>";
										}
									}
								echo"</select>";

								echo "<select name='date_year' id='date_year' >";
									$year_start = 1910;
									$year_end	= date('Y')-5;
									for($i = $year_start; $i<$year_end; $i++){
										if(isset($day_month_year[2]) && $day_month_year[2] == $i){
											echo "<option value='$i' selected >$i</option>";
										}else{
											echo "<option value='$i'>$i</option>";
										}
									}
								 echo "</select>";
								/* echo"
									<input readonly='true' name='$key' id='$key' type='text' class='input-text2  date  demo_vista1' style='width:150px;' value='$value'> ";
									*/
							}elseif($key == 'gender'){
								echo "<select name='$key' id='$key' >";
									if($value == 'female'){
										echo "<option value='male' >male</option><option value='female' selected='selected'>female</option>";										
									}else{
										echo "<option value='male' selected='selected'>male</option><option value='female'>female</option>";
									}
								echo "</select>";
								//echo "<input type='text' name='$key' id= '$key' value='".$value."' />";
							}elseif($key == 'web_url'){
								echo '<input type="text" name="'.$key.'" id= "'.$key.'" value="'.$value.'" class="validate[\'required\',\'url\']"/>';
								//echo "<input type='text' name='$key' id= '$key' value='".$value."' />";
							}
							elseif($key == 'state')
							{
								$state_value = $value ;	
								$getstate = get_records_sql('SELECT * FROM '.$CFG->prefix.'state WHERE status="active"');
									echo '<select name="state" id="state">';
									echo '<option value="">Select State</option>';
									foreach($getstate as $data)
									{
										if($data->id == $state_value)
										{
											echo '<option value="'.$data->id.'" selected="selected" >'.$data->name.'</option>';
										}
										else
										{
											echo '<option value="'.$data->id.'">'.$data->name.'</option>';
										}
										
									}
								echo '</select>';
							
							}
							elseif($key == 'city'){

								$getstate = get_record_sql('SELECT * FROM '.$CFG->prefix.'city WHERE status="active" and id = "'.$personal_info->city.'"');
								if(isset($getstate->name)&& !empty($getstate->name))
								{
								echo '<input type="text" name="city" id= "city" value="'.$getstate->name.'" class="validate[\'required\']"/>';
								}else{
								echo '<input type="text" name="city" id= "city" value="" class="validate[\'required\']"/>';
								}
								/*echo '<select name="city" id="city" >';
									echo '<option value="">Select City</option>';
									if($getstate)
									{
										foreach($getstate as $data)
										{
											if($data->id == $value)
											{
												echo '<option value="'.$data->id.'" selected="selected" >'.$data->name.'</option>';
											}
											else
											{
												echo '<option value="'.$data->id.'">'.$data->name.'</option>';
											}
											
										}
									}
								echo '</select>';*/
							
							}
							else{
								if($key=="street"){
								echo "<input type='text' name='$key' id= '$key' value='".$value."' />";
								}else{
								echo '<input type="text" name="'.$key.'" id= "'.$key.'" value="'.trim($value).'" class="validate[\'required\']"/>';               }
							}
						echo"</td>
					</tr>";
			}
			echo '<tr><td colspan="3" align="center" ><a href="javascript:void(0);" onclick="checkPersionalDetails();" ><img src="images/save_button.jpg" /></a>&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="populate_personal_detail();" ><img src="images/cancel_bottom.jpg" /></a></td></tr>';
			
		}	
		echo '</table>';
		echo '</form>';
	}
	if(isset($_POST['flag']) && $_POST['flag']=='update_personal_detail') {
	
		$personal_info	= get_personal_info($_SESSION['s4c_user_id']);
		$date_month		= optional_param('date_month','0',PARAM_INT);
		$date_day		= optional_param('date_day','0',PARAM_INT);
		$date_year		= optional_param('date_year','0',PARAM_INT);
		$state			= optional_param('state','0',PARAM_INT);
		$zip_code		= optional_param('zip_code','0',PARAM_INT);
		
		
		$update_array = array();
		
		if($_POST){
			foreach($personal_info as $key=>$value){	
				if(array_key_exists($key,$_POST)){
				    if($key=="city")
					{
					                $city_string=$_POST[$key];
								$sql 							= "SELECT * FROM {$CFG->prefix}city WHERE name LIKE '%$city_string%' and status = 'active'";
                                    $city 							= get_record_sql($sql);
                                    
                                    if(!empty($city)){
									    $update_array[$key] = $city->id;
                                        //$city						= $city->id;
                                        }else{
									                 $rec  = execute_sql("INSERT INTO ".$CFG->prefix."city SET
                                                                        state_id		= ".$state.", 	
                                                                        pincode 		= '',
                                                                        zipcode 		= '".$zip_code."',
                                                                        code 			= '".$city_string."',
                                                                        name 			= '".$city_string."',
                                                                        added_date 		= ".time().",
                                                                        status 			= 'active'",false);
                            
                                            if($rec){
                                                    $city = mysql_insert_id();
													$update_array[$key] = $city;
                                                 }
                                    }
					}
					else
					{
					   $update_array[$key] = $_POST["$key"];
					}	
				}
			}
		}
		
		$table = '';
		$reg_type = $_SESSION['user_type'];
		
		switch($reg_type){
			case 'student':$table = 'student'; break;
			case 'counselor':$table = 'counselors';break;
			case 'parent':$table = 'parent';break;
			case 'school':$table = 'schools';break;
			case 'teacher':$table = 'teacher';break;
		}

		if($table != 'schools'){
			$date_of_birth					= $date_month . '/' . $date_day . '/' . $date_year;
			$update_array['date_of_birth']  = $date_of_birth;
		}
		
		$id					= get_field($table,'id','user_id',$_SESSION['s4c_user_id']);
		$update_array['id'] = $id;
		
		$update_record = (object)$update_array;
		print_object($update_record);
	
		if($update_record){
			if($update = update_record($table, $update_record))
			{				
				echo "updated";
			}else{
				echo "unabble to update";
			}
		}

	}

	if(isset($_POST['flag']) && $_POST['flag']=='populate_personal_detail') {


		$personal_info = get_personal_info($_SESSION['s4c_user_id']);		
		
		echo '<table width="500" border="0"  >';
		if($personal_info){
			echo" 
				 <tr>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				 </tr>";
			foreach($personal_info as $key=>$value){
							$title='';
							 if($key=="address")
							 {
							   $title="Address Line1";
							 }
							 else if($key=="street"){
							   $title="Address Line2";
							 }else if($key=="proffession")
							 {
							   $title="profession";
							 }else{
							   $title=ucwords(str_replace('_',' ',$key));
							 }						
				echo"
					<tr>
					<td width='28%'><b><div align='right' style='margin-right:10px;'>".$title ."</div></b></td>
					<td width='70%'><div style='margin-left:100px;'>";
						
						if($key == 'state' || $key == 'city'){
							if($value != ''){
								echo get_field($key,'name','status','active','id',$value);
							}
						}else{
						       if($key=="date_of_birth")
							   {
									echo date('F d, Y',strtotime($value));
							   }else
							   {
									echo $value;
							   }
						}
						
						//echo $value;
					echo "</div></td>";
					echo "</tr>";
			}
		}
		echo '</table>';
	}

if(isset($_POST['flag']) && $_POST['flag']=='edit_staff_section') 
{

$school_staff_user = get_records_sql('Select * from '.$CFG->prefix.'school_staff_user where id = '.$_POST['id'].' and status = "active"');
$arr_sections = '';
$checked = '';

	$allowed_sections = array('colleges_profile','college_leads','college_news','college_statistics','colleges_admissions','colleges_contact_information','colleges_culture_campus_life','colleges_degrees','colleges_download_brochures','colleges_gallery','colleges_scholarships','colleges_send_enquiry','college_membership','college_affiliated_banks','colleges_related_colleges','events');

	asort($allowed_sections);

	if(!empty($school_staff_user))
		{ 
				echo '<table border="1" cellspacing="0" cellpadding="8" width="100%">
						<tr>
							<th width="25%" bgcolor="#ededed">Name</th>
							<th width="70%" bgcolor="#ededed">Allowed sections</th>
							<th width="10%" bgcolor="#ededed">Save</th>
						</tr>';


$sections = '';
					foreach($school_staff_user as $data){ 
						
							if(isset($data->allowed_sections)){
								$arr_sections = explode(',',$data->allowed_sections);
								
								foreach($arr_sections as $data1){
									//$sections .= $data1."<br />";
								}
								foreach($allowed_sections as $data1){

									if(in_array($data1,$arr_sections)){
										$checked = "checked='checked'";
									}
									else{
										$checked = "";
									}
									$sections .= "<div style='width:220px; float:left;'><input class='".$data->id."' type='checkbox' name='".$data->id."[]' ".$checked." value='".$data1."'>".$data1."<br /></div>";
								}
							}
						
					echo '	<tr>';
					echo '	<td valign="top"><input name="namer" id="namer" type="text" value="'.$data->name.'" id="'.$data->id.'"> </td>';
					echo '	<td>'.$sections.'</td>';
					echo '	<td valign="top"><img style="cursor:pointer" onclick="save_me('.$data->id.')" src="images/save.png" title="save" alt="Save"></td></tr>';
						}
				echo '</table>';

				 }
			

}


	if(isset($_POST['flag']) && $_POST['flag']=='editAcademic') {

		$id = optional_param('id','',PARAM_INT);
		$personal = get_record('academic','id',$id);

		$sql = "select * from {$CFG->prefix}academic where student_id=".$_SESSION['s4c_user_id'];
		$personal_info = get_records_sql($sql);
		echo"<form name='academic_form' id='academic_form' method='post'>	<table  border='0' cellspacing='10' cellpadding='2'>		
				<tr>
					<th width='125px'>Institute Name</th>
					<th width='125px'>City </th>
					<th width='125px'>Degree </th>
					<th width='250px'>Graduation Date</th>
					<th width='100px'>&nbsp;</th>
				</tr>";
		
		if($personal_info){
            $school_type = get_records_sql('Select * from '.$CFG->prefix.'school_type');
			foreach($personal_info as $record){
				if($record->id == $personal->id){							
				echo"
					<tr>
					<td><input type='text' size='18' name='institute_name_change' id= 'institute_name_change' value='$record->institute_name' class=\"validate['required']\"  /></td>
					<td><input type='text' size='18' name='city_change' id= 'city_change' value='$record->city' class=\"validate['required']\"/></td>
					<td><select name='degree_change' id='degree_change' class=\"validate['required']\" style='width:150px;' >";
                    foreach($school_type as $rec){
                        if($record->degree == $rec->type){
                            echo "<option value='".$rec->type."' selected >".$rec->type."</option>";
                        } else {
                            echo "<option value='".$rec->type."'>".$rec->type."</option>";
                        }
                    }
                    echo "</select></td><td>";
					$val_date = date("m/d/Y",$record->year_of_passing);
					$day_month_year = explode('/',$val_date);
					
								$day	= date("d",time());
								$month	= date("m",time());
								$year	= date("Y",time());
								//echo  " $day : $month : $year ";
								$num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
								echo "<select name='date_month' id='date_month' >";
								$month_array = array('Select Month','Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec');
								foreach($month_array as $key=>$value){
									if($key != 0){
										if(isset($day_month_year[0]) && $key == $day_month_year[0]){
											echo "<option value='$key' selected>$value</option>";
										}else{
											echo "<option value='$key'>$value</option>";
										}
									}
								}
								echo "</select> ";


								//echo "<select name='date_day' id='date_day' >";
								//	for($i = 1; $i<=$num; $i++){
								//		if(isset($day_month_year[1]) && $i == $day_month_year[1]){
								//			echo "<option value='$i' selected >$i</option>";
								//		}else{
								//			echo "<option value='$i'>$i</option>";
								//		}
								//	}
								//echo"</select>";

								echo "<select name='date_year' id='date_year' >";
									$year_start = 1910;
									$year_end	= date('Y')+1;
									for($i = $year_start; $i<$year_end; $i++){
										if(isset($day_month_year[2]) && $day_month_year[2] == $i){
											echo "<option value='$i' selected >$i</option>";
										}else{
											echo "<option value='$i'>$i</option>";
										}
									}
								 echo "</select></td>";
					/*<td ><input name='year_of_passing_change' id='year_of_passing_change' type='text' class='input-text2  date  demo_vista1' style='width:150px;' value='$record->year_of_passing'></td>";*/

					echo "<td><a href='javascript:void(0);' onclick='updateAcademic($record->id)'>Save</a>&nbsp;&nbsp;<a href='javascript:void(0);' onclick='cancelAcademic()'>Cancel</a></td>";
					echo "</tr>";
				}else{
					echo"
					<tr id='academic_id_$record->id'>
					<td>".ucwords($record->institute_name) ."</td>
					<td>".ucwords($record->city) ."</td>
					<td>".ucwords($record->degree) ."</td>
					<td >".date('F, Y',$record->year_of_passing) ."</td>";
					echo "<td>&nbsp;</td>";
					echo "</tr>";
				}
			}
			
		}
		echo "</table></form>";
	}

	if(isset($_POST['flag']) && $_POST['flag']=='updateAcademic') {	
		
		
		$institute_name		= optional_param('institute_name','',PARAM_RAW);
		$degree				= optional_param('degree','',PARAM_RAW);
		$city               = optional_param('city','',PARAM_RAW);
        $date_month			= optional_param('date_month','0',PARAM_INT);
		$date_day			= optional_param('date_day','0',PARAM_INT);
		$date_year			= optional_param('date_year','0',PARAM_INT);
		$year_of_passing	= $date_month . '/01/' . $date_year;
		$year_of_passing    = strtotime($year_of_passing);
		//$year_of_passing	= optional_param('year_of_passing','',PARAM_RAW);		
		$id					= optional_param('id','',PARAM_RAW);

		$update_record = new object();
		$update_record->institute_name	= $institute_name;
		$update_record->degree			= $degree;
        $update_record->city			= $city;
		if(empty($year_of_passing)){			
			$update_record->year_of_passing	= time();
		}else{
			$update_record->year_of_passing	= $year_of_passing;
		}
		$update_record->id				= $id;
		if($update_record->institute_name){
			$update = update_record('academic', $update_record);
		}
	}

	if(isset($_POST['flag']) && $_POST['flag']=='update_event') {	
		
		$name		= optional_param('name','',PARAM_RAW);
		$opening_date		= optional_param('opening_date','',PARAM_RAW);
		$closing_date	= optional_param('closing_date','',PARAM_RAW);
		$short_description	= optional_param('description','',PARAM_RAW);
		$id					= optional_param('id','',PARAM_RAW);

		$update_record = new object();
		$update_record->name				= $name;
		$update_record->opening_date		= $opening_date;
		$update_record->closing_date		= $closing_date;
		$update_record->short_description	= $short_description;
		$update_record->id					= $id;
		
		if(!empty($update_record)){			
			$update = update_record('event', $update_record);
		}
	}

	if(isset($_POST['flag']) && $_POST['flag']=='update_manage_users') {	
		
		$name		= optional_param('namer','',PARAM_RAW);
		$sections	= optional_param('sections','',PARAM_RAW);
		$id			= optional_param('id','',PARAM_RAW);

		$update_record = new object();
		$update_record->name				= $name;
		$update_record->allowed_sections 	= $sections ;
		$update_record->id					= $id;
		
		if(!empty($update_record)){			
			echo $update = update_record('school_staff_user', $update_record);
			exit;
		}
	}
	
	if(isset($_POST['flag']) && $_POST['flag']=='delete_event') {	

		$id	 = optional_param('id','',PARAM_RAW);
		
		if($id){
			$update = delete_records('event','id',$id);
		}
	}

	if(isset($_POST['flag']) && $_POST['flag']=='delete_manage_user') {	

		$id	 = optional_param('id','',PARAM_RAW);
		
		$staff_id = get_field('school_staff_user','fe_staff_id','id',$id);

		if($id){
			$update = delete_records('school_staff_user','id',$id);
		}

		if($staff_id){
			$update_id = delete_records('fe_users','id',$staff_id);
		}
	}
	
	if(isset($_POST['flag']) && $_POST['flag']=='deleteAcademic') {	

		$id					= optional_param('id','',PARAM_RAW);
		
		if($id){
			$update = delete_records('academic','id',$id);
		}
	}


	if(isset($_POST['flag']) && $_POST['flag']=='addAcademic') {	
		
		$institute_name		= optional_param('institute_name','',PARAM_RAW);
		$degree				= optional_param('degree','',PARAM_RAW);
        $city               = optional_param('city','',PARAM_RAW);
		//$year_of_passing	= optional_param('year_of_passing','',PARAM_RAW);
		$date_month			= optional_param('date_month','0',PARAM_INT);
		$date_year			= optional_param('date_year','0',PARAM_INT);
        //$date_day			= optional_param('date_day','0',PARAM_INT);
        $year_of_passing	= strtotime( $date_month . '/01/' . $date_year);
		$student_id			= $_SESSION['s4c_user_id'];

		$update_record = new object();
		$update_record->institute_name	= $institute_name;
		$update_record->degree			= $degree;
        $update_record->city            = $city;

		if(empty($year_of_passing)){
			$update_record->year_of_passing	= time();
		}else{
			$update_record->year_of_passing	= $year_of_passing;
		}
		$update_record->student_id		= $student_id;
		$update_record->added_date		= time();
		if($update_record->institute_name){
			$update = insert_record('academic', $update_record);
		}
	}

	if(isset($_POST['flag']) && $_POST['flag']=='populateAcademic') {
		global $CFG;
		$sql = "select * from {$CFG->prefix}academic where student_id=".$_SESSION['s4c_user_id'];
		$personal_info = get_records_sql($sql);
		echo "<table  border='0' cellspacing='10' cellpadding='2'>";
		if($personal_info){
			echo"			
				<tr>
					<th width='125px'>Institute Name</th>
					<th width='125px'>City </th>
					<th width='125px'>Degree </th>
					<th width='250px'>Graduation Date</th>
					<th width='100px'>&nbsp;</th>
				</tr>";
			
			foreach($personal_info as $record){
				echo"
					<tr>
					<td>".ucwords($record->institute_name) ."</td>
					<td>".ucwords($record->city) ."</td>
					<td>".ucwords($record->degree) ."</td>
					<td >".date('F, Y',$record->year_of_passing) ."</td>";
					echo "<td><a href='javascript:void(0);' onclick='editAcademic($record->id);' ><img src='images/edit.gif' border='0' /></a>&nbsp;&nbsp;<a href='javascript:void(0);' onclick='deleteAcademic($record->id);' ><img src='images/delete.png' border='0' /></a></td>";
					echo "</tr>";
			}
		}
		
		echo "<tr><td colspan='4'><a href='javascript:void(0);' onclick='showAcademic()'>Please list your past your last schools attended starting from high school.</a></td></tr>";
		echo "</table";
	}

	if(isset($_POST['flag']) && $_POST['flag']=='showAcademic') {
		global $CFG;
		$sql = "select * from {$CFG->prefix}academic where student_id=".$_SESSION['s4c_user_id'];
		$personal_info = get_records_sql($sql);
		echo "<form name='academic_form' id='academic_form' method='post'><table  border='0' cellspacing='10' cellpadding='2' width='100%'>";
		echo"			
				<tr>
					<th width='125px'>Institute Name</th>
					<th width='125px'>City</th>
					<th width='125px'>Degree </th>
					<th width='250px'>Graduation Date</th>
					<th width='50px'>&nbsp;</th>
				</tr>";

		if($personal_info){			
			foreach($personal_info as $record){
				echo"
					<tr>
					<td>".ucwords($record->institute_name) ."</td>
					<td>".ucwords($record->city) ."</td>
					<td>".ucwords($record->degree) ."</td>
					<td >".date('d/m/Y',$record->year_of_passing) ."</td>";
					echo "<td><a href='javascript:void(0);' onclick='editAcademic($record->id);' ><img src='images/edit.gif' border='0' /></a>&nbsp;&nbsp;<a href='javascript:void(0);' onclick='deleteAcademic($record->id);' ><img src='images/delete.png' border='0' /></a></td>";
					echo "</tr>";
			}
		}

        //get details of school type
        $school_type = get_records_sql('Select * from '.$CFG->prefix.'school_type');
		echo"
			<tr>
			<td><input type='text' size='18' name='institute_name_add' id= 'institute_name_add' value='' class=\"validate['required']\"/></td>
			<td><input type='text' size='18' name='city_add' id= 'city_add' value='' class=\"validate['required']\"/></td>
			<td><select name='degree_add' id='degree_add' class=\"validate['required']\" style='width:150px;' >";
                foreach($school_type as $rec){
                    echo "<option value='".$rec->type."'>".$rec->type."</option>";
                }
			echo "</select></td>
			<td  >";
								$day	= date("d",time());
								$month	= date("m",time());
								$year	= date("Y",time());
			$num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
								echo "<select name='date_month' id='date_month' >";
								$month_array = array('Select Month','Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec');
								foreach($month_array as $key=>$value){
									if($key != 0){
										echo "<option value='$key'>$value</option>";
										}
								}
								echo "</select> ";


								//echo "<select name='date_day' id='date_day' >";
									//for($i = 1; $i<=$num; $i++){
									//		echo "<option value='$i'>$i</option>";
									//}
								//echo"</select>";

								echo "<select name='date_year' id='date_year' >";
									$year_start = 1910;
									$year_end	= date('Y')+1;
									for($i = $year_start; $i<$year_end; $i++){
										echo "<option value='$i'>$i</option>";
									}
								 echo "</select></td>";
			/*<td ><input readonly='true' name='year_of_passing_add' id='year_of_passing_add' type='text' class='input-text2  date  demo_vista1' style='height: 18px; width:150px;' value=''></td>";*/
			echo "<td><a href='javascript:void(0);' onclick='addAcademic()'>Save</a></td>";
			echo "</tr>";
		echo "</table></form>";
	}

	if(isset($_POST['flag']) && $_POST['flag']=='delete_gallery') {
		global $CFG;
		
		$id		= optional_param('id','',PARAM_RAW);
		if($id){
			if($new= delete_records('gallery', 'id', $id)){
				echo "updated";
			}else{
				echo "unable to update";
			}
		}else{
				echo "unable to update";
			}		
	}

	if(isset($_POST['flag']) && $_POST['flag']=='delete_school_gallery') {
		global $CFG;
		
		$id		= optional_param('id','',PARAM_RAW);
		if($id){
			if($new= delete_records('school_image_video', 'id', $id)){
				echo "updated";
			}else{
				echo "unable to update";
			}
		}else{
				echo "unable to update";
			}		
	}

	if(isset($_POST['flag']) && $_POST['flag']=='editAbout') {
		$reg_type = $_SESSION['user_type'];
		$user_id = $_SESSION['s4c_user_id'];
		$table ='';	
		 echo '<form name="about_form" id="about_form" method="post" onsubmit="updateAbout();return false;">';
		if($reg_type == 'student'){
			echo"<table>";

			$studeny_info = get_record('student','user_id',$user_id);
			if($studeny_info){
				//$short_description	=$studeny_info->short_description;
				$long_description	=$studeny_info->long_description;
				$goals				=$studeny_info->goals_in_life;			
				$expectation		=$studeny_info->expectation_from_s4c;
			}
?>
		
		<tr>		
			<td align="right" colspan="3"><input type="image" name="submit" src="images/save_button.jpg" />&nbsp;&nbsp;&nbsp;<a href='javascript:void(0);' onclick='populateAbout();' ><img src="images/cancel_bottom.jpg" /></a><!-- <a href='javascript:void(0);' onclick='updateAbout();' >Save</a> --></td>
		</tr>
		<!--<tr>
			<td style="width:130px; " valign='top' ><b>Short description</b></td>
			<td >&nbsp;&nbsp;&nbsp;</td>
			<td class="about_me_description"><textarea name="short_description" id="short_description" cols="40" rows="5" class="validate['required']"><?php echo isset($short_description)?$short_description:'';?></textarea><br /><br /></td>
		</tr>	-->	
		<tr>
			<td valign='top'><b>About Yourself</b></td>
			<td>&nbsp;&nbsp;&nbsp;</td>
			<td >
				<textarea name="long_description" id="long_description" class="editor txtBox"><?php echo isset($long_description)?$long_description:'';?></textarea>
				<script type="text/javascript">
					init_ckfinder('long_description');
				</script><br />
			</td>
		</tr>	
		<tr>
			<td valign='top'><b>Career Goals</b></td>
			<td>&nbsp;&nbsp;&nbsp;</td>
			<td>
				<textarea name="goals" id="goals" class="editor txtBox"><?php echo isset($goals)?$goals:'';?></textarea>
				<script type="text/javascript">
					init_ckfinder('goals');
				</script><br />
			</td>
		</tr>	
		<tr>
			<td valign='top'><b>College selection requirements(tell us about your requirements in selecting a college)</b></td>
			<td>&nbsp;&nbsp;&nbsp;</td>
			<td>
				<textarea name="expectation" id="expectation" class="editor txtBox"><?php echo isset($expectation)?$expectation:'';?></textarea>
				<script type="text/javascript">
					init_ckfinder('expectation');
				</script><br />
			</td>
		</tr>
		


<?php
		echo"</table>";
		}
		echo '</form>';
	}

	if(isset($_POST['flag']) && $_POST['flag']=='editAboutOther') {
		$reg_type = $_SESSION['user_type'];
		$user_id = $_SESSION['s4c_user_id'];
		$table ='';
		
		switch($reg_type){
			case 'counselor':$table = 'counselors';break;
			case 'parent':$table = 'parent';break;
			case 'school':$table = 'schools';break;
			case 'teacher':$table = 'teacher';break;
		}
		if(!empty($table)) {

			$studeny_info = get_record($table,'user_id',$user_id);
			if($studeny_info){
				$about_me	=$studeny_info->about_me;
			}
		}
?>
		
		<form name="aboutother_form" id="aboutother_form" method="post" onsubmit="updateAboutOther();return false;">
		<table border="0" width="100%">
		<tr>
			<td >
				<textarea name="about_me" id="about_me" class="editor txtBox" ><?php echo isset($about_me)?$about_me:'';?></textarea>
				<script type="text/javascript">
					init_ckfinder('long_description');
				</script><br />
			</td>
		</tr>
		<tr>
			<td >
			<!-- <a href='javascript:void(0);' onclick='updateAboutOther();' >Save</a> -->
			<input type="image" src="images/save.jpg" />
			
			</td>
		</tr>
		</form>
		</table>


<?php	
	}

	if(isset($_POST['flag']) && $_POST['flag']=='updateAbout') {

		//$short_description	= trim(optional_param('short_description','',PARAM_RAW));
		$long_description	= trim(optional_param('long_description','',PARAM_RAW));
		$goals				= trim(optional_param('goals','',PARAM_RAW));
 		$expectation		= trim(optional_param('expectation','',PARAM_RAW));
		$update_record = new object();
		/*if(!empty($short_description)){
			$update_record->short_description	= $short_description;
		}*/

		$update_record->long_description	= $long_description;
		$update_record->goals_in_life	= $goals;
		$update_record->expectation_from_s4c	= $expectation;
		if(!empty($update_record)){
			$reg_type = $_SESSION['user_type'];
			
			 
			$table ='';			
			switch($reg_type){
				case 'student':$table = 'student';break;
				case 'counselor':$table = 'counselors';break;
				case 'parent':$table = 'parent';break;
				case 'school':$table = 'schools';break;
				case 'teacher':$table = 'teacher';break;
			}
			$update_record->id = get_field($table,'id','user_id',$_SESSION['s4c_user_id']);
			if($new = update_record($table,$update_record)){
				echo "successfully  updated ";
			}else{
				echo "unable to update";
			}
		}
	}
	if(isset($_POST['flag']) && $_POST['flag']=='updateAboutOther') {

		$about_me	= trim(optional_param('about_me','',PARAM_RAW));
		$update_record = new object();	
		if(!empty($about_me) && ($about_me !='<br />')){
			$update_record->about_me	= $about_me;
		}else{
			$update_record->about_me	= '';
		}
		
		if(!empty($update_record)){
			$reg_type = $_SESSION['user_type'];
			 
			$table ='';			
			switch($reg_type){
				case 'counselor':$table = 'counselors';break;
				case 'parent':$table = 'parent';break;
				case 'school':$table = 'schools';break;
				case 'teacher':$table = 'teacher';break;
			}
			$update_record->id = get_field($table,'id','user_id',$_SESSION['s4c_user_id']);
			if($new = update_record($table,$update_record)){
				echo "successfully  updated ";
			}else{
				echo "unable to update";
			}
		}
	}

	if(isset($_POST['flag']) && $_POST['flag']=='populateAbout') {
		
		$reg_type = $_SESSION['user_type'];
		$user_id = $_SESSION['s4c_user_id'];
		$table ='';
		
		switch($reg_type){
			case 'student':$table = 'student';break;
			case 'counselor':$table = 'counselors';break;
			case 'parent':$table = 'parent';break;
			case 'school':$table = 'schools';break;
			case 'teacher':$table = 'teacher';break;
		}
		$studeny_info = get_record($table,'user_id',$user_id);
		//print_r($studeny_info);
		$show = 0;
		if($studeny_info){
			if($table =='student'){
				//$short_description = trim($studeny_info->short_description);
				$long_description =  trim($studeny_info->long_description);
				$goals_in_life =  trim($studeny_info->goals_in_life);
				$expectation_from_s4c = trim($studeny_info->expectation_from_s4c);

				if(!empty($long_description) || !empty($goals_in_life) || !empty($expectation_from_s4c)) 
					$show =1;
			}else{
				$about_me = trim($studeny_info->about_me);
				if(!empty($about_me))
					$show =1;
			}
		}	

		if($studeny_info){
			echo "<div>";
			if($show == 0){
				$edit_function = ($table =='student')?'editAbout':'editAboutOther';
				echo"<div style='text-align:left;font-size:17px;'><a href='javascript:void(0);' onclick='$edit_function();' >Add</a><div>";	
			        
                                
                        }else{
				$edit_function = ($table =='student')?'editAbout':'editAboutOther';
				echo"<div style='text-align:left;'><a href='javascript:void(0);' onclick='$edit_function();' ><img src='images/edit.gif' border='0' /></a><div>";	
				if($table =='student'){
						//echo"<b>Short Description  :</b> <p> $studeny_info->short_description </p>";
						?><div style="width:100%; float:left;"><?php  ?> </div><?php
						if((!empty($studeny_info->long_description)) &&($studeny_info->long_description !='<br />')){
							echo"<b>Description  :</b> <p> $studeny_info->long_description </p>";
						}				
						?><div style="width:100%; float:left;"><?php  ?> </div><?php
						if((!empty($studeny_info->goals_in_life)) &&($studeny_info->goals_in_life !='<br />')){
							echo"<b>Life Goals  :</b> <p> $studeny_info->goals_in_life </p>";
						}
						?><div style="width:100%; float:left;"><?php  ?> </div><?php
						if((!empty($studeny_info->expectation_from_s4c)) && ($studeny_info->expectation_from_s4c !='<br />')){
							echo"<b>Expectation From S4C  :</b> <p> $studeny_info->expectation_from_s4c </p>";
						}
				}else{
						echo"<p> $studeny_info->about_me </p>";
				}	
			}
			echo "</div>";
		}else{
			echo "No Records Found ";
		}
	}

	  if(isset($_POST['flag']) && $_POST['flag']=='populateAchievements') {
		
		$user_id = $_SESSION['s4c_user_id'];
		$studeny_info = get_records('achievement','user_id',$user_id);
		if($studeny_info){
			foreach($studeny_info as $record){
			if(isset($record->year)){
			 $date=date("F d, Y",$record->year);
			}else{
			$date='';
			}
				if(empty($record->image)){
					$image = "<img src='$CFG->siteroot/images/noimage.jpg' alt='noimage.jpg' width='95' height='105' border='0'/>";
				}else{
					$image ="<a href='$CFG->siteroot/file.php/achievements/$record->image' rel ='lightbox' title='$record->image' ><img src='$CFG->siteroot/file.php/achievements/f3_$record->image' alt='$record->image' width='95' height='105' /></a>";					
				}
				echo"
				<div style='text-align:right;widht:100%'>
				    <a href='javascript:void(0);' onclick='editAchievements($record->id);' ><img src='images/edit.gif' border='0' /></a>
				    <a href='javascript:void(0);' onclick='deleteAchievements($record->id);' ><img src='images/delete.png' border='0' /></a>
				</div>
				<div class='achievements_content' id='achievements_content_$record->id'>  					
					 $image
					<strong>".$record->title."</strong><br />
					<p>".$record->short_description."</p>
					<p>".$date."</p>
						<div class='clear'></div>
				</div>";
			}
		}
		echo"<div><a href='javascript:void(0);' onclick='addAchievements();' >ADD</a></div>";
	} 

	 if(isset($_POST['flag']) && $_POST['flag']=='editAchievements') {

		 $id = required_param('id',PARAM_INT);
		 $user_id = $_SESSION['s4c_user_id'];
		$studeny_info = get_records('achievement','user_id',$user_id);
		echo "<form action='upload_script.php' method='post' enctype='multipart/form-data' target='upload_target' id='achievement_form' name='achievement_form' onsubmit='startUpload();' >";
		if($studeny_info){
			
			foreach($studeny_info as $record){
				if(empty($record->image)){
					$image = "<img src='$CFG->siteroot/images/noimage.jpg' alt='noimage.jpg' width='95' height='105' border='0'/>";
				}else{
					$image ="<a href='$CFG->siteroot/file.php/achievements/$record->image' rel ='lightbox' title='$record->image' ><img src='$CFG->siteroot/file.php/achievements/f3_$record->image' alt='$record->image' width='95' height='105' /></a>";					
				}
				 if($id == $record->id){
					?>
					<div style='text-align:right;widht:100%'>
					<input type="image" name="submit" src="images/save_button.jpg" />
					<a href='javascript:void(0);' onclick='populateAchievements();' ><img src="images/cancel_bottom.jpg"></a></div>
					<div class='achievements_content' id='achievements_content_$record->id'>  
						<table>
						<tr><td rowspan='4'><?php echo $image; ?></td></tr>

						<tr>
							<td><strong>Title</strong></td>
							<td><input type='text' name='title' id='title' value='<?php echo $record->title; ?>' class="validate['required']" /></td>
						</tr>
						
						<tr>
							<td><strong>Year</strong></td>
							<td><!--<input readonly='true' name='year' id='year' type='text' class='input-text2  date  demo_vista1' style='width:150px;' value='<?php echo $record->year; ?>'>-->
                            <?php
							 	$val_date = date("n/d/Y",$record->year);
								$day_month_year = explode('/',$val_date);
					
								$day	= date("d",time());
								$month	= date("m",time());
								$year	= date("Y",time());
								//echo  " $day : $month : $year ";
								$num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
								echo "<select name='date_month' id='date_month' >";
								$month_array = array('Select Month','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
								foreach($month_array as $key=>$value){
									if($key != 0){
										if(isset($day_month_year[0]) && $key == $day_month_year[0]){
											echo "<option value='$key' selected>$value</option>";
										}else{
											echo "<option value='$key'>$value</option>";
										}
									}
								}
								echo "</select> ";


								echo "<select name='date_day' id='date_day' >";
									for($i = 1; $i<=$num; $i++){
										if(isset($day_month_year[1]) && $i == $day_month_year[1]){
											echo "<option value='$i' selected >$i</option>";
										}else{
											echo "<option value='$i'>$i</option>";
										}
									}
								echo"</select>";

								echo "<select name='date_year' id='date_year' >";
									$year_start = 1910;
									$year_end	= date('Y')+1;
									for($i = $year_start; $i<$year_end; $i++){
										if(isset($day_month_year[2]) && $day_month_year[2] == $i){
											echo "<option value='$i' selected >$i</option>";
										}else{
											echo "<option value='$i'>$i</option>";
										}
									}
								 echo "</select>";
							?>
                            </td>
						</tr>

						<tr>
							<td><strong>Short Description</strong></td>
							<td><textarea  name='short_description' id='short_description' class="" ><?php echo $record->short_description;?></textarea></td>
						</tr>

						<tr>
							<td colspan='3'>Change Image<br /><input name='myfile' type='file' size='2' class="validate['image']" /></td>
						</tr>

						</table>

						 <br />	
						
						<iframe id='upload_target' name='upload_target' src='#' style='width:0;height:0;border:0px solid #fff;'></iframe>
						<div class='clear'></div>

					</div>
					<?php
				}else{
					echo"
					<div style='text-align:right;widht:100%'><a href='javascript:void(0);' onclick='editAchievements($record->id);' ><img src='images/edit.gif' border='0' /></a></div>
					<div class='achievements_content' id='achievements_content_$record->id'>  					
						$image
						<strong>".$record->title."</strong><br />
						<p>".$record->short_description."</p>
							<div class='clear'></div>
					</div>";
				}
			}
			echo"<input type='hidden' name='update' id='update' value='update' />";
			echo"<input type='hidden' name='id' id='id' value='$id' />";
			
		}
		echo "</form>";		
	}
	 if(isset($_POST['flag']) && $_POST['flag']=='deleteAchievements') {

		 $id = required_param('id',PARAM_INT);
		 $user_id = $_SESSION['s4c_user_id'];

         $result = delete_records('achievement','id',$id);

         // Need to handle the error, unable to find the error display block in UI, for later
//         if(!$result){
//
//         }
	}

	if(isset($_POST['flag']) && $_POST['flag']=='addAchievements') {
		$user_id = $_SESSION['s4c_user_id'];
		$studeny_info = get_records('achievement','user_id',$user_id);
		echo "<form action='upload_script.php' method='post' enctype='multipart/form-data' target='upload_target' id='achievement_form' name='achievement_form' onsubmit='startUpload();' >";
		if($studeny_info){			
			foreach($studeny_info as $record){
				if(empty($record->image)){
					$image = "<img src='$CFG->siteroot/images/noimage.jpg' alt='noimage.jpg' width='95' height='105' border='0'/>";
				}else{
					$image ="<a href='$CFG->siteroot/file.php/achievements/$record->image' rel ='lightbox' title='$record->image' ><img src='$CFG->siteroot/file.php/achievements/f3_$record->image' alt='$record->image' width='95' height='105' /></a>";					
				}
				 
					echo"
					<div style='text-align:right;widht:100%'><a href='javascript:void(0);' onclick='editAchievements($record->id);' ><img src='images/edit.gif' border='0' /></a></div>
					<div class='achievements_content' id='achievements_content_$record->id'>  					
						 $image
						<strong>".$record->title."</strong><br />
						<p>".$record->short_description."</p>
							<div class='clear'></div>
					</div>";
				
			}
		}
		?>
					<div style='text-align:right;widht:100%'><input type='submit' name='submitBtn' class='sbtn' value='Add' /></div>
					<div class='achievements_content' id='achievements_content'>  
						<table border='2' align="center" class="achievements_awards_table">
						

						<tr>
							<td><strong>Title</strong></td>
							<td><input type='text' name='title' id='title' value='' size='40' class='validate["required"]'/></td>
						</tr>
						
						<tr>
							<td><strong>Year</strong></td>
							<td><!--<input readonly='true' name='year' id='year' type='text' class='input-text2  date  demo_vista1' style='width:150px;' value='' class='validate["required"]'/>-->
                            <?php
								$day	= date("d",time());
								$month	= date("m",time());
								$year	= date("Y",time());
								$num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
								echo "<select name='date_month' id='date_month' >";
								$month_array = array('Select Month','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
								foreach($month_array as $key=>$value){
									if($key != 0){
										echo "<option value='$key'>$value</option>";
										}
								}
								echo "</select> ";


								echo "<select name='date_day' id='date_day' >";
									for($i = 1; $i<=$num; $i++){
											echo "<option value='$i'>$i</option>";
									}
								echo"</select>";

								echo "<select name='date_year' id='date_year' >";
									$year_start = 1910;
									$year_end	= date('Y')+1;
									for($i = $year_start; $i<$year_end; $i++){
										echo "<option value='$i'>$i</option>";
									}
								 echo "</select>";
							?>
                            </td>
						</tr>

						<tr>
							<td valign='top'><strong>Short Description</strong></td>
							<td><textarea  name='short_description' id='short_description'  cols='30' rows='4'></textarea></td>
						</tr>

						<tr>
							<td><strong>Add Image</strong></td>
							<td><input name='myfile' type='file'   size='28' /></td>
						</tr>

						</table>
						
						<iframe id='upload_target' name='upload_target' src='' style='width:0;height:0;border:0px solid #fff;'>
                        </iframe>
						<div class='clear'></div>

					</div>
		
		</form>
		<?php 	
	}

	if(isset($_POST['flag']) && $_POST['flag']=='populateProfessional') {
		
		$user_id = $_SESSION['s4c_user_id'];
		$studeny_info = get_record('fe_users','id',$user_id);
		$show =0 ;
		if(!empty($studeny_info->organization)|| !empty($studeny_info->designation)|| !empty($studeny_info->work_from)||     !empty($studeny_info->work_till)|| !empty($studeny_info->work_nature)|| !empty($studeny_info->short_description)) {
			$show = 1;
		}

		if($studeny_info){
			if($show == 0){
				echo "  <div ><a href='javascript:void(0);' onclick='editProfessional();' >Add</a></div>";
			}else{
				echo "  <div class='in_Right_link'><a href='javascript:void(0);' onclick='editProfessional();' ><img src='images/edit.gif' border='0' /></a></div>";
					$work_from = !empty($studeny_info->work_from)?date('F d, Y',$studeny_info->work_from):'';
					$work_till = !empty($studeny_info->work_till)?date('F d, Y',$studeny_info->work_till):'';
					if(isset($studeny_info->till_now) && !empty($studeny_info->till_now))
					{
						$work_till = $studeny_info->till_now;
					}
					echo"
						<div class='pro_exp_box'>
							<strong>Company</strong>
							<p>".$studeny_info->organization."</p>					
							<strong>Title</strong>
							<p>".$studeny_info->designation."</p>
							<strong>From</strong>
							<p>".$work_from."</p>
							<strong>To</strong>
							<p>".$work_till."</p>
							<strong>Type of Work</strong>
							<p>".$studeny_info->work_nature."</p>
							<strong>Brief Description</strong>
							<p>".$studeny_info->short_description."</p>
						</div>
					";
			}
		}else{
			echo "No Records Found ";
		}		
	} 

	if(isset($_POST['flag']) && $_POST['flag']=='editProfessional') {
		
		$user_id = $_SESSION['s4c_user_id'];
		$studeny_info = get_records('fe_users','id',$user_id);
		echo '<form name="professional_form" id="professional_form" method="post" onsubmit="saveProfessional();return false;">';
		if($studeny_info){
			//echo "  <div class='in_Right_link'><a href='javascript:void(0);' onclick='saveProfessional();' >Save</a></div>";
			echo "<div class='in_Right_link'><input type='image' name='submit' src='images/save_button.jpg' /><a href='javascript:void(0);' onclick='populateProfessional();' ><img src='images/cancel_bottom.jpg'></a></div>";
			foreach($studeny_info as $record){
				/*
				echo"
					<strong>Organization</strong>
					<p><input name='organization' id='organization' value='".$record->organization."' /></p>					
					<strong>Designation</strong>
					<p><input name='designation' id='designation' value='".$record->designation."' /></p>
					<strong>Working From</strong>
					<p><input readonly='true' name='work_from' id='work_from' type='text' class='input-text2  date  demo_vista1' style='width:150px;' value='$record->work_from'></p>
					<strong>Working Till</strong>
					<p><input readonly='true' name='work_till' id='work_till' type='text' class='input-text2  date  demo_vista1' style='width:150px;' value='$record->work_till'></p>
					<strong>Nature Of Work</strong>
					<p><textarea name='work_nature' id='work_nature' >".$record->work_nature."</textarea></p>
					<strong>Short Description</strong>
					<p><textarea name='short_description' id='short_description' >".$record->short_description."</textarea></p>
				";*/
				?>
				 
				<div class="pro_exo_wrapper1">
                <div class="professional_experience_box_small">
                <strong>Company<span>*</span></strong>
				<p><input name='organization' id='organization' value='<?php echo $record->organization; ?>'  class="validate['required']"/></p>					
				</div>
                <div class="professional_experience_box_small_right">	
                <strong>Title</strong>
				<p><input name='designation' id='designation' value='<?php echo $record->designation; ?>' /></p>
				</div>
                </div>
                
                <div class="pro_exo_wrapper1">
                <div class="professional_experience_box_small">	
                <strong>From</strong>
				<p><!--<input readonly='true' name='work_from' id='work_from' type='text' class='input-text2  date  demo_vista1' style='width:150px;' value='<?php echo $record->work_from; ?>'>-->
                <?php
				 				 $val_date = date("n/d/Y",$record->work_from);
					             $day_month_year = explode('/',$val_date);
					
								$day	= date("d",time());
								$month	= date("m",time());
								$year	= date("Y",time());
								//echo  " $day : $month : $year ";
								$num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
								echo "<select name='date_month_f' id='date_month_f' >";
								$month_array = array('Select Month','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
								foreach($month_array as $key=>$value){
									if($key != 0){
										if(isset($day_month_year[0]) && $key == $day_month_year[0]){
											echo "<option value='$key' selected>$value</option>";
										}else{
											echo "<option value='$key'>$value</option>";
										}
									}
								}
								echo "</select> ";


								echo "<select name='date_day_f' id='date_day_f' >";
									for($i = 1; $i<=$num; $i++){
										if(isset($day_month_year[1]) && $i == $day_month_year[1]){
											echo "<option value='$i' selected >$i</option>";
										}else{
											echo "<option value='$i'>$i</option>";
										}
									}
								echo"</select>";

								echo "<select name='date_year_f' id='date_year_f' >";
									$year_start = 1910;
									$year_end	= date('Y')+1;
									for($i = $year_start; $i<$year_end; $i++){
										if(isset($day_month_year[2]) && $day_month_year[2] == $i){
											echo "<option value='$i' selected >$i</option>";
										}else{
											echo "<option value='$i'>$i</option>";
										}
									}
								 echo "</select>
								
								 </td>";
				?>
                </p>
				</div>
                <div class="professional_experience_box_small_right">
                 <strong id="to_id" <?php if(!empty($record->till_now)){ echo "style='display:none'"; }?>>To</strong>
                 <strong style="float:right">Presently Employed:</strong>	
				<p><!--<input readonly='true' name='work_till' id='work_till' type='text' class='input-text2  date  demo_vista1' style='width:150px;' value='<?php echo $record->work_till; ?>'>-->
                <?php
				 				$val_date = date("n/d/Y",$record->work_till);
					            $day_month_year = explode('/',$val_date);
					
								$day	= date("d",time());
								$month	= date("m",time());
								$year	= date("Y",time());
								//echo  " $day : $month : $year ";
								$num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
								echo "<select name='date_month_t' id='date_month_t' ";
								if(!empty($record->till_now))
								{
									 echo "style='display:none'";
								}
								echo " >";
								$month_array = array('Select Month','January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
								foreach($month_array as $key=>$value){
									if($key != 0){
										if(isset($day_month_year[0]) && $key == $day_month_year[0]){
											echo "<option value='$key' selected>$value</option>";
										}else{
											echo "<option value='$key'>$value</option>";
										}
									}
								}
								echo "</select> ";


								echo "<select name='date_day_t' id='date_day_t'";
								if(!empty($record->till_now))
								{
									 echo "style='display:none'";
								}
								echo " >";
									for($i = 1; $i<=$num; $i++){
										if(isset($day_month_year[1]) && $i == $day_month_year[1]){
											echo "<option value='$i' selected >$i</option>";
										}else{
											echo "<option value='$i'>$i</option>";
										}
									}
								echo"</select>";

								echo "<select name='date_year_t' id='date_year_t'"; if(!empty($record->till_now))
								{
									 echo "style='display:none'";
								}
								echo " >";
									$year_start = 1910;
									$year_end	= date('Y')+1;
									for($i = $year_start; $i<$year_end; $i++){
										if(isset($day_month_year[2]) && $day_month_year[2] == $i){
											echo "<option value='$i' selected >$i</option>";
										}else{
											echo "<option value='$i'>$i</option>";
										}
									}
								 echo "</select>
								 
								 ";
					echo '<span ';
					if(!empty($record->till_now))
					{
						echo ' style="float:right;width:100%;" ';
					}else{
						echo ' style="float:right;width:20%;" ';
					} 
					echo 'id="chk_span_id"><input onclick="checkChecked(this);" name="current_emp" id="current_emp" type="checkbox" style="float:right;width:50px !important;"';
					if(!empty($record->till_now))
					{
					     echo "checked";
					}
					' /></span>';
				?> 
                </p>
				</div>
                </div>
                
                
<!--                <div class="professional_experience_box">
                <strong>Type of Work</strong>
				<p><textarea name='work_nature' id='work_nature' ><?php echo $record->work_nature; ?></textarea></p>
		</div>-->
                <div class="professional_experience_box">	
                <strong>Brief Description</strong>
				<p><textarea name='short_description' id='short_description' ><?php echo $record->short_description; ?></textarea></p>
				</div>
				<?php
				
			}
		}else{
			echo "No Records Found ";
		}
		echo '</form>';
	} 
	if(isset($_POST['flag']) && $_POST['flag']=='saveProfessional') {
		
		$id = $_SESSION['s4c_user_id'];
		$organization = optional_param('organization','',PARAM_TEXT);
               $designation = optional_param('designation','',PARAM_TEXT);
		//$work_from = optional_param('work_from','',PARAM_TEXT);
		$date_month_f			= optional_param('date_month_f','0',PARAM_INT);
		$date_day_f			    = optional_param('date_day_f','0',PARAM_INT);
		$date_year_f			= optional_param('date_year_f','0',PARAM_INT);
		$work_from	    		= $date_month_f . '/' . $date_day_f . '/' . $date_year_f;
		$work_from        		= strtotime($work_from);
		$current_emp			= optional_param('current_emp','',PARAM_INT);
		
		//$work_till = optional_param('work_till','',PARAM_TEXT);
		
		$date_month_t			= optional_param('date_month_t','0',PARAM_INT);
		$date_day_t			    = optional_param('date_day_t','0',PARAM_INT);
		$date_year_t			= optional_param('date_year_t','0',PARAM_INT);
		$work_till	    		= $date_month_t . '/' . $date_day_t . '/' . $date_year_t;
		$work_till        		= strtotime($work_till);
		
		//$work_nature = optional_param('work_nature','',PARAM_TEXT);
		$short_description = optional_param('short_description','',PARAM_TEXT);
		$post_date = date("Y-m-d",$work_till);
		$update_record = new object();
		$update_record->id = $id;
		$update_record->organization = $organization;
		$update_record->designation = $designation;
		if(empty($work_from)){
			$work_from = time();
		}
		if($current_emp=="1"){
			$work_till = time();
		}
		$update_record->work_from = $work_from;
		if($current_emp=="1")
		{
			$update_record->work_till = $work_till;
			$update_record->till_now = 'Currently Employed';
		}else{
			$update_record->work_till = $work_till;
			$update_record->till_now = '';
		}
		$update_record->work_nature = $work_nature;
		$update_record->short_description = $short_description;
                update_record('fe_users',$update_record);
	} 

	

	if(isset($_POST['flag']) && $_POST['flag']=='search_friends') {
	   
		//$txtsearchtype		= optional_param('txtsearchtype','',PARAM_TEXT);
		$txtsearch				= optional_param('txtsearch','',PARAM_TEXT);
		//$txtsearch_city			= optional_param('txtsearch_city','',PARAM_TEXT); 
		$user_count 			= 0;
		$user_id				= $_SESSION['s4c_user_id'];
		$extraselect			= '';
		 
		/*if(!empty($txtsearch))	
			$extraselect = "AND student.first_name like '%$txtsearch%'" ; 
		
		if($txtsearchtype == 'city' &&  !empty($txtsearch_city)	){		 
			$extraselect = "AND student.city = " . $txtsearch_city ; 
		} */
		//$school_type = get_field('school_type','type','id',$type_id);
		// code for city
		$sql = "select id from {$CFG->prefix}city where name like '%$txtsearch%'";
		$city_id = get_records_sql($sql);
		$city_id_arr = array();
		foreach($city_id as $city_id_val)
		{
			$city_id_arr[] = $city_id_val->id;
		}
		//print_r($city_id_arr);
		$city_id = implode(",",$city_id_arr);
		if(!empty($city_id))
		{
			$city_id=" || student.city in($city_id)";
		}else{
		    $city_id='';
		}
		
		// code for state
		$sql = "select id from {$CFG->prefix}state where name like '%$txtsearch%'";
		$state_id = get_records_sql($sql);
		$state_id_arr = array();
		foreach($state_id as $state_id_val)
		{
			$state_id_arr[] = $state_id_val->id;
		}
		//print_r($city_id_arr);
		$state_id = implode(",",$state_id_arr);
		if(!empty($state_id))
		{
			$state_id=" || student.state in($state_id)";
		}else{
		    $state_id='';
		}
		
		if(!empty($txtsearch)){
		$txtsearch1 = preg_replace ("/\s+/", " ", trim($txtsearch));
		$txtsearch1 = explode(" ",$txtsearch1);
		//print_r($txtsearch);
		if(isset($txtsearch1[1]))
		{
		$extraselect = "AND (student.first_name like '%$txtsearch1[0]%' || student.last_name like '%$txtsearch1[1]%' || student.first_name like '%$txtsearch1[1]%' || student.last_name like '%$txtsearch1[0]%' $city_id $state_id)" ;
		}else{
		$extraselect = "AND (student.first_name like '%$txtsearch1[0]%' || student.last_name like '%$txtsearch1[0]%' || student.zip_code like '%$txtsearch1[0]%' $city_id $state_id)" ; 
		}
		}
		
		$search_student_sql  = "SELECT rand(),student.first_name,student.id, student.last_name, student.user_id,student.added_date,fe_users.user_type,fe_users.image FROM fe_users fe_users INNER JOIN student student ON (fe_users.id = student.user_id) WHERE (student.user_id NOT IN (SELECT friends.friend_id FROM friends friends WHERE friends.user_id=$user_id)) AND(student.user_id NOT IN (SELECT friends.user_id FROM friends friends WHERE friends.friend_id=$user_id)) AND(fe_users.isapproved = 1) AND(fe_users.user_type != 'staff')  AND(student.user_id <>$user_id) $extraselect  ORDER BY student.added_date DESC";
		
		$search_counselors_sql  = "SELECT rand(),student.first_name,student.id, student.last_name, student.user_id,student.added_date,fe_users.user_type,fe_users.image FROM fe_users fe_users INNER JOIN counselors student ON (fe_users.id = student.user_id)";
		
		$search_counselors_sql  .= " WHERE (student.user_id NOT IN (SELECT friends.friend_id FROM friends friends WHERE friends.user_id=$user_id)) AND(student.user_id NOT IN (SELECT friends.user_id FROM friends friends WHERE friends.friend_id=$user_id)) AND(fe_users.isapproved = 1) AND(fe_users.user_type != 'staff') AND(student.user_id <>$user_id) $extraselect ORDER BY student.added_date DESC";
         
		
		$search_parent_sql  = "SELECT rand(),student.first_name,student.id, student.last_name, student.user_id,student.added_date,fe_users.user_type,fe_users.image FROM fe_users fe_users INNER JOIN parent student ON (fe_users.id = student.user_id) WHERE (student.user_id NOT IN (SELECT friends.friend_id FROM friends friends WHERE friends.user_id=$user_id)) AND(student.user_id NOT IN (SELECT friends.user_id FROM friends friends WHERE friends.friend_id=$user_id)) AND(fe_users.isapproved = 1) AND(fe_users.user_type != 'staff') AND(student.user_id <>$user_id) $extraselect  ORDER BY student.added_date DESC";

		$search_teacher_sql  = "SELECT rand(),student.first_name,student.id, student.last_name, student.user_id,student.added_date,fe_users.user_type,fe_users.image FROM fe_users fe_users INNER JOIN teacher student ON (fe_users.id = student.user_id) WHERE (student.user_id NOT IN (SELECT friends.friend_id FROM friends friends WHERE friends.user_id=$user_id)) AND(student.user_id NOT IN (SELECT friends.user_id FROM friends friends WHERE friends.friend_id=$user_id)) AND(fe_users.isapproved = 1) AND(fe_users.user_type != 'staff') AND(student.user_id <>$user_id) $extraselect  ORDER BY student.added_date DESC";

		$search_student			= get_records_sql($search_student_sql);
		$search_counselors		= get_records_sql($search_counselors_sql);
		$search_parent			= get_records_sql($search_parent_sql); 
		$search_teacher			= get_records_sql($search_teacher_sql);
		
	   
		$search_list			= array();
		if($search_student)
			$search_list		= array_merge($search_list,$search_student);
		if($search_counselors)
			$search_list		= array_merge($search_list,$search_counselors);
		if($search_parent)
			$search_list		= array_merge($search_list,$search_parent);
		if($search_teacher)
			$search_list		= array_merge($search_list,$search_teacher);
		//print_r($search_list);
		
		
		$i = 1;
		if(!empty($search_list) && isset($search_list)){
			$user_count += count($search_list); 
		}
		
		if($user_count== 0)
		{
			if(empty($txtsearch))
				echo "<h5>No User Found for Search : ".get_field('city','name','status','active','id',$txtsearch_city) ."</h5>";
			else
				echo "<h4>No Result Found for Search : $txtsearch </h4>";
		}
		else
		{
			if(empty($txtsearch))
			{
				echo "<h5>Search Result for " . get_field('city','name','status','active','id',$txtsearch_city) ."&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp; <b>$user_count &nbsp;User Found </b></h5>";
			}
			else
			{
				echo "<h5>Search Result for " . $txtsearch ."&nbsp;&nbsp;&nbsp;:&nbsp;&nbsp;&nbsp; <b>$user_count &nbsp;User Found </b></h5>";
			}
		}
		if(!empty($search_list) && isset($search_list)){
			$link = '';
			$i=1;
			foreach($search_list as $record){ 
					if($record->user_type == 'teacher'){
						$link='teacher.html?tid='.$record->id;
					}
					if($record->user_type == 'student'){
						$link='student.html?sid='.$record->id;
					}
					if($record->user_type == 'counselor'){
					  $link='counselors.html?cid='.$record->id;
					}
					if($record->user_type == 'parent'){
					  $link='parent.html?pid='.$record->id;
					}
					
					if(isset($record->image) && !empty($record->image))
					{
						$image = "file.php/personal/f3_".$record->image;
						$imageb = "file.php/personal/".$record->image;
					}else{
						$image = "images/noimage.jpg";
						$imageb= "images/noimage.jpg";
					}
				$friend_cetogery = (($i%4) == 0)?'friend_cetogery':'friend_cetogery';
				echo "				 
					<div class='$friend_cetogery' style='float:left'>
					<a rel='lightbox[img]' href='".$CFG->siteroot."/".$imageb."' title='$record->first_name'><img src='".$CFG->siteroot."/".$image."' border='0' title='$record->first_name'/></a>
						<div class='friend_name'> <a href='".$CFG->siteroot."/".$link."'>".ucwords($record->first_name)." &nbsp; ".ucwords($record->last_name)." </a> </div>
						<div class='clear'>
							<div class='friend_delete'> <a href='javascript:void(0);' onclick='addFriend($record->user_id);' >Add  Friend</a>&nbsp;&nbsp;&nbsp;&nbsp; </div>
						</div>
					</div>
										
					 ";
					echo (($i%4) == 0)?'<div class="clear"></div>':'';
				$i=$i+1;
			}
		}
		unset($txtsearch);
		unset($txtsearch_city);
	} 

	if (isset($_POST['flag']) && $_POST['flag']=='addFriend'){
		global $CFG;

		$user_id		= $_SESSION['s4c_user_id'];
		$friend_id		= $_POST['id'];		
		$code			= generate_random(8);

		$user_data		= get_user_info($user_id);
		$friend_data	= get_user_info($friend_id);

		$sql = "select * from {$CFG->prefix}friends where user_id =".$user_id." and friend_id =".$friend_id;
		$check_friend = get_record_sql($sql);
		
		if(empty($check_friend)) {
			$to		= $friend_data->email;	
			$name	= ($user_data->user_type == 'school')?$user_data->school_name:"$user_data->first_name &nbsp; $user_data->last_name";
			$sub	= "Add Friend Request BY : $name On Search 4 Colleges ";
			$msg = "<br /><a href='".$CFG->siteroot."'><img src='".$CFG->siteroot."/cmiadmin/theme/cmi/images/cmi_logo.png' alt='Logo' border='0' /></a><br /><br />";
			$msg	= "<br />Please Click on below  Link to Accept  <br />"; 
			$msg	.= '<a href="'.$CFG->siteroot.'/verify.html?friend='.$code.'">Accept</a>';
			$msg .= <<<OP
				<div style="background-color:#FFFFFF;">
					  <br /><b>Thanks,</b><br />
						<b>S4c </b>
				</div>
OP;

			require_once("cmiadmin/lib/phpmailer/class.phpmailer.php");
			global $CFG; 
			$mail				= new PHPMailer();
			$mail->IsHTML(true);
			$mail->From			= $user_data->first_name;
			$mail->FromName		= $user_data->first_name."&nbsp;".$user_data->last_name;
			$mail->Subject		= $sub;
			$mail->Body			= $msg;
			$mail->AddAddress($to);
			//if($mail->Send())
			//{
				$insert_record				= new object();
				$insert_record->user_id		= $user_id;
				$insert_record->friend_id	= $friend_id;
				$insert_record->code		= $code;
				$insert_record->added_date	= time();
				if($new = insert_record('friends',$insert_record)){
					echo "yes";					
				}
			//}
		}else{
			echo "duplicate";
		}
	}

	if(isset($_POST['flag']) && $_POST['flag']=='delete_friends') {

		$id = optional_param('id','',PARAM_TEXT);
		if($id){
			if($new = delete_records('friends','id',$id)) {
				echo"deleted";
			}else{
				echo "Unable to delete";
			}
		}
	} 

	if(isset($_POST['flag']) && $_POST['flag']=='delete_school_sports') {

		$id = optional_param('id','',PARAM_TEXT);
		if($id){
			if($new = delete_records('school_culture_campus_life_sports','id',$id)) {
				echo"deleted";
			}else{
				echo "Unable to delete";
			}
		}
	}
	
	if(isset($_POST['flag']) && $_POST['flag']=='add_school_sports') {

		$id 			= optional_param('id','',PARAM_TEXT);
		$description 	= optional_param('description','',PARAM_TEXT);
		$teamname 		= optional_param('teamname','',PARAM_TEXT);
		$team_type 		= optional_param('team_type','',PARAM_TEXT);
		
		$object					= new object();
		$object->team_name 		= $teamname;
		$object->school_id 		= $id;
		$object->team_description = $description;
		$object->team_type 		= $team_type;
		$object->added_date 	= time();
		
		if($id){
			if($new = insert_record('school_culture_campus_life_sports',$object)) {
				echo"Added";
			}else{
				echo "Unable to Added";
			}
		}
	}
	
	/* Membership */ 
	if(isset($_POST['flag']) && $_POST['flag']=='membership') {

		if(isset($_SESSION['s4c_user_id'])){
			$school_id = $_SESSION['s4c_user_id'];
		}

		$schoolsql = "select * from {$CFG->prefix}school_membership sm INNER JOIN {$CFG->prefix}school_member_ship_types mt on(sm.school_memberShip_typeid = mt.id) where sm.school_id  = $school_id AND sm.status = 'active' ORDER BY sm.added_date DESC";
		$school_data = get_record_sql($schoolsql);

		

		if(isset($school_data->school_memberShip_typeid)){
			$school_member_id = $school_data->school_memberShip_typeid;
			$registeredon = $school_data->registeredon;
			$expiryon = $school_data->expiryon;
		}
		else{
			$school_member_id  = 0;
		}

			
		$sql = "select * from {$CFG->prefix}school_member_ship_types where status  = 'active'";
		$membership_data = get_records_sql($sql);

		/*100 for a month
		subscribed for 2 years ie.. 2*12 = 24 months

		total  fee =  24 * 100 = 2400

		subscription date =  11 - 10 - 2010

		current date	  =  03 - 11 - 2011

		expired date      =  11 - 10 - 2012

		remaining days to expire = */
		
		$text = '';
		
		$existing_membership_total_amount = '';
		if(!empty($membership_data)){
			$discount  = '0';
			
			if(!empty($school_data)){

				$registeredon = $school_data->registeredon;
				$expiryon = $school_data->expiryon;

				$existing_membership_total_amount = $school_data->amount * $school_data->validity;

				$date1 = date('Y-m-d',$registeredon);
				$date2 = date('Y-m-d',$expiryon);

				$now = strtotime($date2); // or your date as well
				$your_date = strtotime($date1);
				$datediff = $now - $your_date;
				$total_days = round($datediff/86400);
				
				$now1 = time(); // or your date as well
				$your_date1 = strtotime($date1);
				$datediff1 = $now1 - $your_date1;

				$current_days_passed = round($datediff1/86400);

				$total_days_remaining = $total_days - $current_days_passed;

				$total_amount = $school_data->amount * round($total_days/30)/$total_days;

				$discount = round($total_amount * $total_days_remaining);
			}
			$count = 1;
			foreach($membership_data as $data){
				

				if(isset($school_member_id) && $school_member_id == $data->id){
										
				}
				
				if($school_member_id != $data->id){
					
					$expiry_date = mktime(0, 0, 0, date("m")+$data->validity, date("d"), date("y"));

					$date_expirt = date('Y-m-d',$expiry_date);
					
					$now2 = strtotime($date_expirt); // or your date as well
					$your_date2 = time();
					$datediff2 = $now2 - $your_date2;
					$total_days2 = round($datediff2/86400);

					$total_amount = $data->amount * $data->validity;

					if($total_amount > $existing_membership_total_amount){

						if($count == "1")
						{							
							$required = 'checked';							
						}
						else
						{
							$required = '';
						}
						
						$text .= '<div class="membership_type"><input '.$required.' id="'.$data->id.'" type="radio" name="membership_radio" value="'.$data->title.'" onclick="javascript:insert_value(this.value,'.$total_amount.','.$expiry_date.',this.id,'.$discount.')"><span>'.$data->title.'</span>									
						<span id="info_'.$data->id.'"></span>
						</div>';
						$submit_button = '<br /><div><input type="submit" name="submit" value="Pay Now"> </div>						
						';
						if($count == "1")
						{
						$text.= '<script>';
						$text.= 'insert_value("'.$data->title.'",'.$total_amount.','.$expiry_date.','.$data->id.','.$discount.')';
						$text.= '</script>';
						}
						
					}
					else{
						$submit_button  = '<div class="membership_type">No Higher Membership found than the current.</div>';
					}
				}

				$count++;
			}
			$text .= $submit_button;
		}
		else{
			$text .= '<div class="membership_type">No Membership found</div>';
		}
		echo $text;
	}
	/* Membership */ 
	
	if(isset($_POST['flag']) && $_POST['flag']=='populate_all_friends') {
		global $CFG;
		$user_id = $_SESSION['s4c_user_id'];
		$sql = "select friend_id as friend_id,id from {$CFG->prefix}friends where user_id = $user_id and invitaion_status='approved' order by added_date ";
		$user_friend_list = get_records_sql($sql);
		$sql = "select user_id as friend_id,id from {$CFG->prefix}friends where friend_id = $user_id and invitaion_status='approved'  order by added_date";
		$friend_user_list = get_records_sql($sql);
		if(!empty($user_friend_list) && !empty($friend_user_list)){
			$friend_list = (object) array_merge((array)$user_friend_list, (array)$friend_user_list);
		}elseif(!empty($user_friend_list)){
			$friend_list = $user_friend_list;
		}elseif(!empty($friend_user_list)){
			$friend_list = $friend_user_list;
		}

		if(!empty($friend_list)){
			$i = 1;
			if($friend_list){
				foreach($friend_list as $record){	
				    
					$friend_info = get_user_info($record->friend_id);
					//$friend_cetogery = (($i%5) == 0)?'friend_cetogery_tight':'friend_cetogery';
					if($friend_info->user_type == 'school'){
						$name = $friend_info->school_name;
						if(isset($friend_info->logo) && !empty($friend_info->logo))
						{
							$image = "file.php/schools/".$friend_info->logo;
						    $imageb = "file.php/schools/".$friend_info->logo;
						}else{
							$image = "images/noimage.jpg";
							$imageb= "images/noimage.jpg";
						}
						
						$link='colleges_profile.html?view='.$friend_info->id;
					}else{
						$name = $friend_info->first_name."&nbsp;".$friend_info->last_name;
					}
					if($friend_info->user_type == 'teacher'){
						$link='teacher.html?tid='.$friend_info->pid;
					}
					if($friend_info->user_type == 'student'){
						$link='student.html?sid='.$friend_info->pid;
					}
					if($friend_info->user_type == 'counselor'){
					  $link='counselors.html?cid='.$friend_info->pid;
					}
					if($friend_info->user_type == 'parent'){
					  $link='parent.html?pid='.$friend_info->pid;
					}
					if($friend_info->user_type != 'school')
					{
						if(isset($friend_info->image))
						{
							$image = "file.php/personal/f1_".$friend_info->image;
							$imageb = "file.php/personal/".$friend_info->image;
						}else{
							$image = "images/noimage.jpg";
							$imageb= "images/noimage.jpg";
						}
					}

                    /*
                     <div class='friend_cetogery_line'>
						    <a rel='lightbox[img]' href='".$imageb."' title='".$name."'><img src='".$image."' /></a>
							<a href='".$CFG->siteroot."/".$link."'>$name</a>
							<a href='javascript:void(0);' onclick='delete_friends($record->id);'>
							    <img src='images/delete_icon.png' style='width:16px; height:16px; margin: 4px 4px 0 20px; float: left; ' />
							</a>&nbsp;&nbsp;
							<a href='new_messages.html?id=$record->id&friend_id=$record->friend_id' rel =\"lightbox[msg_$record->id 550 300]\" >Send Message</a>
						  </div>
                     */
					echo"
						 <div class='friend_cetogery_line'><div style='text-align: center;'> <a rel='lightbox[img]' href='".$imageb."' title='".$name."'><img src='".$image."' /></div></a>
							<div class='friend_name'> <a href='".$CFG->siteroot."/".$link."'>$name</a> </div>
							<div class='clear'>
							  <div class='friend_delete'>
							  <a href='javascript:void(0);' onclick='delete_friends($record->id);'><img src='images/delete_icon.png' style='width:16px; height:16px; margin: 4px 4px 0 20px; float: left; ' /></a>&nbsp;&nbsp;
							  <a href='new_messages.html?id=$record->id&friend_id=$record->friend_id' rel =\"lightbox[msg_$record->id 550 300]\" >Send Message</a>
							  </div>
							</div>
						  </div>
						  ";
					$i++;
				}
			}
		}
	} 


	if(isset($_POST['flag']) && $_POST['flag']=='populate_all_friends_list') {
		global $CFG;
		$user_id = $_SESSION['s4c_user_id'];
		$sql = "select friend_id as friend_id,id from {$CFG->prefix}friends where user_id = $user_id and invitaion_status='approved' order by added_date ";
		$user_friend_list = get_records_sql($sql);
		$sql = "select user_id as friend_id,id from {$CFG->prefix}friends where friend_id = $user_id and invitaion_status='approved'  order by added_date";
		$friend_user_list = get_records_sql($sql);
		if(!empty($user_friend_list) && !empty($friend_user_list)){
			$friend_list = (object) array_merge((array)$user_friend_list, (array)$friend_user_list);
		}elseif(!empty($user_friend_list)){
			$friend_list = $user_friend_list;
		}elseif(!empty($friend_user_list)){
			$friend_list = $friend_user_list;
		}

		if(!empty($friend_list)){
			if($friend_list){
				
					echo "<center><select name='friend[]' id='friend' multiple='multiple' style='width:250px;' class='validate['required']'>";
					
					foreach($friend_list as $record){	
					$friend_info = get_user_info($record->friend_id);
					if($friend_info->user_type == 'school'){
						$name = $friend_info->school_name;
					}else{
						$name = $friend_info->first_name."&nbsp;".$friend_info->last_name;
					}

					echo "<option value=".$friend_info->id.">".$name."</option>";

				}
				echo "</select></center>";
			}
		}
	} 

	if(isset($_POST['flag']) && $_POST['flag']=='show_day') {

		$month	= required_param('month',PARAM_INT);
		$year	= required_param('year',PARAM_INT);
		$num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		echo "<option>Select Day</option>";
		for($i=1;$i<=$num;$i++){
			echo "<option value='$i' >$i</option>";
		}
	} 
	 
	if(isset($_POST['flag']) && $_POST['flag']=='show_persional_dob') {

		$month	= required_param('month',PARAM_INT);
		$year	= required_param('year',PARAM_INT);
		$day	= required_param('day',PARAM_INT);
		$num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		for($i=1;$i<=$num;$i++){
			if($i == $day){
				echo "<option value='$i' selected>$i</option>";
			}else{
				echo "<option value='$i' >$i</option>";
			}
		}
	} 

	if(isset($_GET['flag']) && $_GET['flag']=='search_mail_friend') {	
	$input = strtolower( $_GET['to'] );
	$len = strlen($input);
	$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 0;	
	
	$aResults = array();
	$count = 0;
	
	if ($len)
	{	
		$user_id = $_SESSION['s4c_user_id'];
		$search_student_sql		= "select * from {$CFG->prefix}student where first_name LIKE '%$input%' and user_id IN (SELECT friend_id from {$CFG->prefix}friends where user_id=$user_id) and user_id IN (SELECT id from {$CFG->prefix}fe_users where isapproved=1) and user_id != $user_id";
		$search_counselors_sql	= "select * from {$CFG->prefix}counselors where first_name LIKE '%$input%' and user_id IN (SELECT friend_id from {$CFG->prefix}friends where user_id=$user_id) and user_id IN (SELECT id from {$CFG->prefix}fe_users where isapproved=1)";
		$search_parent_sql		= "select * from {$CFG->prefix}parent where first_name LIKE '%$input%' and user_id IN (SELECT friend_id from {$CFG->prefix}friends where user_id=$user_id) and user_id IN (SELECT id from {$CFG->prefix}fe_users where isapproved=1)";
		$search_schools_sql		= "select * from {$CFG->prefix}schools where school_name LIKE '%$input%' and user_id IN (SELECT friend_id from {$CFG->prefix}friends where user_id=$user_id) and user_id IN (SELECT id from {$CFG->prefix}fe_users where isapproved=1)";
		$search_teacher_sql		= "select * from {$CFG->prefix}teacher where first_name LIKE '%$input%' and user_id IN (SELECT friend_id from {$CFG->prefix}friends where user_id=$user_id) and user_id IN (SELECT id from {$CFG->prefix}fe_users where isapproved=1)";


		$search_student			= get_records_sql($search_student_sql);
		$search_counselors		= get_records_sql($search_counselors_sql);
		$search_parent			= get_records_sql($search_parent_sql);
		$search_schools			= get_records_sql($search_schools_sql);
		$search_teacher			= get_records_sql($search_teacher_sql);

		$search_list			= array();
		if($search_student)
			$search_list		= array_merge($search_list,$search_student);
		if($search_counselors)
			$search_list		= array_merge($search_list,$search_counselors);
		if($search_parent)
			$search_list		= array_merge($search_list,$search_parent);
		//if($search_schools)
			//$search_list		= array_merge($search_list,$search_schools);
		if($search_teacher)
			$search_list		= array_merge($search_list,$search_teacher);
		$i = 1;
		$user_count = 0;
		if(!empty($search_list)){
			$user_count += count($search_list);
		}elseif(!empty($search_schools)){
			$user_count += count($search_schools);
		}


		if($search_list){
			foreach($search_list as $record){
				$aResults[] = array( "id"=>($record->user_id) ,"value"=>htmlspecialchars($record->first_name) );
			}
		}
		if($search_schools){
			foreach($search_schools as $record){
				$aResults[] = array( "id"=>($record->user_id) ,"value"=>htmlspecialchars($record->school_name) );
			}
		}
	}
	
	
	if (isset($_REQUEST['json']))
	{
		header("Content-Type: application/json");
	
		echo "{\"results\": [";
		$arr = array();
		for ($i=0;$i<count($aResults);$i++)
		{
			$arr[] = "{\"id\": \"".$aResults[$i]['id']."\", \"value\": \"".$aResults[$i]['value']."\"}";
		}
		echo implode(", ", $arr);
		echo "]}";
	}
	else
	{
		header("Content-Type: text/xml");

		echo "<?xml version=\"1.0\" encoding=\"utf-8\" ?><results>";
		for ($i=0;$i<count($aResults);$i++)
		{
			echo "<rs id=\"".$aResults[$i]['id']."\">".$aResults[$i]['value']."</rs>";
		}
		echo "</results>";
	}
}

	if(isset($_POST['flag']) && $_POST['flag']=='populate_friend_req') {	

		$sql ="select * from {$CFG->prefix}friends where friend_id= ".$_SESSION['s4c_user_id']." and invitaion_status='inprocess' ";
		$frnd_req_pending = get_records_sql($sql);
		//print_object($frnd_req_pending);
		if($frnd_req_pending){
			$i = 1;
			$count_record = count($frnd_req_pending);
			foreach($frnd_req_pending as $record){

				$user_info		= get_user_info($record->friend_id);
				$user_name		= ($user_info->user_type == 'school')?$user_info->school_name:$user_info->first_name;
				
				$friend_info	= get_user_info($record->user_id);
				$get_city = get_field('city','name','id',$friend_info->city,'status','active');
				$friend_name	= ($friend_info->user_type == 'school')?$friend_info->school_name:$friend_info->first_name." ".$friend_info->last_name;
                if(!empty($friend_info->image))
				{
				  $img="<img src='file.php/personal/f3_$friend_info->image'>";
				}else{
				 $img="<img src='$CFG->siteroot/images/noimage.jpg' alt='noimage.jpg' width='95' height='105' border='0'/>";
				}
				echo "<h3>$i.<br /> $img<br />Name : $friend_name <br /> city : $get_city</h3> ";
				
				//echo "<image src='' onclick=\"verify_friend_req($record->id,1)\" style='cursor:pointer;'/>";
				echo '<center><a onclick="verify_friend_req('.$record->id.',1)" style="cursor:pointer;"><b> <img src="images/accept.jpeg" /> </b></a>&nbsp;&nbsp;&nbsp;';
				echo '<a onclick="verify_friend_req('.$record->id.',0)" style="cursor:pointer;"><b> <img src="images/reject.jpeg" /></b></a></center><br/>';
				
				$i++;
				if($i > $count_record ){
					break;
				}
			}
		}else{
			echo "<br/><h4> No Request Found</h4><br/>";
		}
	}

	if(isset($_POST['flag']) && $_POST['flag']=='update_school_sports') {

		$id		= required_param('id',PARAM_INT);
		$team	= required_param('teamname',PARAM_RAW);
		$description	= required_param('description',PARAM_RAW);
	
		if($id != ''){
			$update_friend					 = new object();
			$update_friend->id				 = $id;
			$update_friend->team_name  = $team;
			$update_friend->team_description  = $description;
			
			if(update_record('school_culture_campus_life_sports',$update_friend))
				echo " Added to Friend List <br />";
			else
				echo "Unable to add <br/>";
		}
	}
	 
	if(isset($_POST['flag']) && $_POST['flag']=='verify_friend_req') {

		$id		= required_param('id',PARAM_INT);
		$op		= required_param('op',PARAM_INT);
		if($op == 1){
			$update_friend					 = new object();
			$update_friend->id				 = $id;
			$update_friend->invitaion_status = 'approved';
			if(update_record('friends',$update_friend))
				echo " Added to Friend List <br />";
			else
				echo "Unable to add <br/>";
		}
		else{
			$update_friend					 = new object();
			$update_friend->id				 = $id;
			$update_friend->invitaion_status = 'declined';
			if(update_record('friends',$update_friend))
				echo " Request Rejected. <br />";
		}
	}
	if(isset($_POST['flag']) and $_POST['flag'] == "check_delete_msg")
	{
		$id = $_POST['id'];	
		$msg = $_POST['msg'];
		$msg_tag = ($msg == 'inbox')?'isdelete_receiver':'isdelete_sender';
		$messages = get_record('messages','id',$id);
		
		if($messages->isdelete_receiver =='no' && $messages->isdelete_sender =='no'){
			//update
			$message_obj = new object();
			$message_obj->id = $id;
			$message_obj->$msg_tag = 'yes';
			update_record('messages',$message_obj);
			echo"updated";
		}elseif($messages->isdelete_receiver =='no' && $messages->isdelete_sender =='yes'){
			delete_records('messages','id',$id);
			echo"deleted";
		}elseif($messages->isdelete_receiver =='yes' && $messages->isdelete_sender =='no'){
			delete_records('messages','id',$id);
			echo"deleted";
		}
	}

	if (isset($_POST['flag']) && $_POST['flag']=='add_comment'){
		$txtcomment = optional_param('txtcomment','',PARAM_RAW);
		$id = optional_param('id','',PARAM_INT);
		if($txtcomment != '' && $id != ''){
            $user_info = get_user_info($_SESSION['s4c_user_id']);
			$add_mode = new object();
			$add_mode->comment = $txtcomment;
			$add_mode->added_date = time();
			$add_mode->blog_id = $id;
			$add_mode->user_id = $_SESSION['s4c_user_id'];
			$add_mode->user_name = $user_info->name;
			insert_record('blog_comment', $add_mode);

			echo "<strong>Thank you for your comments.<br />Your comments will be moderated by Search For Colleges and published soon.</strong>";

		}else{
			echo"Some technical problem please try again";
		}
	}	

	if (isset($_POST['flag']) && $_POST['flag']=='add_post'){
		$sql = "select * from {$CFG->prefix}blog_category where status='active' order by sort_order";
		$blog_category_info = get_records_sql($sql);
		?>
		<form name="add_post_form" id="add_post_form" method="post" onsubmit="save_post();return false;">
		<table width='100%' border='0' class="table_font3">
			<tr>
				<td align="left" valign="middle">Blog Category</td>
				<td align="left" valign="middle">
					<select name='category_id' id='category_id'>
						<?php
						if($blog_category_info){
							foreach($blog_category_info as $record){
								echo "<option value='$record->id'>$record->title</option>";
							}
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td align="left" valign="middle">Blog Title</td>
				<td align="left" valign="middle"><textarea name='title' id='title' class="validate['required']" cols="50" rows="5" maxlength="100"></textarea>(limit 100 words)</td>
			</tr>
			<tr>
				<td align="left" valign="middle">BLOG Description</td>
				<td align="left" valign="middle"><textarea name='description' id='description' class="validate['required']" cols="50" rows="5" maxlength="100"></textarea>(100 words)</td>
			</tr>
			<tr>
				<td align="left" valign="middle">BLOG story</td>
				<td align="left" valign="middle"><textarea name='story' id='story' class="validate['required']" cols="50" rows="5"></textarea></td>				
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="submit" name="submit" id="submit" value="save" /></td>				
			</tr>
		</table>
		</form>
		<?php
	}

	if (isset($_POST['flag']) && $_POST['flag']=='save_post'){

		$category_id	= optional_param('category_id','',PARAM_INT);
		$title			= optional_param('title','',PARAM_RAW);
		$description	= optional_param('description','',PARAM_RAW);
		$story			= optional_param('story','',PARAM_RAW);

		if($category_id != '' && $title != '' && $description != '' && $story != ''){
			$add_mode = new object();
			$add_mode->category_id = $category_id;
			$add_mode->title = $title;
			$add_mode->description = $description;			
			$add_mode->story = $story;
			$add_mode->added_date = time();
			$add_mode->user_id = $_SESSION['s4c_user_id'];
			insert_record('blog', $add_mode);
			echo "<strong>Thank you for your comments.<br />Your comments will be moderated by Search For Colleges and published soon.</strong>";

		}else{
			echo"Some technical problem please try again";
		}
	}


		

	if (isset($_POST['flag']) && $_POST['flag']=='add_article'){
		$sql = "select * from {$CFG->prefix}article_category where status='active' order by sort_order";
		$blog_category_info = get_records_sql($sql);
		?>
		<form name="add_article_form" id="add_article_form" method="post" onsubmit="save_article();return false;">
		<table width='100%' border='1'>
			<tr>
				<td>Category</td>
				<td >
					<select name='category_id' id='category_id' class="validate['required']">
						<option value="0">Select Category</option>
						<?php
						if($blog_category_info){
							foreach($blog_category_info as $record){
								echo "<option value='$record->id'>$record->name</option>";
							}
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Title</td>
				<td><textarea name='title' id='title' class="validate['required']" cols="50" rows="5" maxlength="100"></textarea>(limit 100 words)</td>
			</tr>
			<tr>
				<td>Description</td>
				<td><textarea name='description' id='description' class="validate['required']" cols="50" rows="5"></textarea>(100 words)</td>
			</tr>
			<tr>
				<td colspan="2" align="center"><input type="submit" name="submit" id="submit" value="save" /></td>				
			</tr>
		</table>
		</form>
		<?php
	}

	if (isset($_POST['flag']) && $_POST['flag']=='save_article'){

		$category_id	= optional_param('category_id','',PARAM_INT);
		$title			= optional_param('title','',PARAM_RAW);
		$description	= optional_param('description','',PARAM_RAW);

		if($category_id != '' && $title != '' && $description != ''){
			$add_mode = new object();
			$add_mode->category_id = $category_id;
			$add_mode->name = $title;
			$add_mode->long_description = $description;	
			$add_mode->added_date = time();
			$add_mode->user_id = $_SESSION['s4c_user_id'];
			insert_record('articles', $add_mode);
			echo "<strong>Thank you for your Article.<br />Your Article will be moderated by Search For Colleges and published soon.</strong>";

		}else{
			echo"Some technical problem please try again";
		}
	}
	if (isset($_POST['flag']) && $_POST['flag']=='edit_blog'){
		global $CFG;

		$id					= optional_param('id','',PARAM_INT);
		$comment_info_count	= optional_param('count','',PARAM_INT);		
		$name				= optional_param('name','',PARAM_TEXT);

		$sql = "SELECT rand(),blog.added_date, blog.description, blog.title, blog.category_id, blog.id, blog.user_id, blog.story, blog_category.title AS category_name FROM {$CFG->prefix}blog_category blog_category INNER JOIN {$CFG->prefix}blog blog ON (blog_category.id = blog.category_id) WHERE (blog.id = $id) ORDER BY blog_category.sort_order ASC";	
		$blog_info = get_record_sql($sql);
		?>

		<tr>
			<td>
			<form name="edit_blog__form" id="edit_blog__form" method="post" onsubmit="save_blog(<?php echo "'$comment_info_count','$name'"; ?>);return false;">
			<table width='100%' border='1'>
				<tr>
					<td class='td_color'>
						<div style='float:left;'><b>Title : <br/></b>
							<span class='blog_detail_head'>
								<textarea name='title' id='title' class="validate['required']" cols="50" rows="5" maxlength="100"><?php echo ucfirst($blog_info->title); ?></textarea>
							</span>
							<?php echo "<br />$name <br />".date('M Y',$blog_info->added_date); ?>
						</div>
						<div style='float:right;'><b>Comments : <?php echo $comment_info_count; ?></b></div>
					</td>
				</tr>
				
				<tr>
					<td><b>Decription : </b>
						<p><textarea name='description' id='description' class="validate['required']" cols="50" rows="5" maxlength="100"><?php echo ucfirst($blog_info->description); ?></textarea>
						</p>
					</td>
				</tr>
				<tr>
					<td><b>Story :</b>
						<p><textarea name='story' id='story' class="validate['required']" cols="50" rows="5" maxlength="100"><?php echo ucfirst($blog_info->story); ?></textarea></p>
					</td>
				</tr>
				<tr>
					<td align='center'>
						<input type="image" src='images/save.jpg'/>	&nbsp;&nbsp;
					<!--	<img src='images/cancel_bottom.jpg'  onclick='window.location.reload();' style='cursor:pointer;'/>-->
						<img src='images/cancel_bottom.jpg'  onclick='window.location.reload();' style='cursor:pointer;'/>
						<input type="hidden" name="id" id="id" value="<?php echo $id; ?>">
					</td>
				</tr>
			</table>
			</form>
			</td>
		</tr>

		<?php
	}
	if (isset($_POST['flag']) && $_POST['flag']=='save_blog'){
		global $CFG;
		//print_object($_POST);

		$id					= optional_param('id','',PARAM_INT);
		$name				= optional_param('name','',PARAM_TEXT);
		$comment_info_count	= optional_param('count','',PARAM_INT);
		$title				= optional_param('title','',PARAM_TEXT);
		$description		= optional_param('description','',PARAM_TEXT);
		$story				= optional_param('story','',PARAM_TEXT);

		$update				= new object();
		$update->id				= $id;
		$update->title			= $title;
		$update->description	= $description;
		$update->story			= $story;
		update_record('blog',$update);
/*
		$sql = "SELECT rand(),blog.added_date, blog.description, blog.title, blog.category_id, blog.id, blog.user_id, blog.story, blog_category.title AS category_name FROM {$CFG->prefix}blog_category blog_category INNER JOIN {$CFG->prefix}blog blog ON (blog_category.id = blog.category_id) WHERE (blog.id = $id) ORDER BY blog_category.sort_order ASC";	
		$blog_info = get_record_sql($sql);	

		echo "<tr>";
			echo "<td class='td_color'>";
				echo "<div style='float:left;'><span class='blog_detail_head'>".ucfirst($blog_info->title)."</span>";
				echo "<br />".$name;
				echo "<br />".date('M Y',$blog_info->added_date)."</div>"."<div style='float:right;'><b>Comments : $comment_info_count</b></div>";
			echo "</td>";
		echo "</tr>";	

		echo "<tr><td align='right'>";
			echo"<img src='images/edit.gif' alt='edit' onclick='edit_blog($blog_info->id,$comment_info_count,\"$name\")' style='cursor:pointer;'/>";
		echo"</td></tr>";

		echo "<tr><td><b>Decription : </b><p>".ucfirst($blog_info->description)."</p></td></tr>";
		echo "<tr><td><b>Story :</b><p>".ucfirst($blog_info->story)."</p></td></tr>";

*/
	}

	if (isset($_POST['flag']) && $_POST['flag']=='majordegree'){
		global $CFG;
		//print_object($_POST);

		$degree				= optional_param('degree','',PARAM_INT);
		$school				= optional_param('school','',PARAM_INT);		

		$result = get_record_sql('SELECT id FROM school_admission_details WHERE degree_id='.$degree.' AND school_id='.$school);
		if(isset($result->id)){
			delete_records('school_admission_details','degree_id',$degree, 'school_id', $school);
			echo 'deleted';
		}
		else{
			echo 'new';
		}
	}
    if(isset($_GET['id']) && $_GET['id'] != '0' && $_GET['flag'] == 'city_from')
	{
		$sql = "SELECT * FROM ".$CFG->prefix."city WHERE status = 'active' AND state_id = ".$_GET['id']." ORDER BY name ASC";
		
		$user_data = get_records_sql($sql);
		
		echo '<option value="0">Select city</option>';
		
		if(!empty($user_data))
		{
			foreach($user_data as $data)
				{
					echo '<option value="'.$data->id.'">'.$data->name.'</option>';
				}
		}
		
	}
	
	if(isset($_POST['id']) && $_POST['id'] != '0' && $_POST['flag'] == 'view_count')
	{
			$add = new object();
			if($_SESSION['s4c_user_id'])
			{
			$add->user_id = $_SESSION['s4c_user_id'];
			}else{
			$add->user_id = '0';
			}
			$add->school_id = $_POST['id'];
			$add->ip = $_SERVER['REMOTE_ADDR'];
			$add->count = '1';
			$add->added_date = time();

			if(!empty($add)){
				$insert = insert_record('schools_view_count',$add);
			}
		
	}
	
	

?>
