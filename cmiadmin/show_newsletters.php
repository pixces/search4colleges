<?php 
	require_once("config.php");

	//print_r($_GET);

	define('menu_status', 'Manage News');
	$theme = current_theme();
	isLogin();
	//isallow();
	$edit_flag= false;
	
	$page_name =  substr(str_replace(array(str_replace('','',$CFG->dirroot),''),'',($_SERVER['SCRIPT_FILENAME'])),'1');
	$page_form = str_replace('manage','form',$page_name);
	$page_title = ucwords(str_replace('_',' ',str_replace('manage.php','',$page_name)));
	
	$sort				= optional_param('sort', 'email', PARAM_TEXT);
    $dir				= optional_param('dir', 'DESC', PARAM_ALPHA);
    $page				= optional_param('page', 0, PARAM_INT);	
	$perpage			= $CFG->news_letters_per_page=10;        // how many per page
	$search				= optional_param('search_title', '', PARAM_RAW);
	$search_id			= optional_param('id', '', PARAM_RAW);
	$search_username	= optional_param('username', '', PARAM_RAW);

	$message	= "";	
	
	
	$member_data = get_records_sql("SELECT * FROM ".$CFG->prefix."members_type");
	
	if($page == 0)
	{
		$pager = 0;
	}
	else
	{
		$pager = $page * $perpage;
	}


	if(isset($_GET) && isset($_GET['show']))
	{
	
		$caption_txt	= "Edit";
		$edit_flag		= true;
		$show_id		= required_param('show', PARAM_INT);
		//$show_data		= get_records_sql("SELECT * FROM ".$CFG->prefix."fe_users WHERE user_type=".$show_id." AND newsletter='yes' AND status='active'");
	}
	else
	{
		$caption_txt	= "Edit";
		$edit_flag		= true;
		$show_id		= 0;
	}
		


	$sort_term    = $sort." ".$dir;				
	$searchfields = array("first_name","email");
	
	if($show_id == 1)
	{
		$extraselect = " newsletter='yes' AND user_type='student'";
	}
	elseif($show_id == 2)
	{
		$extraselect = " newsletter='yes' AND user_type='parent'";
	}
	elseif($show_id == 3)
	{
		$extraselect = " newsletter='yes' AND user_type='counselor'";
	}
	elseif($show_id == 4)
	{
		$extraselect = " newsletter='yes' AND user_type='teacher'";
	}
	elseif($show_id == 5)
	{
		$extraselect = " newsletter='yes' AND user_type='staff'";
	}
	elseif($show_id == 6)
	{
		$extraselect = " newsletter='yes' AND user_type='school'";
	}
	else
	{
		$extraselect = " newsletter='yes'";
	}
	


	if($search != '')
	{
		$extraselect .= " AND (c.first_name like '%".$search."%' OR p.first_name like '%".$search."%' OR s.school_name like '%".$search."%' OR st.first_name like '%".$search."%' OR t.first_name like '%".$search."%' OR u.name like '%".$search."%' OR email like '%".$search."%')";
	}
	if($search_id != '')
	{
		$extraselect .= " AND id = '".$search_id."'";
	}
	

	//echo $pager." , ".$perpage;

	//echo "SELECT fe.*,c.first_name as counselor,p.first_name as parent,s.school_name as school,st.first_name as student,t.first_name as teacher,u.name as staff FROM ".$CFG->prefix."fe_users as fe LEFT JOIN ".$CFG->prefix."counselors as c ON fe.id = c.user_id LEFT JOIN ".$CFG->prefix."parent as p ON fe.id = p.user_id LEFT JOIN ".$CFG->prefix."schools as s ON fe.id = s.user_id LEFT JOIN ".$CFG->prefix."student as st ON fe.id = st.user_id LEFT JOIN ".$CFG->prefix."teacher as t ON fe.id = t.user_id LEFT JOIN ".$CFG->prefix."school_staff_user as u ON fe.id = u.fe_staff_id WHERE".$extraselect." GROUP BY id ORDER BY ".$sort." LIMIT ".$pager.",".($perpage)."";
	mysql_query("SET SQL_BIG_SELECTS=1");
	$user_data = get_records_sql("SELECT fe.*,c.first_name as counselor,p.first_name as parent,s.school_name as school,st.first_name as student,t.first_name as teacher,u.name as staff FROM ".$CFG->prefix."fe_users as fe LEFT JOIN ".$CFG->prefix."counselors as c ON fe.id = c.user_id LEFT JOIN ".$CFG->prefix."parent as p ON fe.id = p.user_id LEFT JOIN ".$CFG->prefix."schools as s ON fe.id = s.user_id LEFT JOIN ".$CFG->prefix."student as st ON fe.id = st.user_id LEFT JOIN ".$CFG->prefix."teacher as t ON fe.id = t.user_id LEFT JOIN ".$CFG->prefix."school_staff_user as u ON fe.id = u.fe_staff_id WHERE".$extraselect." GROUP BY id ORDER BY ".$sort." LIMIT ".$pager.",".($perpage)."");

    //print_r($user_data);
	$user_count = count_records_sql("SELECT count(distinct(fe.id)) FROM ".$CFG->prefix."fe_users as fe LEFT JOIN ".$CFG->prefix."counselors as c ON fe.id = c.user_id LEFT JOIN ".$CFG->prefix."parent as p ON fe.id = p.user_id LEFT JOIN ".$CFG->prefix."schools as s ON fe.id = s.user_id LEFT JOIN ".$CFG->prefix."student as st ON fe.id = st.user_id LEFT JOIN ".$CFG->prefix."teacher as t ON fe.id = t.user_id LEFT JOIN ".$CFG->prefix."school_staff_user as u ON fe.id = u.fe_staff_id WHERE".$extraselect."");
	
	 if (!$user_data)
	 {
			$match = array();
			//print_heading(get_string('norecordsfound'));
			$table = NULL;
	 }
	 else
	  $table = "Yes";

	
	
	if(isset($_GET) && isset($_GET['news_id']))
	{
		$caption_txt	= "Edit";
		$edit_flag		= true;
		$news_id		= required_param('news_id', PARAM_INT);
	}
	//print_header(get_string('cmi','cmi'));
	cmi_include_head('css');
	cmi_include_head('js');
	//print_container_start();
	
