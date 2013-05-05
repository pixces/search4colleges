<?php
	
	require_once('cmiadmin/config.php');
	$result = 0;

	$target_path = $CFG->dataroot."/achievements/";	

	if(!file_exists($target_path)){
		mkdir($CFG->dataroot."/achievements/",777);
	}

	$user_id = $_SESSION['s4c_user_id'];
	$added_date = time();
	$title = optional_param('title','',PARAM_TEXT);
	//$year = optional_param('year','',PARAM_INT);
		$date_month			= optional_param('date_month','0',PARAM_INT);
		$date_day			= optional_param('date_day','0',PARAM_INT);
		$date_year			= optional_param('date_year','0',PARAM_INT);
		$year	= $date_month . '/' . $date_day . '/' . $date_year;
		$year    = strtotime($year);			
	$short_description = optional_param('short_description','',PARAM_TEXT);

	if(empty($title) && empty($year) && empty($short_description) && !isset($_FILES["myfile"]['name'])) {
		$result = 2;
	}elseif(empty($title) && empty($year) && empty($short_description) ) {
		$result = 2;
	}elseif(isset($_FILES["myfile"]['name']) && !empty($_FILES["myfile"]['name'])){
		$add_mode_image = upload_image("myfile",$target_path);			
		if($add_mode_image){
			$photo = $add_mode_image;			
			}
		else{
			$photo = '';
		}
		$add_mode = new object();
		$add_mode->image = $photo;

		

		if($_POST['update']){
			$id = optional_param('id','',PARAM_TEXT);

			$add_mode->id = $id;
			$add_mode->title = $title;
			$add_mode->year = $year;
			$add_mode->short_description = $short_description;
			$add_mode->added_date = $added_date;

			if($new =update_record('achievement', $add_mode)){
				$result = 1;
			}
		}else{			
			$add_mode->title = $title;
			$add_mode->short_description = $short_description;
			$add_mode->year = $year;
			$add_mode->added_date = $added_date;
			$add_mode->user_id = $user_id;				
			if($new =insert_record('achievement', $add_mode)){
				$result = 1;
			}
		}
	}else{
			$add_mode = new object();
			if($_POST['update']){
				$id = optional_param('id','',PARAM_TEXT);
				$add_mode->id = $id;
				$add_mode->title = $title;
				$add_mode->year = $year;
				$add_mode->short_description = $short_description;
				$add_mode->added_date = $added_date;

				if($new =update_record('achievement', $add_mode)){
					$result = 1;
				}
			}else{			
				$add_mode->title = $title;
				$add_mode->year = $year;
				$add_mode->short_description = $short_description;			
				$add_mode->user_id = $user_id;
				$add_mode->added_date = $added_date;

				if($new =insert_record('achievement', $add_mode)){
					$result = 1;
				}
			}
		}
 
?>

 <script language="javascript" type="text/javascript">window.top.window.stopUpload(<?php echo $result; ?>);</script> 
