<?php
	//project specific funtions would come 

	function get_category($category_id,$table){
		global $CFG;
		$sql = "SELECT * from ".$CFG->prefix."$table where status='active'";
		$data = get_records_sql($sql);

		if(!empty($data)){
			echo "<option value='0'>Select Category </option>";
			foreach($data as $record){	
				if($category_id == $record->id ){
					$selected = "selected='selected'";
				}else{
					$selected = "";
				}
				echo "<option value='".$record->id."' $selected >".$record->name."</option>";	
			}
		}
		else
		{
		echo "<option value=''>No Category Added</option>";
		}
	}

	function get_category_option_article($parent_id)
	{
		global $CFG;

		$parentsql = "SELECT * FROM ".$CFG->prefix."article_category WHERE status != 'delete' AND parent_id = 0 AND status = 'active'";
		$parent_categories = get_records_sql($parentsql);
		$selected = '';
		$html = '';
		$html .= "<option value='0'>Root</option>";
		if(!empty($parent_categories)){
			foreach($parent_categories as $data){
				{
					if($parent_id  == $data->id){
						$selected = 'selected=selected';
					}
					else
						$selected ='';
					$html .= "<option value=".$data->id ." $selected>".$data->name."</option>";
					
					}
				}
			}
		
		echo $html;
	}
	function get_category_option_blog($parent_id)
	{
		global $CFG;

		$parentsql = "SELECT * FROM ".$CFG->prefix."blog WHERE status != 'delete' AND parent_id = 0 AND status = 'active'";
		$parent_categories = get_records_sql($parentsql);
		$selected = '';
		$html = '';
		$html .= "<option value='0'>Root</option>";
		if(!empty($parent_categories)){
			foreach($parent_categories as $data){
				{
					if($parent_id  == $data->id){
						$selected = 'selected=selected';
					}
					else
						$selected ='';
					$html .= "<option value=".$data->id ." $selected>".$data->topic."</option>";
					
					}
				}
			}
		
		echo $html;
	}
	function cmi_get_us_states(){
		global $CFG;
		$country_id = 223 ;
		$selected = '';
		$html = '';
		$html .= "<option value='0'>Select State</option>";
		$states_data = get_records_sql("select id,name from {$CFG->prefix}state where status = 'active' and country_id = $country_id");
		if($states_data)
		{
			foreach($states_data  as $states)
			{
				if($country_id  == $states->id){
					$selected = 'selected=selected';
				}
				else
					$selected ='';
				$html .= "<option value=".$states->id ." $selected>".$states->name."</option>";
			}
		}
		echo $html;
	}	
?>