?>

<script type="text/javascript">
			window.addEvent('domready', function(){
				new FormCheck('frm_search');


				
				//call Motools tipz initialization
				show_tipz();
				if($('check')){ 
				 $('check').addEvent('click', function() {
					if($('check').get('rel') == 'yes' || $('check').get('rel') == 'null')
					{
						do_check = false;
						$('check').set('checked','').set('rel','no');
					}
					else{
						do_check = true;
						$('check').set('checked','true').set('rel','yes');
					}
					$$('.check-me').set('checked',do_check) 
				 });
				}
				
			});
			
			function checkall(val)
			{
				
					$$('.check-me').each(function(el)
					{
						$$('.checkall').each(function(e2)
						{
							if(e2.checked == true)
									el.checked = true;
								else
									el.checked = false;
					});
					});
			}
			
			function selectall(val)
			{
				$('changesel').innerHTML = "Selected <a href='javascript:void(0);' onclick='unselectall();'>Unselect All</a>";
			
				$('selecting').value = "selectall";
				
					$$('.check-me').each(function(el)
					{
						el.checked = true;
					});
						$$('.checkall').each(function(e2)
						{
							e2.checked = true;
						});
			}
		
			function unselectall(val)
			{
				$('changesel').innerHTML = "<a href='javascript:void(0);' onclick='selectall();'>Select All</a>";
			
				$('selecting').value = "unselectall";
				
					$$('.check-me').each(function(el)
					{
						el.checked = false;
					});
						$$('.checkall').each(function(e2)
						{
							e2.checked = false;
						});
			}
		
			function confirm_send(val)
			{
			if($('selecting').value == 'selectall')
			{
				var memberid = $('showing').value;
				call_alert_confirm("Are you sure you want to Send Newsletters to all selected Users?");
				
				parent.window.location = 'send_newsletters.php?send_id=selectall&member_id='+memberid+'&news_id=<?php echo $news_id; ?>';
				parent.Mediabox.close();
			}
			
				var total = '';
				$$('.check-me').each(function(el){
					 if(el.checked == true){
						total += el.value+',';
					 }
					 
				});
				tot = total.substring(0,total.length-1);
				
				
				if(total != '')
				{
				
					call_alert_confirm("Are you sure you want to Send Newsletters to selected Users?");
					
					parent.window.location = 'send_newsletters.php?send_id='+tot+'&news_id=<?php echo $news_id; ?>';
					parent.Mediabox.close();		
					
				}
				else{
					Sexy.error('Select atleast one user to send the Newsletter!'); 
					return false;
				}
			}
			
			
			
		</script>
<table width="100%" cellspacing="0" cellpadding="0" border="0">

	<tr>
		<td>
			<div class="DotedHeader" width="70%">Send Newsletters

			</div>
		    <br>
			<?php notify($message);?>
			<form method="post" name="frm_search" id="frm_search" action="show_newsletters.php?show=<?php echo $show_id; ?>&news_id=<?php echo $news_id; ?>" enctype="multipart/form-data" >
				<table border="0" width="100%" align="center" cellspacing="5" cellpadding="5">
					 <tbody>
						
						  <tr>
							 <td class="Label" width="20%">Select User Type : 
							 </td>
							 <td >
                             <input type="hidden" name="selecting" id="selecting" value="" />
                             <input type="hidden" name="showing" id="showing" value="<?php echo $show_id; ?>" />
                             <input name="user_type" id="user_type" type="radio" value="0"  onclick="javascript:location.href='show_newsletters.php?show=0&news_id=<?php echo $news_id; ?>'" checked="checked" /> ALL &nbsp;
							 <?php
                             foreach($member_data as $value)
							 {?>
                             <input name="user_type" id="user_type" type="radio" value="<?php echo $value->id; ?>"  onclick="javascript:location.href='show_newsletters.php?show=<?php echo $value->id; ?>&news_id=<?php echo $news_id; ?>'" <?php if($show_id == $value->id) {?>  checked="checked" <?php } ?> /><?php echo $value->name; ?>&nbsp;
                             <?php } ?>
                             </td>
							 <td >&nbsp;&nbsp;&nbsp;</td>
						 </tr>
						 
						<tr>
							<td colspan="3">
                            
                            
                            
                            
                            
