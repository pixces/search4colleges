<?php 
	require_once("config.php");
	define('menu_status', 'Manage News');
	$theme = current_theme();
	isLogin();
	//isallow();
	$message = $date = $sendemail = $email = $time = "";
	
	if(isset($_GET) && isset($_GET['edit']))
	{
		$caption_txt	= "Edit";
		$edit_flag		= true;
		$edit_id		= required_param('edit', PARAM_INT);
		$edit_data		= get_record('news_letters', 'id', $edit_id);
		
		$subject		= $edit_data->subject;
		$new_content	= $edit_data->new_content;
	}
	if(isset($_GET) && isset($_GET['news_id']))
	{
		$caption_txt	= "Edit";
		$edit_flag		= true;
		$news_id		= required_param('news_id', PARAM_INT);
		$edit_data		= get_record('news_letters', 'id', $news_id);

		$subject		= $edit_data->subject;
		$new_content	= $edit_data->new_content;
	}
	cmi_include_head('css');
	cmi_include_head('js');
	cmi_include_editor();
?>
<script type="text/javascript">
		window.addEvent('domready', function(){
			new FormCheck('frm_addnewsletters');
			new DatePicker('.demo_vista', { 
					pickerClass: 'datepicker_vista',
					allowEmpty: true ,
					format: 'd F, Y',
					toggleElements: '.date_toggler'
			});
		});
		
		
		function get_category(id,flag,pid){
		
			if(id != ''){
				var req = new Request({
				 method: 'get',
				 url: 'ajax_handler.php',
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
				<?php if(isset($_GET['edit'])){ 
					echo get_string('updt_newsletters','cmi');
				 }
				 if(isset($_GET) && isset($_GET['news_id']))
				 {
					echo 'Send News letter';
				 }
				 elseif(!(isset($_GET['edit'])))
				 {
					echo get_string('add_newsletters','cmi'); 
				 }
				?>

			</div>
		    <br>
			<?php notify($message);?>
			<form method="post" name="frm_addnewsletters" id="frm_addnewsletters" action="newsletters_manage.php" enctype="multipart/form-data" target="_parent">
				<table border="0" width="100%" align="center" cellspacing="5" cellpadding="5">
					 <tbody>
						
						  <tr>
							 <td class="Label" width="20%"><?php echo get_string('subject',"cmi"); ?>: 
							 </td>
							 <td ><input name="subject" class="validate['required'] txtBox" type="text" size="50" value="<?php if(isset($subject)) echo $subject;?>" /></td>
							 <td >&nbsp;&nbsp;&nbsp;</td>
						 </tr>
						 
						<tr>
							<td colspan="3"></td>
						</tr>
						 <tr>
							 <td colspan="3">
								<span class="Label" align="center">News Letter Content :</span> 
								<textarea name="new_content" id="editor1" class="editor txtBox"><?php if(isset($new_content)) echo $new_content;?></textarea>
								<script type="text/javascript">
									init_ckfinder('editor1');
								</script>
								
							</td>
							 <td >&nbsp;&nbsp;&nbsp;</td>
						 </tr>
						 <?php 
							if(isset($_GET['edit'])){
								$valuebutt = "btnUpdate.gif";
								$submitname = "updatenewsletters";
								?>
								<input type="hidden" name="updatenewsletters" value="updatenewsletters" />
								<?php
								$hiddenid = $_GET['edit'];
						?>
							<tr>
								 <td colspan="2" style="text-align:center">
									<input type="hidden" name="hiddenid" value="<?php echo $hiddenid;?>" />
									<input name="<?php echo $submitname;?>" type="submit" value="Update NewsLetters">
								 </td>
							 </tr>
						<?php
							}
							else
							{ 
								if(isset($_GET) && isset($_GET['news_id']))
								 {
									$valuebutt = "btnSave.gif";
									$submitname = "sendnewsletters";
									?>
									<input type="hidden" name="sendnewsletters" value="sendnewsletters" />
									<?php
									$hiddenid = '';
									?>
										<tr>
											 <td colspan="2" style="text-align:center">
												<input type="hidden" name="hiddenid" value="<?php echo $hiddenid;?>" />
												<input name="<?php echo $submitname;?>" type="submit" value="Send News Letters">
											 </td>
										 </tr>
									<?php
								 }	
							 else
								{
									$valuebutt = "btnSave.gif";
									$submitname = "addnewsletters";
									?>
									<input type="hidden" name="addnewsletters" value="addnewsletters" />
									<?php
									$hiddenid = '';
									?>
										<tr>
											 <td colspan="2" style="text-align:center">
												<input type="hidden" name="hiddenid" value="<?php echo $hiddenid;?>" />
												<input name="<?php echo $submitname;?>" type="submit" value="Add News Letters">
											 </td>
										 </tr>
									<?php
								}
							}
							 						
						?>
					</tbody>
				</table>
			</form>
		</td>
	</tr>
</table>

