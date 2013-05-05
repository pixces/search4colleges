<?php 
	require_once("config.php");	
	$theme = current_theme();	
	
	$message = "";
	$master_table_name = 'school_cultural_diversity';
		
	$page_name =  substr(str_replace(array(str_replace('','',$CFG->dirroot),''),'',($_SERVER['SCRIPT_FILENAME'])),'1');
	$page_title = ucwords(str_replace('_',' ',str_replace('form.php','',$page_name)));
	
	$title = optional_param('title','', PARAM_TEXT);
	$school_cultural_diversity = get_record($master_table_name,'id','1');
			
	if(isset($_POST['submit']))
	{
		if($school_cultural_diversity)
		{	
			$update_mode = new object();
			$update_mode->id = 1;
			$update_mode->title = $title;
			$update_mode->added_time = time(); 
			if(update_record($master_table_name, $update_mode))
			{
				$message = "Updated cultural diversity!!";
			}
		}
		else
		{
			$add_mode = new object();
			$add_mode->title = $title;
			$add_mode->added_time = time();
			if(insert_record($master_table_name, $add_mode))
			{
				$message = "Added cultural diversity !!";
			}			
		}
	}	

	$school_cultural_diversity = get_record($master_table_name,'id','1');
	if($school_cultural_diversity)
	{
		$title = $school_cultural_diversity->title;
	}

print_header(get_string('cmi','cmi'));
cmi_include_head('css');
cmi_include_head('js');
print_container_start();

?>

<script type="text/javascript">

	window.addEvent('domready', function()
	{				
		new FormCheck('frm_add_mode');			
	});
	
</script>


<table width="100%" cellspacing="0" cellpadding="0" border="0">	
	<tr>
		<td><?php load_menu(); ?></td>
	</tr>
	<tr>
		<td>
			<div class="DotedHeader" width="70%">
				<?php
				if($school_cultural_diversity)
				{ 
					echo "Update $page_title :";
				}
				else 
					echo "Add $page_title:"; 
				?>
			</div>
			
			<br><br>
			
			<form method="post" name="frm_add_mode" id="frm_add_mode" action="<?php echo $page_name; ?>" enctype="multipart/form-data">
				<table border="0" width="100%" align="center" cellspacing="1" cellpadding="1">
					<tbody>							
						<tr>
							<td width="30%"></td>
							<td class="Label" width="20%">Cultural Diversity : </td>
							<td width="10%">
								<input type="text" name="title" class="validate['required'] txtBox" size="40" value="<?php echo $title; ?>"/>
							</td>
							<td width="40%"></td>
						</tr>	
						<tr>
							<td colspan="4" style="text-align:center"><br></td>
						</tr>
						<tr>
							<td colspan="4" style="text-align:center">																		
								<input name="submit" type="submit" value="Save">
							</td>
						</tr>
						<tr>
							<td colspan="4" style="text-align:center"><br></td>
						</tr>
						<tr>
							<td colspan="4" style="text-align:center">																		
								<?php notify($message); ?>
							</td>
						</tr>					
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