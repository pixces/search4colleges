<?php 
	require_once("config.php");
	$theme = current_theme();
	isLogin();
	isallow();
	$message = "";
	
	if(isset($_GET) && isset($_GET['edit']))
	{
		$caption_txt	= "Edit";
		$edit_flag		= true;
		$edit_id		= required_param('edit', PARAM_INT);
		$edit_data		= get_record('role', 'id', $edit_id);

		$rolename			= $edit_data->rolename;
	}
	print_header(get_string('cmi','cmi'));
	cmi_include_head('css');
	cmi_include_head('js');
	cmi_include_editor();
	print_container_start();
?>
<script type="text/javascript">
		window.addEvent('domready', function(){
			new FormCheck('frm_addrole');
		});
		
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
		<td VALIGN="top" width="200">
			<?php load_menu(); ?>
		</td>
	</tr>
	<tr>
		<td>
			<div class="DotedHeader" width="70%">
				<?php if(isset($_GET['edit'])){ 
					echo "Update Role:";
				 }
					else echo "Add Role:"; 
				?>
			</div>
		    <br>
			<?php notify($message);?>
			<form method="post" name="frm_addrole" id="frm_addrole" action="role_manage.php" enctype="multipart/form-data">
				<table border="0" width="70%" align="center" cellspacing="5" cellpadding="5">
					 <tbody>
						 <tr>
							 <td class="Label" width="30%">Role Name:
							 </td>
							 <td ><input name="rolename" class="validate['required','alpha'] txtBox" type="text" size="25" value="<?php if(isset($rolename)) echo $rolename;?>" /></td>
							 <td >&nbsp;&nbsp;&nbsp;</td>
						 </tr>
						 <?php 
							if(isset($_GET['edit'])){
								$valuebutt = "btnUpdate.gif";
								$submitname = "updaterole";
								?>
								<input type="hidden" name="updaterole" value="updaterole" />
								<?php
								$hiddenid = $_GET['edit'];
							}
							else{ 
								$valuebutt = "btnSave.gif";
								$submitname = "addrole";
								?>
								<input type="hidden" name="addrole" value="addrole" />
								<?php
								$hiddenid = '';
							}
						?>
						 <tr>
							 <td colspan="2" style="text-align:center">
								<input type="hidden" name="hiddenid" value="<?php echo $hiddenid;?>" />
								 <input name="btnSave" id="btnSave" type="Submit" value="Save">
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