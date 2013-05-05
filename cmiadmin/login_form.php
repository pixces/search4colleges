			<script type="text/javascript">
				window.addEvent('domready', function(){
					new FormCheck('frmLogin');
				});
			</script>
			<div id="wrapper">
				<div id="header">					
     			</div>
			</div>
			<div id="ctr">
				<div class="login">
					<div class="login-form">
					<br /><br />
					<form id="frmLogin" name="frmLogin" method="post" action="<?php echo $CFG->wwwroot;?>/login_process.php">
						<table align="center" border="0" width="50%">
							<tr>
								<td>
									<div><?php echo get_string('login_content','cmi'); ?></div>
									<div>
										<img alt="security" src="theme/<?php echo $theme; ?>/images/adminusers.png" width="64" height="64" />
									</div>
								</td>
								<td>
									<div>
										
									</div>
									<div class="form-block">
										<div class="inputlabel">Username</div>
										<div>
											<input type="text" class="validate['required'] textbox" id="txtUsername" maxlength="20" name="txtUsername"/>
										</div>
										<div class="inputlabel">Password<br/>
											<input type="password" class="validate['required'] textbox" id="txtPass" maxlength="20" name="txtPass"/>
											<br/><br/>
										</div>
										<div>
											<input type="submit" alt="Submit button" id="btnLogin" value="Login" name="btnLogin"/>
										</div>
									</div>
								</td>
							</tr>
						</table>
						</form>
						<br /><br />
					</div>
					
				</div>
			</div> 