<?php 
	require_once("config.php");	
	$theme = current_theme();	
	
	$message = $date = $time ="";
	$master_table_name = 'articles';

	$name				= optional_param('name','', PARAM_TEXT);
	$seo_keyword		= optional_param('seo_keyword','', PARAM_TEXT);
	$category_id		= optional_param('category_id','', PARAM_RAW);
	$article_date		= optional_param('article_date','', PARAM_RAW);
	$long_description	= optional_param('long_description','', PARAM_RAW);
	

	$page_name =  substr(str_replace(array(str_replace('','',$CFG->dirroot),''),'',($_SERVER['SCRIPT_FILENAME'])),'1');
	$page_manage = str_replace('form','manage',$page_name);
	$page_title = ucwords(str_replace('_',' ',str_replace('form.php','',$page_name)));
		
	if(isset($_GET) && isset($_GET['edit_load']))
	{
		$caption_txt	= "Edit";
		$edit_flag		= true;
		$edit_id		= required_param('edit_load', PARAM_INT);
		$edit_data		= get_record($master_table_name, 'id', $edit_id);

		$name				= $edit_data->name;
		$category_id		= $edit_data->category_id;
		$article_date		= $edit_data->article_date;
		$seo_keyword		= $edit_data->seo_keyword;
		$long_description	= $edit_data->long_description;
	}
	else
	{
		$edit_id = 0;
	}

	if(isset($_POST['update_mode']))
	{
		$hiddenid			= optional_param('hiddenid','', PARAM_TEXT);		
		$added_date			= '';

		$update_mode = new object();

		$update_mode->id				= $hiddenid;
		$update_mode->name				= $name;
		$update_mode->category_id		= $category_id;	
		$update_mode->article_date		= $article_date;
		$update_mode->seo_keyword		= $seo_keyword;
		$update_mode->long_description	= $long_description;
		$message = '';
		if(!empty($update_mode))
		{
			$update = update_record($master_table_name, $update_mode);
			if($update)
			{
				?>
				<script  type="text/javascript">	
					parent.window.location = '<?php echo $page_manage; ?>';
					parent.Mediabox.close();			
				</script>
				<?php
			}
		}
	}

	if( isset($_POST['add_mode']) && $_POST['add_mode'] )
	{			
		$added_date 			= time();
		$allowed_sections		= "";

		$add_mode	 = new object();

		$add_mode->name =$name ;
		$add_mode->long_description =$long_description;
		$add_mode->category_id 		=$category_id;
		$add_mode->article_date		= $article_date ;
		$add_mode->seo_keyword		= $seo_keyword ;
		$add_mode->approved			= '1' ;
		$add_mode->added_date 		=$added_date ;
		
		if(!empty($add_mode->name))
		{
			if($new = insert_record($master_table_name, $add_mode))
			{
				cmi_add_to_log('article','Insert','New category '.$name.' added');
				?>	<script  type="text/javascript">
					parent.window.location = '<?php echo $page_manage; ?>';
					parent.Mediabox.close();			
				</script>
				<?php
			}
		}
	}
	

cmi_include_head('css');
cmi_include_head('js');
cmi_include_editor();	
?>

<script type="text/javascript">

	window.addEvent('domready', function()
	{
		new FormCheck('frm_add_mode',{'submit':false,'onValidateSuccess':processForm});
		var validator = new duplicate_checker({
			trigger: 'keyup',
			table: '<?php echo $master_table_name; ?>',
			field: 'seo_keyword',
			current_id: '<?php echo $edit_id ;?>',
			check_exist: 'yes',
			element: $('seo_keyword'),
			availableImage: '<?php echo "theme/$theme/images/available.png"; ?>',
			takenImage: '<?php echo "theme/$theme/images/unavailable.png"; ?>',
			offset: { x: 4, y: 4 },
			url: 'ajax_handler.php'
		});
		new FormCheck('frm_add_mode');
		get_datepicker_without_toggler();	
	});
	function processForm()
	{
		if($('toggler').value == 1)
			{
				$('frm_add_mode').submit();
			}
	}
	function get_category(id,flag,pid){
		
			if(id != ''){
				var req = new Request({
				 method: 'get',
				 url: 'test.php',
				 data: { 'id' : id,'flag' : flag },
				 onRequest: function() { },
				 onComplete: function(response) { 
					$(pid).innerHTML = response;
				}
			 
			}).send();	
			}
		}
