<?php
require_once("lib.php");

	function cmi_include_head($dir)
	{
		global $CFG;

		$path = $dir;
		if($dir == 'css')
		{
			$theme = current_theme();
			$path = 'theme/'.$theme.'/'.$dir;
		}
		
		if(is_dir($path))
		{	
		
			$dh = opendir($path);
		
			while (($file = readdir($dh)) !== false)
			{   
				$files[] = $file;
			}

			sort($files);
			foreach($files as $key=>$value)
			{
				
				if($dir =='css')
				{ 
					
					if(($value =='.' )|| ($value =='..') || substr($value,-4) !='.css')
					{
					}
					else
					{
							echo "<link href=\"$CFG->wwwroot/theme/$theme/css/$value\" type=\"text/css\" rel=\"stylesheet\" />\n";
					}
				}
				else if($dir =='js')
				{
					if(($value =='.' )|| ($value =='..') || substr($value,-3) !='.js')
					{
					}
					else
					{ 
							echo "<script src=\"js/$value\" type=\"text/javascript\"></script>\n";
					}
				}
			}
		}
	}


	function generate_random($length)
	{
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz12345`7890';

		// Length of character list
		$chars_length = (strlen($chars) - 1);

		// Start our string
		$string = $chars{rand(0, $chars_length)};
	   
		// Generate random string
		for ($i = 1; $i < $length; $i = strlen($string))
		{
			// Grab a random character from our list
			$r = $chars{rand(0, $chars_length)};
		   
			// Make sure the same two characters don't appear next to each other
			if ($r != $string{$i - 1}) $string .=  $r;
		}
	   
		// Return the string
		return $string;
	}


	function cmi_include_editor()
	{
		global $CFG;
		echo '<link rel="stylesheet" href="lib/editor/ckeditor/samples/sample.css" type="text/css">
				<script type="text/javascript" src="lib/editor/ckeditor/ckeditor.js"></script>
				<script type="text/javascript" src="lib/editor/ckeditor/samples/sample.js"></script>
				<script type="text/javascript" src="lib/editor/ckeditor/ckfinderinit.js"></script>';
	}

	function show_admin_menu($user_id)
	{
		global $CFG;

		if(($user_id == 1) || (($user_id != 1) && ($_SESSION['allowed_sections'] != ""))){

			$sql = "SELECT * FROM ".$CFG->prefix."section WHERE status = 'active' AND parent_id = 0";
			$userdata = get_record('users','id',$user_id);
			if($user_id != 1 && $_SESSION['allowed_sections'] != "" && $userdata->role_name == 'admin'){
				$sql = "SELECT * FROM ".$CFG->prefix."section WHERE status = 'active' AND parent_id = 0 AND id IN(".$_SESSION['allowed_sections'].")";
			}
			$sql .= " ORDER BY sort_order";
			
			$menu_data		= get_records_sql($sql);
				
			$allowed = '11';
			if($_SESSION['allowed_sections'] != '')
				$allowed = "$_SESSION[allowed_sections]";
			$i = 1;

			if(!empty($menu_data))
			{
				foreach($menu_data as $main_menu)
				{	
					
					if($main_menu->section_name != 'Logout')
					{
						if($user_id != 1 && $userdata->role_name != 'admin')
							$sub_sql = "SELECT * FROM ".$CFG->prefix."section WHERE status = 'active' AND (parent_id = $main_menu->id AND id IN ($allowed))";
						else
							$sub_sql = "SELECT * FROM ".$CFG->prefix."section WHERE status = 'active' AND parent_id = $main_menu->id";
						
						$sub_menu_data	= get_records_sql($sub_sql);
						
						if(!empty($sub_menu_data))
						{
							echo "<li class='cmi_link' id='".str_replace(' ','_',$main_menu->section_name)."'><a href='#'>".$main_menu->section_name."</a>";
							echo "<ul class='submenu' id='sub_".str_replace(' ','_',$main_menu->section_name)."'>";
							foreach($sub_menu_data as $sub_menu)
							{
								echo "<li><a href=\"".$CFG->wwwroot.'/'.$sub_menu->link_url."\">".$sub_menu->section_name."</a></li>\n";	
							}
							$i++;
							echo "</ul>";
							echo "<span>|</span></li>\n";
						}
						if($main_menu->section_name == "SEO")
						{
								echo "<li class='cmi_link' id='".str_replace(' ','_',$main_menu->section_name)."'><a href='#'>".$main_menu->section_name."</a>";
								echo "<ul class='submenu' id='sub_".str_replace(' ','_',$main_menu->section_name)."'>";					
								$sql = "SELECT * FROM {$CFG->prefix}seo_setting WHERE status = 'active'";
								$seo_data = get_records_sql($sql);
								if(!empty($seo_data)){
									foreach($seo_data as $seo)
									{
										echo "<li><a href=\"".$CFG->wwwroot.'/seo_form.php?edit='.$seo->id."\">".ucfirst(strtolower($seo->page_name))."</a></li>\n";
									}
								}
								else
								{
									echo '<li><a>Use $seo in settings.php</a></li>';
								}
								$i++;
								echo "</ul>";
								echo "<span>|</span></li>\n";
						}
						if($main_menu->section_name == "Page Content")
						{
								echo "<li class='cmi_link' id='".str_replace(' ','_',$main_menu->section_name)."'><a href='#'>".$main_menu->section_name."</a>";
								echo "<ul class='submenu' id='sub_".str_replace(' ','_',$main_menu->section_name)."'>";					
								$sql = "SELECT * FROM {$CFG->prefix}page_contents WHERE status = 'active'";
								$content_data = get_records_sql($sql);
								if($content_data){
									foreach($content_data as $page)
									{
										echo "<li><a href=\"".$CFG->wwwroot.'/page_content_form.php?edit='.$page->id."\">".ucfirst(strtolower($page->page_name))."</a></li>\n";
									}
								}
								else
								{
									echo '<li><a>Use $manage_page_content in settings.php</a></li>';
								}
								$i++;
								echo "</ul>";
								echo "<span>|</span></li>\n";							
						}
					}
				}			
			}		
		}
		if($_SESSION['login'] == "yes")
		{
			echo "<li class='cmi_link'><a onclick=\"window.location='logout.php'\" href=\"$CFG->wwwroot/logout.php\">Logout</a></li>";					
		}
	}

	function cmi_add_to_log($section='',$action='',$info='')
	{
		global $CFG;
		$activities = new object();
		if(isset($_SESSION['user_id']) && $_SESSION['user_id'] !='')
		{
			$activities->userid    =	$_SESSION['user_id'];
		}
		$activities->ip        =	getremoteaddr();
		$activities->time      =	time();     
		$activities->url       =	str_replace($_SERVER['PHP_SELF'],substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], "/") + 1) ,me()); 
		$activities->section   = $section;
		$activities->action    = $action;
		$activities->info      = $info;
		//insert_record('activities',$activities);
	}
	
	function get_buttons($text,$link,$imgsrc,$light='',$jsevent=''){

		if($light =='')
		{
			$var = " class='tipz'";
			return "<a  title=' ::$text' href='$link' $var >
					<img alt=\"Edit\" height=\"16\" width=\"16\" border=\"0\" class=\"Bg8\" src='$imgsrc' $jsevent />
				 </a>";
		}else
		{
			$var = $light." class='tipz'";
			return "<a  title=' ::$text' href='$link' $var >
					<img alt=\"Edit\" height=\"16\" width=\"16\" border=\"0\" class=\"Bg8\" src='$imgsrc' $jsevent />
				 </a>";
		}
		
	}

	function islogin()
	{
		if(!isset($_SESSION['login']))
		{
			redirect("login.php");
		}
		elseif(isset($_SESSION['login']) && !($_SESSION['login'] == "yes")){
			redirect("login.php");
		}
	}

	/* Function to load menu */
	function load_menu()
	{
		$theme = current_theme();
		echo <<<OP
		<ul id="nav" class="clearfix">
OP;
			show_admin_menu($_SESSION['user_id']);
		echo <<<OP
		</ul>
OP;
	}

	function isallow()
	{
		global $CFG;
		if($_SESSION['user_id'] != "1")
		{
			$sql  = "select * from ".$CFG->prefix."section where link_url ='".str_replace($CFG->dirroot.'/','',($_SERVER['SCRIPT_FILENAME']))."' AND status='active'";
			$data = get_record_sql($sql);
			$allow = explode(',', $_SESSION['allowed_sections']);
			if(in_array($data->id, $allow))
			{
			}
			else
			{
				redirect("index.php","Access Denied !!!");
			}
		}
	}

	/*
		 * Returns a subset of records
		 *
		 * @uses $CFG
		 * @param string $table The database table to be checked against.
		 * @param string $field The field to search
		 * @param bool $get If false then only a count of the records is returned
		 * @param string $fields A comma separated list of fields to be returned from the chosen table.
		 * @param string $searchterm A simple string to search for
		 * @param string $searchfields A comma separated list of fields to be searched.
		 * @param string $sort A SQL snippet for the sorting criteria to use
		 * @param string $page ?
		 * @param string $recordsperpage.
		 * @param string  $extraselect.
		 * @return object|false|int records unless get is false in which case the integer count of the records found is returned. False is returned if an error is encountered.
	*/
		
	function get_cmi_records($table, $get=true, $fields='*',  $search='', $searchfields='', $extraselect='', $sort='',$page='', $recordsperpage='') 
	{

		global $CFG;

		$LIKE      = sql_ilike();
		$select    = "";
		$start	   = ($page * $recordsperpage); 

		if (!empty($search) && !empty($searchfields)){
			
			$search_str = "";

			foreach($searchfields as $search_field){
				$search_field = trim($search_field);
				$search_str .= " $search_field $LIKE '%$search%' OR";
			}
			$search_str = substr($search_str,0,strlen($search_str)-3);
			$select = " (".$search_str.") AND ";
		}
		if ($extraselect) {
			$select .= $extraselect;
		}
		if ($get) {
			return get_records_select($table, $select, $sort, $fields, $start, $recordsperpage);
		}
		else {
			return count_records_select($table, $select);
		}
	}

	function get_all_users($field)
	{
		global $CFG;
		$sql = "SELECT distinct ".$CFG->prefix."users.first_name, 
			   ".$CFG->prefix."activities.id,
			   ".$CFG->prefix."activities.userid as reportid,
			   ".$CFG->prefix."activities.ip,
			   ".$CFG->prefix."activities.section,
			   ".$CFG->prefix."activities.action,
			   ".$CFG->prefix."activities.url,
			   ".$CFG->prefix."activities.time,
			   ".$CFG->prefix."activities.info
				FROM    ".$CFG->prefix."users ".$CFG->prefix."users
			   INNER JOIN
				  ".$CFG->prefix."activities ".$CFG->prefix."activities
			   ON (".$CFG->prefix."users.id = ".$CFG->prefix."activities.userid)";
		$data = get_records_sql($sql);
		if(!empty($data)){
			echo "<option value=''>Select Username</option";	
			foreach($data as $value){
				
				echo "<option value='".$value->reportid."'>".$value->first_name."</option";	
			}
		}
	}

	function wrap_string($source_str, $width=200)
	{
		$str = substr($source_str, 0, $width);
		$str .= "&#8230;";
		return $str;
	}

	function get_sort_order($table_name)
	{
		global $CFG;
		$sort_order = 1;
		$sql = "SELECT max(sort_order) AS max_sort_order FROM ".$CFG->prefix."".$table_name;
		if($rs = get_record_sql($sql)){
			$sort_order = $rs->max_sort_order+1;
		}
		return($sort_order);
	}

	function get_all_info($field)
	{
		global $CFG;
		$sql = "select distinct  $field from ".$CFG->prefix."activities where id != ''";
		$data = get_records_sql($sql);
		if(!empty($data)){
			if($field == 'time'){
				$fieldname = 'Date';
			}
			else if($field == 'ip'){
				$fieldname = 'Ip';
			}
			else
			{
				$fieldname = $field;
			}

			echo "<option value=''>Select $fieldname</option";	
			foreach($data as $value){
				if($fieldname == 'Date'){
					echo "<option value='".$value->$field."'>".date('d-m-y-h-m-s',$value->$field)."</option";
				}
				else{
					echo "<option value='".$value->$field."'>".$value->$field."</option";
				}
					
			}
		}	
	}

	function insert_seo()
	{
		global $CFG;

		include('../settings.php');
		if(!empty($seo)){
			foreach($seo as $value){
				$sql = "SELECT * FROM ".$CFG->prefix."seo_setting WHERE page_name = '$value'";
				$data = get_record_sql($sql);
				if(empty($data)){
					$insert = new object();
					$insert->page_name = $value;
					$insert->meta_title = ucwords(str_replace('_',' ',$value));
					$insert->meta_description = ucwords(str_replace('_',' ',$value));
					$insert->meta_keywords = ucwords(str_replace('_',' ',$value));
					$insert->status = 'active';
					$insert->added_date = time();
					$ins = insert_record('seo_setting',$insert);
					if($ins){
						cmi_add_to_log('Seo','Add',"New Seo page $value added.");
					}
					else{
						cmi_add_to_log('Seo','Add',"Fail to add Seo page $value");
					}
				}
			}
		}
	}

	function upload_image($image_field_name, $dir_path)
	{

			$resized_path = $dir_path;
			if (!file_exists($dir_path)) 
			{
				mkdir($dir_path);
				chmod($dir_path, 0755);
			}

			if (!file_exists($resized_path)) 
			{
				mkdir($resized_path);
				chmod($resized_path, 0755);
			}
			if(!empty($_FILES[$image_field_name]['tmp_name']))
			{
				$info = getimagesize($_FILES[$image_field_name]['tmp_name']);		
				$originalfilename = basename($_FILES[$image_field_name]['name']);
				$imagetarget = resolve_filename_collisions($dir_path, array(basename($_FILES[$image_field_name]['name'])), $format='%s_%d.%s');
				$originalfile = $dir_path.$imagetarget[0];

				/*$ext = preg_replace('/^.*\.([^.]+)$/D', '$1', $originalfile);			
				$destinationfile =  $resized_path.$imagetarget[0];
				$destinationfile1 =  str_replace($ext,'_f1.'.$ext,$destinationfile);
				$destinationfile2 =  str_replace($ext,'_f2.'.$ext,$destinationfile);*/

				$destinationfile1 =  $resized_path.'f1_'.$imagetarget[0];
				$destinationfile2 =  $resized_path.'f2_'.$imagetarget[0];
				$destinationfile3 =  $resized_path.'f3_'.$imagetarget[0];				
				$destinationfile4 =  $resized_path.'f4_'.$imagetarget[0];
				$destinationfile5 =  $resized_path.'f5_'.$imagetarget[0];
				$destinationfile6 =  $resized_path.'f6_'.$imagetarget[0];
													
				if(move_uploaded_file($_FILES[$image_field_name]['tmp_name'],$originalfile))
				{

					wp_resize_logo($originalfile,$destinationfile1,$originalfilename,'61','61');
					wp_resize_logo($originalfile,$destinationfile2,$originalfilename,'150','117');
					wp_resize_logo($originalfile,$destinationfile3,$originalfilename,'124','91');
					wp_resize_logo($originalfile,$destinationfile4,$originalfilename,'82','91');
					wp_resize_logo($originalfile,$destinationfile5,$originalfilename,'183','104');
					wp_resize_logo($originalfile,$destinationfile6,$originalfilename,'183','104');

					return $imagetarget[0];
				}else{
					return 0;
				}
			}
			else
			{
				return 0;
			}

	}

	function wp_resize_logo($originalfile, $destination,$originalfilename,$maxw=100,$maxh=100) 
	{		
		global $CFG;
		
		$imageinfo = GetImageSize($originalfile);		

		if (empty($imageinfo)) {
			if (file_exists($originalfile)) {
				unlink($originalfile);
			}
			return false;
		}

		$image->width  = $imageinfo[0];
		$image->height = $imageinfo[1];
		$image->type   = $imageinfo[2];

		switch ($image->type) {
			case IMAGETYPE_GIF:
				if (function_exists('ImageCreateFromGIF')) {
					$im = ImageCreateFromGIF($originalfile);
				} else {
					notice('GIF not supported on this server');
					unlink($originalfile);
					return false;
				}
				break;
			case IMAGETYPE_JPEG:
				if (function_exists('ImageCreateFromJPEG')) {
					$im = ImageCreateFromJPEG($originalfile);
				} else {
					notice('JPEG not supported on this server');
					unlink($originalfile);
					return false;
				}
				break;
			case IMAGETYPE_PNG:
				if (function_exists('ImageCreateFromPNG')) {
					$im = ImageCreateFromPNG($originalfile);
				} else {
					notice('PNG not supported on this server');
					unlink($originalfile);
					return false;
				}
				break;
			default:
				unlink($originalfile);
				return false;
		}

		resizeImage($im,$image,$destination,$originalfilename,$maxw,$maxh);
		return $destination;
	}

	function resizeImage($im,$image,$path,$new_filename,$new_width,$new_height)
	{
		global $CFG;
		set_time_limit(60);
		$newsize = getNewImageSizes($image->width,$image->height,$new_width,$new_height);
		$new_width = $newsize['width'];
		$new_height = $newsize['height'];
		if (function_exists('ImageCreateTrueColor'))
		{
		   $im2 = ImageCreateTrueColor($new_width,$new_height);
		}
		else
		{
		   $im2 = ImageCreate($new_width,$new_height);
		}
		ImageCopyBicubic($im2, $im, 0, 0, 0, 0, $new_width, $new_height, $image->width,$image->height);
		@touch($path);
		if (imagejpeg($im2,$path, 90))
		{
			@chmod($path, 0666);
			return true;
		}
	}

	/**
	 * getNewImageSizes() function is used to get the new size
	 * of a Image
	 *
	 * @uses $origWidth,$origHeigth,$maxWidth,$maxHeigth
	 *
	 * @param string $origWidth Original Width of a Image
	 * @param string $origHeigth Original Heigth of a Image
	 * @param string $maxWidth Maximum Width of a Image
	 * @param string $maxHeigth Maximum Heigth of a Image
	 * @return array
	 */
	function getNewImageSizes($origWidth, $origHeigth, $maxWidth, $maxHeigth)
	{

		$newSize['width']=$origWidth;
		$newSize['height']=$origHeigth;

		if($maxWidth<$origWidth || $maxHeigth<$origHeigth)
		{
			$scale = min($maxWidth/$origWidth, $maxHeigth/$origHeigth);
			$newSize['width'] = floor($scale*$origWidth);
			$newSize['height'] = floor($scale*$origHeigth);
		}
		return $newSize;
	}

	/**
	 * short description (optional)
	 *
	 * long description
	 * @uses $CFG
	 * @param type? $dst_img description?
	 * @param type? $src_img description?
	 * @param type? $dst_x description?
	 * @param type? $dst_y description?
	 * @param type? $src_x description?
	 * @param type? $src_y description?
	 * @param type? $dst_w description?
	 * @param type? $dst_h description?
	 * @param type? $src_w description?
	 * @param type? $src_h description?
	 * @return ?
	 * @todo Finish documenting this function
	 */
	function ImageCopyBicubic ($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) {

		global $CFG;

		if (function_exists('ImageCopyResampled') and $CFG->gdversion >= 2) {
		   return ImageCopyResampled($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y,
									 $dst_w, $dst_h, $src_w, $src_h);
		}

		$totalcolors = imagecolorstotal($src_img);
		for ($i=0; $i<$totalcolors; $i++) {
			if ($colors = ImageColorsForIndex($src_img, $i)) {
				ImageColorAllocate($dst_img, $colors['red'], $colors['green'], $colors['blue']);
			}
		}

		$scaleX = ($src_w - 1) / $dst_w;
		$scaleY = ($src_h - 1) / $dst_h;

		$scaleX2 = $scaleX / 2.0;
		$scaleY2 = $scaleY / 2.0;

		for ($j = 0; $j < $dst_h; $j++) {
			$sY = $j * $scaleY;

			for ($i = 0; $i < $dst_w; $i++) {
				$sX = $i * $scaleX;

				$c1 = ImageColorsForIndex($src_img,ImageColorAt($src_img,(int)$sX,(int)$sY+$scaleY2));
				$c2 = ImageColorsForIndex($src_img,ImageColorAt($src_img,(int)$sX,(int)$sY));
				$c3 = ImageColorsForIndex($src_img,ImageColorAt($src_img,(int)$sX+$scaleX2,(int)$sY+$scaleY2));
				$c4 = ImageColorsForIndex($src_img,ImageColorAt($src_img,(int)$sX+$scaleX2,(int)$sY));

				$red = (int) (($c1['red'] + $c2['red'] + $c3['red'] + $c4['red']) / 4);
				$green = (int) (($c1['green'] + $c2['green'] + $c3['green'] + $c4['green']) / 4);
				$blue = (int) (($c1['blue'] + $c2['blue'] + $c3['blue'] + $c4['blue']) / 4);

				$color = ImageColorClosest ($dst_img, $red, $green, $blue);
				ImageSetPixel ($dst_img, $i + $dst_x, $j + $dst_y, $color);
			}
		}
	}

	//Pagination function
	function print_paging($totalcount, $page, $perpage, $baseurl, $pagevar='page',$nocurr=false, $return=false,$gallery=false,$start,$list_in_current_page) {
		$maxdisplay = 5;
		$output = '';
	    // Cleaning up base url string
        if($baseurl){
            $baseUrlSplit = explode("?",$baseurl);
            parse_str($baseUrlSplit[1],$queryString);
            unset($queryString['page']);
            $baseurl = $baseUrlSplit[0] . "?" . http_build_query($queryString);
        }
		if ($totalcount > $perpage) {
			$start_display = ($page == 0)?1:($page*$perpage)+1;
			$end_display = ($start_display+$perpage-1);
			$total_pages = ceil($totalcount / $perpage);
			$end_display = ($end_display >= $totalcount)?$totalcount:$end_display;
			$output .= '<div class="wraper">			
				<div class="news_wraper_left">Displaying <strong>'.($start_display).'</strong> to <strong> '.($end_display).'</strong> of<strong> '.$totalcount.'</strong> total</div>
				<div class="list_page"> Page: '.($page+1).'/'.$total_pages.'    </div>
				<div class="news_wraper_right"><div class="right_holder">';
			if ($page > 0) {
				$pagenum = $page - 1;
				$output .= '<div class="jobs_pagination">';
				if(!$gallery){
					$output .= '<a class="previous" href="'. $baseurl . $pagevar .'='. $pagenum .'">';
					$output .= 'Previous</a></div>';
					//$output .= '<img src="images/pagination_arrow_left_red.gif" width="15" height="14" border="0" alt="" /></a></div>';
				}
				else{
					$output .= '<a class="previous" href="'. $baseurl . $pagevar .''. $pagenum .'">';
					$output .= 'Previous</a></div>';
					//$output .= '<img src="images/pagination_arrow_left_red.gif" width="15" height="14" border="0" alt="" /></a></div>';
				}
			}else{
				$output .= '<div class="jobs_pagination_arrow_gray">';
					$output .= 'Previous</div>';
				//$output .= '<img src="images/pagination_arrow_left_grey.gif" width="15" height="14" border="0" alt="" /></div>';		
			}

			if ($perpage > 0) {
				$lastpage = ceil($totalcount / $perpage);
			} else {
				$lastpage = 1;
			}
			if ($page > 3) {
				$startpage = $page - 1;
				if(!$gallery)
					$output .= '<div class="jobs_pagination"><a href="'. $baseurl . $pagevar .'=0">1</a></div>...';
				else
					$output .= '<div class="jobs_pagination"><a href="'. $baseurl . $pagevar .'">1</a></div>...';
			} else {
				$startpage = 0;
			}
			$currpage = $startpage;
			$displaycount = $displaypage = 0;
			while ($displaycount < $maxdisplay and $currpage < $lastpage) {
				$displaypage = $currpage+1;
				if ($page == $currpage && empty($nocurr)) {
					$output .= '<div class="jobs_pagination_current">'. $displaypage."</div>";
				} else {
						if(!$gallery)
							$output .= '<div class="jobs_pagination"><a href="'. $baseurl . $pagevar .'='. $currpage .'">'. $displaypage .'</a></div>';
						else
							$output .= '<div class="jobs_pagination"><a href="'. $baseurl . $pagevar .''. $currpage .'">'. $displaypage .'</a></div>';
				}
				$displaycount++;
				$currpage++;
			}
			if ($currpage < $lastpage) {
				$lastpageactual = $lastpage - 1;
					$output .= '<div class="cmi_pagination">...<a href="'. $baseurl . $pagevar .'='. $lastpageactual .'">'. $lastpage .'</a></div>';
			}

			$pagenum = $page + 1;
			//right
			if ($pagenum != $displaypage) {
					$output .= '<div class="jobs_pagination">';
					if(!$gallery){
						$output .= '<a class="next" href="'. $baseurl . $pagevar .'='. $pagenum .'">';
						$output .= 'Next</a></div>';
						//$output .= '<img src="images/pagination_arrow_right_red.gif" width="15" height="14" border="0" alt="" /></a></div>';
					}
					else{
						$output .= '<a class="next" href="'. $baseurl . $pagevar .''. $pagenum.'">';
						$output .= 'Next</a></div>';
						//$output .= '<img src="images/pagination_arrow_right_red.gif" width="15" height="14" border="0" alt="" /></a></div>';
					}
			}else{
				$output .= '<div class="jobs_pagination_arrow_red">';
						$output .= 'Next</div>';
				//$output .= '<img src="images/pagination_arrow_right_gray.gif" width="15" height="14" border="0" alt="" /></div>';		
			}
			//end of right

			$output .= '</div></div></div>';
		}

		if ($return) {
			return $output;
		}

		return $output;
	}

	function get_country_option($country_id)
	{
		global $CFG;
		$parentsql = "SELECT id,name FROM ".$CFG->prefix."country WHERE status = 'active' and id = 223 ORDER BY name ASC";
		$parent_zone = get_records_sql($parentsql);
		$selected = '';
		$html = '';
		//$html .= "<option value='0'>Select Country</option>";
		if(!empty($parent_zone))
		{
			foreach($parent_zone as $data)
			{
				if($country_id  == $data->id)
				{
					$selected = 'selected=selected';
				}
				else
				{
					$selected ='';							
				}
				$html .= "<option value=".$data->id ." $selected>".$data->name."</option>";
			}
		}
		echo $html;
	}

	function get_state_option($state_id,$country_id)
	{
		$country_id = 223;
		global $CFG;	
		$parentsql = "SELECT id,name FROM ".$CFG->prefix."state WHERE status = 'active' AND country_id = '".$country_id."' ORDER BY name";
		
		$parent_zone = get_records_sql($parentsql);
		$selected = '';
		$html = '';
		$html .= "<option value='0'>Select State</option>";
		if(!empty($parent_zone))
		{
			foreach($parent_zone as $data)
			{
					if($state_id  == $data->id)
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

	function get_role($role_name)
	{
		global $CFG;	
		$parentsql = "SELECT * FROM ".$CFG->prefix."role WHERE status = 'active' ORDER BY rolename";
		$parent_role = get_records_sql($parentsql);
		$selected = '';
		$html = '';
		$html .= "<option value='0'>Select Role</option>";
		if(!empty($parent_role)){
			foreach($parent_role as $data){
				{
					if($role_name  == $data->rolename){
						$selected = 'selected=selected';
					}
					else
						$selected ='';
						$dataname = ucwords(str_replace("_"," ",$data->rolename));
						$html .= "<option value=".$data->rolename." $selected>".$dataname."</option>";
				
				}
			}
		}
		echo $html;
	}

	function insert_page_content()
	{
		global $CFG;

		include('../settings.php');
		if(!empty($manage_page_content)){
			foreach($manage_page_content as $value){
				$sql = "SELECT * FROM {$CFG->prefix}page_contents WHERE page_name = '$value'";
				$data = get_record_sql($sql);
				if(empty($data)){
					$insert = new object();
					$insert->page_name = $value;
					$insert->page_contents = ucwords(str_replace('_',' ',$value));
					$insert->status = 'active';
					$insert->added_date = time();
					$ins = insert_record('page_contents',$insert);
					if($ins){
						cmi_add_to_log('Page Content','Add',"New Page Content page $value added.");
					}
					else{
						cmi_add_to_log('Page Content','Add',"Fail to Page Content page $value");
					}
				}
			}
		}
	}

	/*Update role access for a user in allowed_sections*/
	function update_lib_role()
	{
		global $CFG,$menu_access;
		error_reporting(0);
		foreach($menu_access as $menu)
		{
				$variable = "allowed_section_".$menu[0];
				$array_tag[$menu[0]] = $menu[0];
				$$variable .= get_field('section','id','section_name',$menu[1]).',';
		}
		foreach($array_tag as $menu)
		{
			$variable = "allowed_section_".$menu;

			$check = get_record('config','name',$variable);
			if(!isset($check->id))
			{
				$cnf_update_config = new object();
				$cnf_update_config->name	= $variable;
				$cnf_update_config->value	= substr($$variable,0,-1);
				insert_record('config',$cnf_update_config);
			}
			else
			{
				$cnf_update_config = new object();
				$cnf_update_config->id		= $check->id;
				$cnf_update_config->name	= $variable;
				$cnf_update_config->value	= substr($$variable,0,-1);
				update_record('config',$cnf_update_config);
			}

			$sql = "UPDATE {$CFG->prefix}users SET allowed_sections = '".substr($$variable,0,-1)."' WHERE role_name = '$menu'";
			execute_sql($sql);
		}
		error_reporting($CFG->debug);
	}

	//upload any file format start
	function upload_files($file_field_name, $dir_path)
	{
			if (!file_exists($dir_path)) 
			{
				mkdir($dir_path);
				chmod($dir_path, 0755);
			}
			if(!empty($_FILES[$file_field_name]['tmp_name']))
			{
				$csvtarget = resolve_filename_collisions($dir_path, array(basename($_FILES[$file_field_name]['name'])), $format='%s_%d.%s');
				$originalfile = $dir_path.$csvtarget[0];
													
				if(move_uploaded_file($_FILES[$file_field_name]['tmp_name'],$originalfile))
				{
					return $csvtarget[0];
				}else{
					return 0;
				}
			}
			else
			{
				return 0;
			}

	}

	//upload any file format end, used by duplicate checker function
	function check_exist($like_object)
	{
		return get_records($like_object->table,'id !='.$like_object->current_id.' AND status = \'active\' AND '.$like_object->field,$like_object->value,$like_object->field,$like_object->field,0,1);
		
	}

	function generate_per_page_activities($perpage,$count,$arr)
	{
		$str = "";
		$arr = array_reverse($arr);
		for($i=1;$i<=$count;$i++)
		{
				$value=array_pop($arr);
				if($value == $perpage){
					$str .= "<option value='".$value."' selected>".$value."</option>";
				}
				else{
					$str .= "<option value='".$value."'>".$value."</option>";
				}
		}
		return($str);
	}

	function update_status_record($status_id,$status,$table)
	{
		$record				= new object();
		$record->id			= $status_id;
		$record->status		= $status;
		if(update_record($table, $record))
		{
			cmi_add_to_log('$table Management',$status,$status.'d $table id: '.$status_id);
			$message =($status == 'delete' )?"$table Record Deleted Successfully !!":"$table Status Updated Successfully !!";
		}
	}
	
	function get_sorting_header($page_arr,$page_name,$theme,$columns)
	{		
		extract($page_arr);
		$arr = array();
		foreach ($columns as $column)
		{
			$string[$column] = get_string("$column","cmi");
			
			if ($sort != $column)
			{
				$columnicon = "";
				if ($column == "id")
				{
					$columndir = "DESC";
				}
				else{
					$columndir = "ASC";
				}
			}
			else
			{
				$columndir = $dir == "ASC" ? "DESC":"ASC";
				if ($column == "id")
				{
					$columnicon = $dir == "ASC" ? "up":"down";
				}
				else
				{
					$columnicon = $dir == "ASC" ? "down":"up";
				}

				$columnicon = "<img src='theme/$theme/images/$columnicon.gif' alt='' />";
			}

			if($column != 'check'){
				$page_arr['sort']	= $column;
				$page_arr['dir']	= $columndir;
				$page_var = http_build_query($page_arr,'','&amp;');	
				$$column = "<a href='$page_name?$page_var'>".$string[$column]." </a>$columnicon";
				
			}
			else
			{
				$$column = "$string[$column]";
			}

			$arr[$column] =  $$column;
		}
		return $arr;
	}

	function print_page_meta_data($m_type,$page_name,$table_name='')
	{
		global $CFG;
		
		if(($table != ''))
		{		
			$seo_keyword = optional_param('seo_keyword','',PARAM_TEXT);
			$meta_object = get_record($table_name,'seo_keyword',$seo_keyword);
			print_meta_data($table_name, '', $meta_object->id);
		}
		elseif($m_type == 'Page')
		{
			print_meta_data($m_type, $page_name);
		}
	}
	function check_login(){
		if(!isset($_SESSION['user_login']) || !isset($_SESSION['user_type'])  || !isset($_SESSION['user_username']) || !isset($_SESSION['user_login_time']))
		{
			redirect("index.html");
		}
		elseif(isset($_SESSION['user_login']) && ($_SESSION['user_login'] != 'yes')) {
			redirect("index.html");
		}		
		
		if(isset($_SESSION['user_login']) && !empty($_SESSION['user_login']) && isset($_SESSION['user_type']) && !empty($_SESSION['user_type']) ) {
			$user_info = '';
			$table = '';
			$reg_type = $_SESSION['user_type'];

			switch($reg_type){
				case 'student':$table = 'student';break;
				case 'counselor':$table = 'counselors';break;
				case 'parent':$table = 'parent';break;
				case 'school':$table = 'schools';break;
				case 'teacher':$table = 'teacher';break;
				case 'staff':$table = 'staff';break;
			}			
			if($table != '' && $table != 'staff' ){
				$user_info = get_record($table,'user_id',$_SESSION['s4c_user_id']);
				return $user_info;
			}
			if($table != '' && $table == 'staff'){
				
				$user_info = get_record('school_staff_user','fe_staff_id ',$_SESSION['s4c_user_id']);
				return $user_info;
			}

		}
	}
	function get_personal_info($id){
		global $CFG;		
		if(isset($_SESSION['user_type']) && !empty($_SESSION['user_type']) ) {
			$user_info = '';
			$table = '';
			$reg_type = $_SESSION['user_type'];

			switch($reg_type){
				case 'student':
								$sql = "select first_name,last_name,gender,date_of_birth,address,street,city,state,zip_code from {$CFG->prefix}student where user_id=$id";
								$table = 'student';
								break;
				case 'counselor':
								$sql = "select first_name,last_name,proffession,gender,date_of_birth,address,street,city,state,zip_code from {$CFG->prefix}counselors where user_id=$id";
								$table = 'counselors';
								break;
				case 'parent':
								$sql = "select first_name,last_name,date_of_birth,address,street,city,state,zip_code from {$CFG->prefix}parent where user_id=$id";
								$table = 'parent';
								break;
				case 'school':
								$sql = "select school_name,	web_url,address,street,city,state,zip_code from {$CFG->prefix}schools where user_id=$id";
								$table = 'schools';	
								break;
				case 'teacher':
								$sql = "select first_name, last_name, gender, date_of_birth, address,street, city,state,  primary_phone, phone, private_email,zip_code from {$CFG->prefix}teacher where user_id=$id";
								$table = 'teacher';
								break;
			}			
			if($table != '')
			$user_info = get_record_sql($sql);
			return $user_info;
		}
	}
	function get_category_option($parent_id)
	{
		global $CFG;

		$parentsql = "SELECT * FROM ".$CFG->prefix."majors WHERE status != 'delete' AND parent_id = 0 AND status = 'active'";
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
	function get_us_states(){		
				echo'
				<option value="AL">Alabama</option>
				<option value="AK">Alaska</option>
				<option value="AZ">Arizona</option>
				<option value="AR">Arkansas</option>
				<option value="CA">California</option>
				<option value="CO">Colorado</option>
				<option value="CT">Connecticut</option>
				<option value="DE">Delaware</option>
				<option value="DC">District of Columbia</option>
				<option value="FL">Florida</option>
				<option value="GA">Georgia</option>
				<option value="HI">Hawaii</option>
				<option value="ID">Idaho</option>
				<option value="IL">Illinois</option>
				<option value="IN">Indiana</option>
				<option value="IA">Iowa</option>
				<option value="KS">Kansas</option>
				<option value="KY">Kentucky</option>
				<option value="LA">Louisiana</option>
				<option value="ME">Maine</option>
				<option value="MD">Maryland</option>
				<option value="MA">Massachusetts</option>
				<option value="MI">Michigan</option>
				<option value="MN">Minnesota</option>
				<option value="MS">Mississippi</option>
				<option value="MO">Missouri</option>
				<option value="MT">Montana</option>
				<option value="NE">Nebraska</option>
				<option value="NV">Nevada</option>
				<option value="NH">New Hampshire</option>
				<option value="NJ">New Jersey</option>
				<option value="NM">New Mexico</option>
				<option value="NY">New York</option>
				<option value="NC">North Carolina</option>
				<option value="ND">North Dakota</option>
				<option value="OH">Ohio</option>
				<option value="OK">Oklahoma</option>
				<option value="OR">Oregon</option>
				<option value="PA">Pennsylvania</option>
				<option value="RI">Rhode Island</option>
				<option value="SC">South Carolina</option>
				<option value="SD">South Dakota</option>
				<option value="TN">Tennessee</option>
				<option value="TX">Texas</option>
				<option value="UT">Utah</option>
				<option value="VT">Vermont</option>
				<option value="VA">Virginia</option>
				<option value="WA">Washington</option>
				<option value="WV">West Virginia</option>
				<option value="WI">Wisconsin</option>
				<option value="WY">Wyoming</option>';
	}

	function get_user_info($id){		
		$fe_user_info		= get_record('fe_users','id',$id);

		//print_object($fe_user_info->user_type);
		$user_table		= '';
		if(isset($fe_user_info)){
			switch($fe_user_info->user_type){
				case 'student':$user_table = 'student';break;
				case 'counselor':$user_table = 'counselors';break;
				case 'parent':$user_table = 'parent';break;
				case 'school':$user_table = 'schools';break;
				case 'teacher':$user_table = 'teacher';break;
			}
		}
		$user_info = get_record($user_table,'user_id',$id);
		if($fe_user_info->user_type == 'school'){
			$name = $user_info->school_name; 
		}else{
			$name = $user_info->first_name.' '.$user_info->last_name; 
		}
		$user_info->name = $name;
		$user_info->pid = $user_info->id;
		unset($user_info->id);
		unset($user_info->added_date);
		unset($user_info->status);
		$return_object = (object)(array_merge((array)$fe_user_info,(array)$user_info));
		//print_object($info);
		return $return_object;
	}

	function get_data($table,$field,$selectid=""){		
		Global $CFG;
		$sql = "SELECT * FROM ".$CFG->prefix.$table." WHERE status = 'active'";
		$data = get_records_sql($sql);
		echo '<option value="0">None</option>';
		if(!empty($data)){
			foreach($data as $value){
				if($value->id == $selectid){
					echo '<option value="'.$value->id.'"selected="selected">'.$value->$field.'</option>';
				}
				else{
					echo '<option value="'.$value->id.'">'.$value->$field.'</option>';
				}
			}
		}
		$selectid = '';
	}
	function get_data_frontend($table,$field,$selectid,$specified_where_field= ''){		
		
		Global $CFG;

		if($specified_where_field == ''){
			$sql = "SELECT * FROM ".$CFG->prefix.$table." WHERE id=".$selectid." AND status = 'active'";
		}
		else{
			$sql = "SELECT * FROM ".$CFG->prefix.$table." WHERE ".$specified_where_field."='".$selectid."' AND status = 'active'";
		}
		$data = get_record_sql($sql);
		
		if(!empty($data)){
			
			if(isset($data->$field)){
				return $data->$field;
			}
			else{
				return '';
			}
			
		}
	}

	function get_membership($id){
		Global $CFG;


		if($id != ''){
			$student_membership_data = get_records_sql("Select * from ".$CFG->prefix."school_membership sm inner join school_member_ship_types smt on (sm.school_memberShip_typeid = smt.id) where sm.school_id = $id");

			$schoolsql = "select * from {$CFG->prefix}school_membership sm INNER JOIN {$CFG->prefix}school_member_ship_types mt on(sm.school_memberShip_typeid = mt.id) where sm.school_id  = $id AND sm.status = 'active'";
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
				$text .= "<select name='membership' onchange='insert_value(this.value,this.id)'>
							<option value='0' >Select</option>";
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
					
			$text .= '<option id="'.$data->id.'" value="'.$data->id.'##'.$total_amount.'##'.$discount.'##'.$data->validity.'" >'.$data->title.'</option>"';
							
						}
						else{
							
						}
					}
				}
				$text .=  '</select>';
							
			}
			echo $text;
		}
	}

	function cmi_genrate_url($title)
	{
		return (strtolower(str_replace(array(" ","/"),array("_","-"), $title)));
	}

	function get_banner(){	
		Global $CFG;
		
			?>
		 <div class="banner"> <img src="<?php echo $CFG->siteroot;?>/file.php/schools/<?php if(isset($student_data->logo)) echo $student_data->logo; ?>" alt="Banner" height="150"/>
				<div class="banner-heading"> <b><span><?php if(isset($student_data->school_name)) echo $student_data->school_name; ?></span></b><br/>
				  <?php if(isset($student_data->city)) echo get_field('city','name','status','active','id',$student_data->city).', '; ?> <?php if(isset($student_data->state)) echo get_field('state','name','status','active','id',$student_data->state); ?> </div>
				 
			  </div>
	<?php
		}

	function get_menu_college($id,$selected,$url){		
		Global $CFG;
		?>
		<div class="inner_nav">
		<?php 
			if($url != ''){ ?>
				<ul>
				  <li <?php if($selected == 'colleges_profile') echo 'class="selected"';?>>
				  <a href="<?php echo $CFG->siteroot.'/'.$url.'/profile';?>">Profile</a></li>
				  <li <?php if($selected == 'colleges_degrees') echo 'class="selected"';?>>
				  <a href="<?php echo $CFG->siteroot.'/'.$url.'/majors_and_degrees';?>">Majors and Degrees</a></li>
				  <li <?php if($selected == 'colleges_culture_campus_life') echo 'class="selected"';?>>
				  <a href="<?php echo $CFG->siteroot.'/'.$url.'/culture_campus_life';?>">Culture & Campus Life</a></li>
				   <li <?php if($selected == 'colleges_scholarships') echo 'class="selected"';?>>
				   <a href="<?php echo $CFG->siteroot.'/'.$url.'/scholorship';?>">Scholarships</a></li>
				  <li <?php if($selected == 'college_news') echo 'class="selected"';?>>
				  <a href="<?php echo $CFG->siteroot.'/'.$url.'/news';?>">News</a></li>
				  <li <?php if($selected == 'colleges_gallery') echo 'class="selected"';?>>
				  <a href="<?php echo $CFG->siteroot.'/'.$url.'/gallery';?>">Gallery</a></li>
				  <li <?php if($selected == 'colleges_admissions') echo 'class="selected"';?>>
				  <a href="<?php echo $CFG->siteroot.'/'.$url.'/admission';?>">Admissions</a></li>
				  <li <?php if($selected == 'colleges_contact_information') echo 'class="selected"';?>>
				  <a href="<?php echo $CFG->siteroot.'/'.$url.'/contact_information';?>">Contact Information</a></li>
				  <li <?php if($selected == 'colleges_send_enquiry') echo 'class="selected"';?>>
				  <a href="<?php echo $CFG->siteroot.'/'.$url.'/enquiry';?>">Send Enquiry</a></li>
				  <li <?php if($selected == 'colleges_download_brochures') echo 'class="selected"';?>>
				  <a href="<?php echo $CFG->siteroot.'/'.$url.'/brochures';?>">Download Brochures</a></li>
				  
				  <?php if(!isset($_SESSION['s4c_user_id'])){ ?>
					  <li <?php if($selected == 'colleges_related_colleges') echo 'class="selected"';?>>
					<a href="<?php echo $CFG->siteroot.'/'.$url.'/related_colleges';?>">Related colleges</a></li>
				  <?php } ?>

                  <li <?php if($selected == 'college_affiliated_banks') echo 'class="selected"';?>>
                    <a href="<?php echo $CFG->siteroot.'/'.$url.'/affiliated_banks';?>">Affiliated Banks</a></li>
				</ul>
			<?php }
				  else{ ?>
				<ul>
				  <li <?php if($selected == 'colleges_profile') echo 'class="selected"';?>><a href="colleges_profile.html?view=<?php if(isset($id)) echo $id; ?>">Profile</a></li>
				  <li <?php if($selected == 'colleges_degrees') echo 'class="selected"';?>><a href="colleges_degrees.html?view=<?php if(isset($id)) echo $id; ?>">Majors and Degrees</a></li>
				  <li <?php if($selected == 'colleges_culture_campus_life') echo 'class="selected"';?>><a href="colleges_culture_campus_life.html?view=<?php if(isset($id)) echo $id; ?>">Culture & Campus Life</a></li>
				   <li <?php if($selected == 'colleges_scholarships') echo 'class="selected"';?>><a href="colleges_scholarships.html?view=<?php if(isset($id)) echo $id; ?>">Scholarships</a></li>
				  <li <?php if($selected == 'college_news') echo 'class="selected"';?>><a href="college_news.html?view=<?php if(isset($id)) echo $id; ?>">News</a></li>
				  <li <?php if($selected == 'colleges_gallery') echo 'class="selected"';?>><a href="colleges_gallery.html?view=<?php if(isset($id)) echo $id; ?>">Gallery</a></li>
				  <li <?php if($selected == 'colleges_admissions') echo 'class="selected"';?>><a href="colleges_admissions.html?view=<?php if(isset($id)) echo $id; ?>">Admissions</a></li>
				  <li <?php if($selected == 'colleges_contact_information') echo 'class="selected"';?>><a href="colleges_contact_information.html?view=<?php if(isset($id)) echo $id; ?>">Contact Information</a></li>
				  <li <?php if($selected == 'colleges_send_enquiry') echo 'class="selected"';?>><a href="colleges_send_enquiry.html?view=<?php if(isset($id)) echo $id; ?>">Send Enquiry</a></li>
				  <li <?php if($selected == 'colleges_download_brochures') echo 'class="selected"';?>><a href="colleges_download_brochures.html?view=<?php if(isset($id)) echo $id; ?>">Download Brochures</a></li>
				  <?php if(!isset($_SESSION['s4c_user_id'])){ ?>
					<li <?php if($selected == 'colleges_related_colleges') echo 'class="selected"';?>><a href="colleges_related_colleges.html?view=<?php if(isset($id)) echo $id; ?>">Related colleges</a></li>
				  <?php  } ?>
                    <li <?php if($selected == 'college_affiliated_banks') echo 'class="selected"';?>><a href="college_affiliated_banks.html?view=<?php if(isset($id)) echo $id; ?>">Affiliated Banks</a></li>
				</ul>
				<?php 
					}
				?>
				<div class="clear"></div>
			  </div>
	<?php
			}

		function get_s4c_seo($keyword,$city,$state,$schhol_id){

			if($keyword != ''){
					if(isset($_SESSION['s4c_user_id'])){

						if($_SESSION['s4c_user_id'] == $schhol_id){
							$url = ""; 
						}
						else{
														
							$url	= $state."/".$city."/".$keyword;
						}
					}
					else{
						$url	= $state."/".$city."/".$keyword;
					}
			}
			else{
				$url = "";
			}
				return $url;	
		}


    /**
     * Function to generate html select tag and options
     */
    function get_select_tag($name,$options,$selected="",$default=true,$class="",$is_age=false){
        if($options==="month"){
            $options_array = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        } else if($options==="date"){
            for($d=1;$d<=31;$d++){
                $options_array[$d] = $d;
            }
        } else if($options==="year"){
            $cy=date('Y');
            $cy = $is_age?$cy-6:$cy;
            for($y=$cy;$y>=1910;$y--){
                $options_array[$y] = $y;
            }
        } else if(is_array($options)){
            $options_array = $options;
        }

        if($default){
            // Hack for year selection, to avoid resetting the index
            if($options==="year"){
                $options_array[0] = "--select--";
            } else {
                array_unshift($options_array,"--select--");
            }
        }
        $option_string = "";
        foreach($options_array as $key=>$option){
            if(is_object($option)){
                $s = $option->id==$selected?'selected="selected"':"";
                $option_string .= "<option value='".$option->id."' ".$s.">".$option->name."</option>";
            } else {
                $s = $key==$selected?'selected="selected"':"";
                $option_string .= "<option value='".$key."' ".$s.">".$option."</option>";
            }
        }
        $select = "<select name='".$name."' id='".$name."' class='".$class."'>".$option_string."</select>";
        return $select;
    }

    /**
     * Function to delete images from personal folder and gallery folder
     * Delete other backup files/other sized images too (f1_<name> to f6_<name>)
     */
    function delete_images($image,$path='personal',$recursive=false){
        global $CFG;
        $base_path = $CFG->dataroot.'/'.$path.'/';
        if(isset($image) && $image!="" && file_exists($base_path.$image)){
            unlink($base_path.$image);
            // Delete other backup files/other sized images too (f1_<name> to f6_<name>)
            if($recursive===true) {
                for($i=1;$i<=6;$i++){
                    $full_path = $base_path."f".$i."_".$image;
                    if(file_exists($full_path)){
                        $a = unlink($full_path);
                    }
                }
            }
        }
    }

?>
