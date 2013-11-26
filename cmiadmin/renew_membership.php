<?php 
	require_once("config.php");	
	$theme = current_theme();	
	
	$message = $date = $time ="";
	$master_table_name = 'schools';

	$id = optional_param('id','', PARAM_RAW);

	$page_name =  substr(str_replace(array(str_replace('','',$CFG->dirroot),''),'',($_SERVER['SCRIPT_FILENAME'])),'1');
	$page_manage = str_replace('form','manage',$page_name);
	$page_title = ucwords(str_replace('_',' ',str_replace('form.php','',$page_name)));
		
	if($_POST){
			
			$amount			= optional_param('amount', '', PARAM_RAW);
			$discount		= optional_param('discount', 0, PARAM_RAW);	
			$membership_id	= optional_param('membership_id', '', PARAM_RAW);	
			$total_amount	= optional_param('total_amount', '', PARAM_RAW);
			$validity		= optional_param('validity', '', PARAM_RAW);
			$id				= optional_param('id',PARAM_RAW);

			$add_membership				                = new object();
			$add_membership->school_id		            = $id;
			$add_membership->school_memberShip_typeid	= $membership_id;
			$add_membership->registeredon		        = time();
			
			$expiry_date = time();
			if(isset($validity))
				$expiry_date = mktime(0, 0, 0, date("m")+$validity, date("d"), date("y"));
			else
				$expiry_date = '';

			$add_membership->expiryon		= $expiry_date;
			$add_membership->renewedon		= time();
			$add_membership->discount		= $discount;

            #update school as featured
            //update school as featured
            $update_school = new object();
            $update_school->id = $id;
            $update_school->featured = 'featured';

            # TODO:
            # Update membership logic is all messed up
            # this logic should also be fixed
            # no change happens on renewal of membership

            $sql = "select max(history_count) as history from {$CFG->prefix}school_membership where school_id=".$id;
			$data_membership =  get_record_sql($sql);

			$sql_status = "select * from {$CFG->prefix}school_membership where school_id=".$_POST['id'];
			$data_membership_status =  get_records_sql($sql_status);

			if(!empty($data_membership_status)){
				foreach($data_membership_status as $datastatus){
					$update_membership			= new object();	
					$update_membership->id		= $datastatus->id;
					$update_membership->status	= 'inactive';
					
					if($update_membership){
						update_record('school_membership',$update_membership);
					}
				}
			}

			if(isset($data_membership->history) && $data_membership->history != 'null'){
				$history_count = $data_membership->history+1;
			}
			else{
				$history_count = 1;
			}

			$add_membership->history_count	= $history_count;
			$add_membership->status			= 'active';
			$add_membership->added_date 	= time();
			$add_membership->total_amount 	= $total_amount;



			if(!empty($add_membership)){

				$membership_insert =  insert_record('school_membership',$add_membership);
                if($membership_insert){
                    #update the school details to set it as featured
                    if ($update_school) {
                        update_record('schools', $update_school);
                    } ?>
					<script  type="text/javascript">
					parent.window.location = '<?php echo "renew_membership_manage.php"; ?>';
					parent.Mediabox.close();			
				</script>
				<?php }
			}


	}
	if(isset($id))
	{	
		$student_data = get_records_sql("Select * from ".$CFG->prefix."schools sc inner join schools_additional sa on(sc.id = sa.school_id) where sc.id = $id");

		$student_membership_data = get_records_sql("Select * from ".$CFG->prefix."school_membership sm inner join school_member_ship_types smt on (sm.school_memberShip_typeid = smt.id) where sm.school_id = $id");
		//print_object($student_membership_data);
		
		$added_date 			= time();
		$allowed_sections		= "";

		$add_mode				= new object();
			
		
		if(!empty($add_mode->answer))
		{
			if($new = insert_record($master_table_name, $add_mode))
			{
				cmi_add_to_log('FAQ','Insert','New faq '.$answer.' added');
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
		new FormCheck('frm_add_mode');
		get_datepicker_without_toggler();
	});

	function insert_value(val,id){

		var myString = val;

		var result = myString.split("##");
		
		$('amount').value = '';
		$('total_amount').value = '';
		$('discount').value = '';

		if(result[2]){

			if(result[2]!= ''){
				$('amount').value = result[1];
				$('discount').value = result[2];
				$('total_amount').value = result[1]- result[2];
			} 
			else{
				$('amount').value = result[1];
				$('total_amount').value = result[1];
				$('discount').value = 0;
			}
		}
		if(result[3]){
			$('validity').value = result[3];
		}
		if(result[0]){
			$('membership_id').value = result[0];
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
						echo "Update Renew Membership :";
					}
					else 
						echo "Add/Renew Membership:"; 
					?>
				</div>
				<br>

				<?php notify($message);?>
				<form method="post" name="frm_add_mode" id="frm_add_mode" action="<?php echo $page_name; ?>" enctype="multipart/form-data">

				<input name="validity" value="" maxlength="10" id="validity" type="hidden" />
				<input name="id" value="<?php echo $id;?>" maxlength="10" id="validity" type="hidden" />
				<input name="membership_id" value="" maxlength="10" id="membership_id" type="hidden" />

				<table border="0" width="100%" align="center" cellspacing="1" cellpadding="1">
						<tbody>
							<tr> 
							  <td width="5%"> </td>
							  <td align="center" width="90%" colspan="2"> </td>
							  <td width="5%"> </td>
							</tr>
							<tr> 
							  <td width="5%"> </td>
							  <td class="DotedHeader" width="90%" colspan="2">Membership History:</td>
							  <td width="5%"> </td>
							</tr>
							<tr> 
							  <td width="70%" colspan="5" >
							 <?php
								if(!empty($student_membership_data)){ ?>
									<table width="100%" border="1" style="text-align:center">
										<tr>
											<th>Membership name</th>
											<th>Registerd on</th>
											<th>Expiry Date</th>
											<th>Per Moth Cost</th>
											<th>Total Amount($)</th>
											<th>Status</th>
										</tr>
									<?php 
									foreach($student_membership_data as $record){ ?>
										<tr>
											<td><?php echo $record->title;?></td>
											<td><?php echo date('d / m/ Y',$record->registeredon); ?></td>
											<td><?php echo date('d / m/ Y',$record->expiryon); ?></td>
											<td><?php echo $record->amount; ?></td>
											<td><?php echo $record->total_amount; ?></td>
											<td><?php echo $record->status; ?></td>
										</tr>	
									<?php }
									echo "</table>";
								}

							 ?>
							  </td>
							  <td width="5%"> </td>
							</tr>
							<tr> 
							  <td width="5%"> </td>
							  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
							  <td width="5%"> </td>
							</tr>
							<tr> 
							  <td width="5%"> </td>
							  <td class="Label" width="20%" valign="top"> Select Membership : &nbsp;</td>
							<td width="70%" > 
							
							<?php echo get_membership($id);?>
							
							</td>
							  <td width="70%">
							</td>
							  <td width="5%"> </td>
							</tr>
							<tr> 
							  <td width="5%"> </td>
							  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
							  <td width="5%"> </td>
							</tr>
							<tr> 
							  <td width="5%"> </td>
							  <td class="Label" width="20%">Amount : &nbsp;</td>
							  <td width="70%"><input name="amount" value="" maxlength="10" id="amount" style="width: 250px;" type="text" class="validate['required','digit'] txtBox" /></td>
							  <td width="5%"> </td>
							</tr>
							<tr> 
							  <td width="5%"> </td>
							  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
							  <td width="5%"> </td>
							</tr>
							<tr> 
							  <td width="5%"> </td>
							  <td class="Label" width="20%">Discount : &nbsp;</td>
							  <td width="70%"><input name="discount" value="" maxlength="10" id="discount" style="width: 250px;" type="text" class="validate['required','digit'] txtBox" /></td>
							  <td width="5%"> </td>
							</tr>
							<tr> 
							  <td width="5%"> </td>
							  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
							  <td width="5%"> </td>
							</tr>
							<tr> 
							  <td width="5%"> </td>
							  <td class="Label" width="20%">Total Amount : &nbsp;</td>
							  <td width="70%"><input name="total_amount" value="" maxlength="10" id="total_amount" style="width: 250px;" type="text" class="validate['required','digit'] txtBox" /></td>
							  <td width="5%"> </td>
							</tr>
							<tr> 
							  <td width="5%"> </td>
							  <td width="90%" colspan="2"> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
							  <td width="5%"> </td>
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
									<input name="<?php echo $submitname;?>" type="submit" value="Save">
								</td>
							</tr>
						</tbody>
					</table>
				</form>
			</td>
		</tr>
	</table>