</script>

	<table width="100%" cellspacing="0" cellpadding="0" border="0">	
		<tr>
			<td>
				<div class="DotedHeader" width="70%">
					<?php
					if(isset($_GET['edit_load']))
					{ 
						echo "Update $page_title :";
					}
					else 
						echo "Add $page_title:"; 
					?>
				</div>
				<br>

				<?php notify($message);?>

				<form method="post" name="frm_add_mode" id="frm_add_mode" action="<?php echo $page_name; ?>" enctype="multipart/form-data">
					<table border="0" width="100%" align="center" cellspacing="1" cellpadding="1">
						<tbody>
							<tr>
								<td colspan="0" class="Label" width="20%"><?php echo get_string('label_category','cmi'); ?>
								</td>
								<td>
									<select id="category_id" name="category_id" class="validate['required'] txtBox" >
										<option value="0">Select Category</option>
										<?php
									        $sql = "select * from {$CFG->prefix}article_category where status='active'";
											//$sql = "select * from {$CFG->prefix}article_category where id not in(select `parent_id` from article_category where parent_id !=0 group by parent_id ) AND status='active'";
											$category_info = get_records_sql($sql);
											if($category_info){
												foreach($category_info as $record){
													if($category_id == $record->id){
														$class = 'selected';
													}else{
														$class = '';
													}
													if($record->parent_id!=0)
													{
													$sql = "select name from {$CFG->prefix}article_category where status='active' AND id=$record->parent_id";
													$parent_info = get_record_sql($sql);
													echo "<option value='$record->id' $class >".$parent_info->name."/".$record->name."</option>";
													}else{
													echo "<option value='$record->id' $class >$record->name</option>";
													}
												}
											}
										?>									
									</select>
								</td>
								 <td>&nbsp;&nbsp;&nbsp;</td>
							</tr>
							<tr>
								<td class="Label" width="20%">Title : </td>
								<td >
									<input name="name" class="validate['required'] txtBox" type="text" size="25" value="<?php if(isset($name)) echo $name;?>" />
								</td>
							</tr>
							<tr>
								<td class="Label" width="20%">Seo Keyword : </td>
								<td>
									<input id="seo_keyword" name="seo_keyword" class="validate['required','seo'] taken txtBox" type="text" size="38" value="<?php if(isset($seo_keyword)) echo $seo_keyword;?>" />
									
								</td>
							</tr>	
							<tr>
								<td class="Label" width="20%">Article Date  : </td>
								<td >
									<input readonly="true" name="article_date" value="<?php echo $article_date;?>" maxlength="30" id="date" style="width: 150px;" type="text" class="validate['required'] date  demo_vista_without_toggler" value=<?php echo $article_date;?> />
								</td>
							</tr>						
							 <tr>
								<td colspan="2" > 
									<span class="Label" align="center">Description</span> 
									<textarea name="long_description" id="editor1" class="editor txtBox"><?php 							if(isset($long_description)) echo $long_description;?></textarea>
										<script type="text/javascript">
											init_ckfinder('editor1');
										</script>
								 </td>
								
							</tr>
							<?php 
							if(isset($_GET['edit_load']))
							{
								$submitname = "update_mode";
								?>
								<input type="hidden" name="update_mode" value="update_mode" />
								<?php
								$hiddenid = $_GET['edit_load'];
							}
							else
							{ 
								$submitname = "add_mode";
								?>
								<input type="hidden" name="add_mode" value="add_mode" />
								<?php
								$hiddenid = '';
							}
							?>
							<tr>
								<td colspan="2" style="text-align:center">
									<input type="hidden" name="master_table_name" value="<?php echo $master_table_name;?>" />
									<input type="hidden" name="hiddenid" value="<?php echo $hiddenid;?>" />
									<input type="hidden" name="toggler" id="toggler" value="1"/>
									<input name="<?php echo $submitname;?>" type="submit" value="Save">
								</td>
							</tr>
						</tbody>
					</table>
				</form>
			</td>
		</tr>
	</table>