<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<td>
			<div class="DotedHeader" width="70%">News Letters Users:</div>
			
		         
        <br>
		<?php notify($message);?>
		<table width="100%" border="0">
             <tbody>
			 <tr>
             <td width="5px"></td>
             <td class="Label" align="center" width="90%">
			 
                <table border="0" class="generaltable1 boxaligncenter">
					<tr>
						<td>Keyword: </td>
						<td><input name="search_title" id="search_title" class="txtBox" value="<?php echo $search;?>" type="text"></td>
					</tr>
					<tr>
						<td><input name="btnSearch" id="btnSearch" type="submit" value="Search"></td>
						<td><input name="btnReset" id="btnReset" type="reset" value="Clear Search" onClick="location.href='show_newsletters.php?show=<?php echo $show_id; ?>&news_id=<?php echo $news_id ; ?>'"></td>
					</tr>
				</table>
			</td>
             <td width="5%"></td>
             </tr>
			 <tr><td>&nbsp;</td></tr>
              <tr>
             <td width="5%"></td>
             <td align="center" width="90%">
                 </td>
             <td width="5%"></td>
             </tr>
             <tr>
             <td width="5%"></td>
             <td align="center" width="90%">
                 <div>
				<?php
					if (!empty($table))
					{
				?>
                <div class="back">
                <div class="ii1">
                <div class="ii2">
                <div class="ii3">
				<table class="generaltable1 boxaligncenter" width="100%" cellspacing="1" cellpadding="5">
                  <tbody>
                	<tr>
                		<th class="header c0" scope="col" style="vertical-align:top; text-align:center;;white-space:nowrap;"><input type="checkbox" class="checkall" id="addcheck" name="addcheck[]" value="1" onclick="checkall(this.value);" /></th>
						<th class="header c0" scope="col" style="vertical-align:top; text-align:center;;white-space:nowrap;">Id</th>
                        <th class="header c0" scope="col" style="vertical-align:top; text-align:center;;white-space:nowrap;">Name</th>
                        <th class="header c0" scope="col" style="vertical-align:top; text-align:center;;white-space:nowrap;">Type</th>
                        <th class="header c0" scope="col" style="vertical-align:top; text-align:center;;white-space:nowrap;">Email </th>
                    </tr>
                    <?php foreach($user_data as $data)
					{
					?>
                    <tr>
                    	<td class="cell c0" style=" text-align:center;"><input type="checkbox" class="check-me" id="deletecheck" name="checkbox[]" value="<?php echo $data->id; ?>" /></td>
                        <td class="cell c0" style=" text-align:center;"><?php echo $data->id ?></td>						
                        <td class="cell c0" style=" text-align:center;"><?php if($data->user_type == 'school') { echo $data->school; } elseif($data->user_type == 'counselor') { echo $data->counselor; } elseif($data->user_type == 'parent') { echo $data->parent; } elseif($data->user_type == 'teacher') { echo $data->teacher; } elseif($data->user_type == 'student') { echo $data->student; } elseif($data->user_type == 'staff') { echo $data->staff; }?></td>
						<td class="cell c0" style=" text-align:center;"><?php echo $data->user_type ?></td>
                        <td class="cell c0" style=" text-align:center;"><?php echo $data->email ?></td>
                   </tr>
                   <?php
				   }
				   ?>
                  </tbody>
                </table>
                </div>
                </div>
                </div>         
                </div>           
                <?php
					$searchname = $searchdescription = '';
					if($search_username != ''){
							$searchdescription = "description=$description&amp;";
					}		
					echo '<br>
					<div algin="right"><div align=""right>'.$user_count.' Records Found  </div><div align="right" id="changesel"><a href="javascript:void(0);" onclick="selectall();">Select All</a></div></div>
						<br>
						<div align="center">
								<input type="button" onclick="confirm_send(); return false;" value="Send Newsletter">
						</div>';
					echo '<br/><br/>';
						print_paging_bar($user_count, $page, $perpage,
										 "show_newsletters.php?sort=$sort&amp;dir=$dir&amp;search=$search&amp;".$searchdescription."perpage=$perpage&amp;news_id=$news_id&amp;show=$show_id&amp;");
					}
					else
					{	
						//print_heading('No Records Found');
						?>
						<table width= "600">
							<tr>
								<td align="center">No Records Found</td>
							</tr>
						</table>
					<?php
					}
				?>
				</div>
             </td>
             <td width="5%"></td>
             </tr>
             <tr>
             <td width="5%"></td>
             <td width="90%"></td>
             <td width="5%"></td>
             </tr>
             </tbody></table>
			</td>
	</tr>
</table>

                            
                            </td>
						</tr>
						 
						 
					</tbody>
				</table>
			</form>
		</td>
	</tr>
</table>

