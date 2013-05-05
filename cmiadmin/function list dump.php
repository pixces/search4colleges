

	function get_all_products()
	{
		global $CFG;
		$sql = "SELECT * from ".$CFG->prefix."product where status='active'";
		$data = get_records_sql($sql);

		if(!empty($data)){
			echo "<option value=''>Select Product Name</option";	
			foreach($data as $value){
				
				echo "<option value='".$value->srno."'>".$value->srno."</option";	
			}
		}
		else
		{
		echo "<option value=''>No Products Added</option";
		}
	}

	function get_category_option_product($parent_id)
	{
		global $CFG;

		$parentsql = "SELECT * FROM ".$CFG->prefix."category WHERE status != 'delete' AND parent_id = 0 AND status = 'active'";
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
					$subsql = "SELECT * FROM ".$CFG->prefix."category WHERE status != 'delete' AND parent_id = $data->id AND status = 'active'";
					$sub_categories = get_records_sql($subsql);
					if(!empty($sub_categories)){
						foreach($sub_categories as $subdata){
							if($parent_id  == $subdata->id){
								$selected = 'selected=selected';
							}
							else
								$selected ='';
							$html .= "<OPTGROUP style='padding-left:15px;' LABEL='".$subdata->name."'>";
							$subsub_sql = "SELECT * FROM ".$CFG->prefix."category WHERE status != 'delete' AND parent_id = $subdata->id AND status = 'active'";
							$subsub_categories = get_records_sql($subsub_sql);
							if($subsub_categories){
								foreach($subsub_categories as $subsubdata){
									if($parent_id  == $subsubdata->id){
										$selected = 'selected=selected';
									}
									else
										$selected ='';
									
									$html .= "<option value=".$subsubdata->id ." $selected>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$subsubdata->name."</option>";
								}
							$html .= "</OPTGROUP>";
							}

						}
					}
					$html .= "</OPTGROUP>";
				}
			}
		}
		echo $html;
	}
	function get_category_option($parent_id)
	{
		global $CFG;

		$parentsql = "SELECT * FROM ".$CFG->prefix."category WHERE status != 'delete' AND parent_id = 0 AND status = 'active'";
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
					$html .= "<option value=".$data->id ." $selected>".$data->name."/</option>";
					$subsql = "SELECT * FROM ".$CFG->prefix."category WHERE status != 'delete' AND parent_id = $data->id AND status = 'active'";
					$sub_categories = get_records_sql($subsql);
					if(!empty($sub_categories)){
						foreach($sub_categories as $subdata){
							if($parent_id  == $subdata->id){
								$selected = 'selected=selected';
							}
							else
								$selected ='';
							$html .= "<option value=".$subdata->id ." $selected>".$data->name."/".$subdata->name."</option>";
							$subsub_sql = "SELECT * FROM ".$CFG->prefix."category WHERE status != 'delete' AND parent_id = $subdata->id AND status = 'active'";
							$subsub_categories = get_records_sql($subsub_sql);
							if($subsub_categories){
								foreach($subsub_categories as $subsubdata){
									if($parent_id  == $subsubdata->id){
										$selected = 'selected=selected';
									}
									else
										$selected ='';
									
									$html .= "<option value=".$subsubdata->id ." $selected>$data->name/$subdata->name/".$subsubdata->name."</option>";
								}
							}
						}
					}
				}
			}
		}
		echo $html;
	}

	function get_category_articles_option($parent_id)
	{
		global $CFG;
		$parentsql = "SELECT * FROM ".$CFG->prefix."category_articles WHERE status != 'delete' AND parent_id = 0 AND status = 'active'";
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
					$html .= "<option value=".$data->id ." $selected>".$data->name."/</option>";
					$subsql = "SELECT * FROM ".$CFG->prefix."category_articles WHERE status != 'delete' AND parent_id = $data->id AND status = 'active'";
					$sub_categories = get_records_sql($subsql);
					if(!empty($sub_categories)){
						foreach($sub_categories as $subdata){
							if($parent_id  == $subdata->id){
								$selected = 'selected=selected';
							}
							else
								$selected ='';
							$html .= "<option value=".$subdata->id ." $selected>".$data->name."/".$subdata->name."</option>";
							$subsub_sql = "SELECT * FROM ".$CFG->prefix."category_articles WHERE status != 'delete' AND parent_id = $subdata->id AND status = 'active'";
							$subsub_categories = get_records_sql($subsub_sql);
							if($subsub_categories){
								foreach($subsub_categories as $subsubdata){
									if($parent_id  == $subsubdata->id){
										$selected = 'selected=selected';
									}
									else
										$selected ='';
									
									$html .= "<option value=".$subsubdata->id ." $selected>$data->name/$subdata->name/".$subsubdata->name."</option>";
								}
							}
						}
					}
				}
			}
		}
		echo $html;
	}