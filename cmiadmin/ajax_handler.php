<?php
	require_once('config.php');

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
			$fancy->added_date = time();;

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
	if(isset($_GET['flag']) and $_GET['flag'] == "city_list")
	{
		$country_id = $_GET['id'] ;
        $cities = get_records_sql('SELECT * FROM '.$CFG->prefix.'city WHERE status="active" and country_id="'.$country_id.'"');
        echo $cities;
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

	if(isset($_POST['flag']) and $_POST['flag'] == "isapproved")
	{
		$chk = $_POST['chk'];
		$id = $_POST['id'];	
		
		$blog = new object();
		$blog->id = $id;
		$blog->isapproved = $chk;
		update_record('blog',$blog);
		echo "<b>Succesfully Updated</b>";
	}
    if(isset($_POST['flag']) and $_POST['flag'] == "isapproved_articles")
	{
		$chk = $_POST['chk'];
		$id = $_POST['id'];	
		
		$articles = new object();
		$articles->id = $id;
		$articles->approved = $chk;
		update_record('articles',$articles);
		echo "<b>Record Succesfully Updated</b>";
	}
	if(isset($_POST['flag']) and $_POST['flag'] == "isapproved_comment")
	{
		
		$chk = $_POST['chk'];
		$id = $_POST['id'];	
		
		$blog = new object();
		$blog->id = $id;
		$blog->isapproved = $chk;
		update_record('blog_comment',$blog);
		echo "<b>Succesfully Updated</b>";
	}

	if(isset($_POST['flag']) and $_POST['flag'] == "isapproved_article")
	{
		
		$chk = $_POST['chk'];
		$id = $_POST['id'];	
		
		$blog = new object();
		$blog->id = $id;
		$blog->isapproved = $chk;
		update_record('articles',$blog);
		echo "<b>Succesfully Updated</b>";
	}
	
	if(isset($_GET['id']) && $_GET['id'] != '0' && $_GET['flag'] == 'country')
	{
				
		$sql = "SELECT * FROM ".$CFG->prefix."state WHERE status = 'active' AND country_id = ".$_GET['id']." ORDER BY name ASC";
		
		$user_data = get_records_sql($sql);
		
		echo '<option value="0">Select state</option>';
		
		if(!empty($user_data))
		{
			foreach($user_data as $data)
				{
					echo '<option value="'.$data->id.'">'.$data->name.'</option>';
				}
		}
		
	}
	
	if(isset($_GET['flag']) and $_GET['flag'] == "allow_section")
	{
		    
			if($_GET['edit'])
			{
			$sql = "SELECT allowed_sections FROM ".$CFG->prefix."users WHERE status = 'active' AND id='".$_GET['edit']."'";
  		    $user_section = get_record_sql($sql);
			$allow_arr=explode(",",$user_section->allowed_sections);
			$sql = "SELECT * FROM ".$CFG->prefix."section WHERE status = 'active' AND parent_id=0 AND section_name!='Logout' ORDER BY section_name ASC";
  		    $section_data = get_records_sql($sql);
			echo '<table>';
			echo '<tr><td colspan="3"><strong><font color="#F6C739">Only checked section will be shown by Mormal user</br>Note:First checked main menu and then sub menu of that section.</font></strong></td></tr>';
			echo '<tr>';
			$i=1;
				 foreach($section_data as $section)
				 {
				    $html='<td><input name="section[]" type="checkbox" value="'.$section->id.'"';
					if(in_array($section->id,$allow_arr)){ $html.='checked="checked"';}
					echo $html.='/><strong>'.$section->section_name.'</strong></td>';
					$sql = "SELECT * FROM ".$CFG->prefix."section WHERE status = 'active' AND parent_id=$section->id AND section_name!='Logout' ORDER BY section_name ASC";
					$child_data = get_records_sql($sql);
					foreach($child_data as $cdata)
					{
					if($i%3=='0'){ echo '</tr><tr>';	}
					$i=$i+1;
					$html='<td><input name="section[]" type="checkbox" value="'.$cdata->id.'" ';
					if(in_array($cdata->id,$allow_arr)){ $html.='checked="checked"';}
					echo $html.='/>'.$cdata->section_name.'</td>';
					}
					echo '</tr><tr><td colspan="3">&nbsp;</td></tr><tr>';
					$i=1;
				 }
			echo '</tr>';
			echo '</table>';
			}
			else{
			$sql = "SELECT * FROM ".$CFG->prefix."section WHERE status = 'active' AND parent_id=0 AND section_name!='Logout' ORDER BY section_name ASC";
  		    $section_data = get_records_sql($sql);
			echo '<table>';
			echo '<tr><td colspan="3"><strong><font color="#F6C739">Only checked section will be shown by Mormal user</br>Note:First checked main menu and then sub menu of that section.</font></strong></td></tr>';
			echo '<tr>';
			$i=1;
				 foreach($section_data as $section)
				 {
				    echo '<td><input name="section[]" type="checkbox" value="'.$section->id.'" /><strong>'.$section->section_name.'</strong></td>';
					$sql = "SELECT * FROM ".$CFG->prefix."section WHERE status = 'active' AND parent_id=$section->id AND section_name!='Logout' ORDER BY section_name ASC";
					$child_data = get_records_sql($sql);
					foreach($child_data as $cdata)
					{
					if($i%3=='0'){ echo '</tr><tr>';	}
					$i=$i+1;
					echo '<td><input name="section[]" type="checkbox" value="'.$cdata->id.'" />'.$cdata->section_name.'</td>';
					}
					echo '</tr><tr><td colspan="3">&nbsp;</td></tr><tr>';
					$i=1;
				 }
			echo '</tr>';
			echo '</table>';
			}
			
	}
	
         if (isset($_POST['flag']) && $_POST['flag']=='toogleStatistics'){

		$statistics_status = get_record_sql("SELECT *  FROM {$CFG->prefix}config c WHERE c.name='school_admin_statistics_view'");
                if(!empty($statistics_status)){
                    $config = new object();
				$config->id = $statistics_status->id;
				$config->value= ($statistics_status->value)? 0 : 1;
                                if(update_record('config',$config)){
                                   $text=($config->value)?'Click to disable statistics':'Click to enable statistics'; 
                                   $reponse_array=array('update_error_code'=>0,'status'=>$config->value,'text'=>$text);
                                  echo json_encode($reponse_array); 
                                   
                                }
                                else{
                                   $reponse_array=array('update_error_code'=>1);
                                    echo json_encode($reponse_array);
                                }
                }else{
                    $reponse_array=array('update_error_code'=>-1);
                     echo json_encode($reponse_array);
                }
		
	}
?>
