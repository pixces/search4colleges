<?php 
	require_once("config.php");
	isLogin();
	//isallow();
	$theme = current_theme();
	$message	= "";
	$master_table_name = 'blog_comment';
	$display='';
	$remove_tag='';
	$short_topic='';
	$topic='';

	$id			= required_param('id', PARAM_INT);
	$blog_info = get_record('blog','id',$id);
	$page_arr		= array(
							'id'			=> $id,
						);

	$page_var = http_build_query($page_arr,'','&amp;');
	if(isset($_GET) && isset($_GET['delete']))
	{
		$com_id			= required_param('delete', PARAM_INT);
		$delete_record			= new object();
		$delete_record->id		= $com_id;
		$delete_record->status	= 'delete';

		if(update_record('blog_comment', $delete_record)){
			$message = "Blog Comment Deleted Successfully !!";		
		}
	}
	if($blog_info){
		$category_info = get_record('blog_category','id',$blog_info->category_id);
		$sql = "SELECT * from ".$CFG->prefix."blog_comment where status='active' AND blog_id=$blog_info->id";
		$comment_info = get_records_sql($sql);
		//$comment_info = get_records('blog_comment','blog_id',$blog_info->id);
		$blog_user_info = get_user_info($blog_info->user_id);
	}
	
	$page_name =  substr(str_replace(array(str_replace('','',$CFG->dirroot),''),'',($_SERVER['SCRIPT_FILENAME'])),'1');
	$page_form = str_replace('manage','form',$page_name);
	$page_title = ucwords(str_replace('_',' ',str_replace('manage.php','',$page_name)));
    
	
?>

<?php 
	print_header(get_string('cmi','cmi'));
	cmi_include_head('css');
	cmi_include_head('js');
	print_container_start();
?>

<script type="text/javascript">

	function mark_unmark_isapproved(val)
	{
		var movealert = new SexyAlertBox();
		var total=document.getElementsByName("isapproved").length;
		var ids=document.getElementsByName("isapproved");

		var cnt=0;
		for(i=0;i<total;i++)
		{
			if(ids[i].checked==true)
			{
				cnt++;
			}
		}
		call_alert_isapproved_confirm("Are you sure ",val);	
	}

	function check_isapproved(val)
	{
		if($('isapproved['+val+']').checked==true)
		{
			chk=1;				
		}
		else if($('isapproved['+val+']').checked==false)
		{
			chk=0;			
		}
		
		 var req = new Request({
		 method: 'post',
		 url: 'ajax_handler.php',
		 data: { 'id' : val,
				 'chk' : chk,
				 'flag' : 'isapproved_comment'						
			 },
		 onRequest: function() {},
		 onComplete: function(response) {
			 $('msg').innerHTML = response;
		 }
		
		}).send();	
		
	 }
</script>

	<table width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td valign="top" width="200"><?php load_menu(); ?></td>
		</tr>
		<tr>
			<td>
				<div class="DotedHeader" width="70%">
					Manage <?php echo $page_title;?>:
				</div>				
				<center>
						<?php if($blog_info){ ?>
							<table width="100%" border="0">	
							<tr><td colspan="2" ><div id="msg" style="text-align:center;"><div></td>
							<tr>
								<td colspan="2" class="td_color">
									<b>Blog Posted By : 
									<?php 
										if($blog_user_info->user_type == 'school'){
											echo ucwords($blog_user_info->school_name).'( '.date('d/m/Y',$blog_info->added_date).' )';
										}else{
											echo ucwords($blog_user_info->first_name.' '.$blog_user_info->last_name).'( '.date('d/m/Y',$blog_info->added_date).' )';
										}
								?> 
								</b>
								</td>
							</tr>
							<tr> <td colspan="2">&nbsp; </td></tr>
							<tr>
								<td width="20%"><b>Title : </b></td>
								<td><?php echo $blog_info->title; ?></td>
							</tr>
							<tr>
								<td width="20%"><b>Description : </b></td>
								<td><?php echo $blog_info->description; ?></td>
							</tr>
							<tr>
								<td width="20%"><b>Story : </b> </td>
								<td><?php echo $blog_info->story ; ?></td>
							</tr>
							<tr>
								<td width="20%"><b>Approved: </b></td>
								<td><?php echo ($blog_info->isapproved == 1)?'Yes':'No'; ?></td>
							</tr>
							<tr> <td colspan="2">&nbsp; </td></tr>
							<tr> <td colspan="2">&nbsp; </td></tr>
							
							<?php 
							if($comment_info){
								$count = count($comment_info);
								$i = 1;
								echo '<tr> <td colspan="2" class="td_color"><b>Comments </b></td></tr>';
								foreach($comment_info as $record){
									$style = ($count == $i)?'':' style="border-bottom:1px dotted black;" ';
									$comment_user_info = get_user_info($record->user_id) ;

									if($record->isapproved==1){
										$isapproved="<input type='checkbox' checked='checked' id='isapproved[".$record->id."]' name='isapproved' value='".$record->id."'  onclick=\"mark_unmark_isapproved(this.value);return false;\">";
									}
									else{
										$isapproved="<input type='checkbox' id='isapproved[".$record->id."]' name='isapproved' value='".$record->id."'  onclick=\"mark_unmark_isapproved(this.value);return false;\">";
									}
									$deletebutton = get_buttons('Delete',"#","theme/$theme/images/delete.png","onclick=\"confirm_delete($record->id,'comment_manage.php','$page_var',''); return false;\"");
									echo '<tr> <td colspan="2">&nbsp; </td></tr>';
									echo '<tr> <td colspan="2" '.$style.' >	';
										echo '<div style="text-align:left"> '.$record->comment.'</div> <br/>';
										echo '<div>';
											echo '<div style="float:left;"><small>Approve/Disapprove '.$isapproved.' '.$deletebutton. '</small></div>';
											if($comment_user_info->user_type == 'school'){
												echo '<div style="float:right"><small>........By '.ucwords($comment_user_info->school_name).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br />'.date('d:m:Y  h:s:i ',$record->added_date).'</small></div>';
											}else{
												echo '<div style="float:right"><small>........By '.ucwords($comment_user_info->first_name).' '.ucwords($comment_user_info->last_name).'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br />'.date('d:m:Y  h:s:i ',$record->added_date).'</small></div>';
											}
											echo '</div>';
										echo '<div>';
									echo '</td></tr>';
									$i++;
								}
							} 
							?>
							<tr> <td colspan="2">&nbsp;</td></tr>

							</table>
							<?php } ?>
					
				</center>
			</td>
		</tr>
	</table>
<?php
	print_container_end();
	print_footer();
?>