<?php 	
require_once("config.php");
require("lib/phpmailer/class.phpmailer.php");
$theme = current_theme();

isLogin();
isallow();

$caption_txt= "Send";

print_header(get_string('cmi','cmi'));

cmi_include_head('css');
cmi_include_head('js');
?>


<script type="text/javascript">
window.addEvent('domready', function()
{
new FormCheck('frm_email');
});
</script>


<?php
cmi_include_editor();
print_container_start();
?>

<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
<td VALIGN="top" width="200">
<?php load_menu(); ?>
</td>
</tr>
<tr>
<td>
<form method="post" name="frm_email" id="frm_email" action="" class="long">
<table width="100%" border="0">
<tbody>
<tr> 
<td width="5%"> </td>
<td align="center" width="90%" colspan="2"> </td>
<td width="5%"> </td>
</tr>
<tr> 
<td width="5%"> </td>
<td class="DotedHeader" width="90%" colspan="2"> <?php echo $caption_txt;?> Email:</td>
<td width="5%"> </td>
</tr>
<tr> 
<td style="height: 21px;" width="5%"> </td>
<td style="height: 21px;" width="90%" colspan="2"> </td>
<td style="height: 21px;" width="5%"> </td>
</tr>
<tr> 
<td width="5%"> </td>
<td class="Label" width="20%"> To: &nbsp;</td>
<td width="70%">
<textarea name="txtTo" cols="50" class="validate['required'] txtBox"></textarea>		
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
<td class="Label" width="20%"> Subject : &nbsp;</td>
<td width="70%">
<input name="txtSubject" maxlength="255" id="txtSubject" style="width: 300px;" type="text"  class="validate['required'] txtBox" />
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
<td class="Label" width="20%"> Message : &nbsp;</td>
<td width="70%">
</td>
<td width="5%"> </td>
</tr>
<tr> 
<td width="5%"> </td>
<td colspan="2"><br>
<textarea name="txtMessage" id="editor1"></textarea>		
<script type="text/javascript">
init_ckfinder('editor1');
</script>
</td>
</tr>
<tr> 
<td width="5%"> </td>
<td width="90%" colspan="2"><br><br>
<input name="btnSave" id="btnSave" type="submit" value="Send">
</td>
<td width="5%"> </td>
</tr>
<tr> 
<td width="5%"> </td>
<td width="90%" colspan="2">
<br>

<?php 
if(isset($_POST['btnSave']))
{
$txtTo  	= required_param('txtTo', PARAM_RAW);
$txtSubject	= required_param('txtSubject', PARAM_RAW);
$txtMessage	= required_param('txtMessage', PARAM_RAW);

require_once 'lib/phpmailer/class.phpmailer.php';

$mail = new PHPMailer();

$mail->From = 'contact@search4colleges.com';
$mail->FromName = 'Search4Colleges.com';

$to=explode(",","$txtTo");

$count=count($to);
for($i=0;$i<$count;$i++)
{
$mail->AddAddress($to[$i],"sudheer");

$mail->Subject = $txtSubject;
$mail->Body    = $txtMessage;

if($mail->Send())
{
echo "Message sent to emial id ".$to[$i]."<br>";
cmi_add_to_log('email','delivery','email send to '.$to[$i]);
}
else
{
echo "Message not sent to email id ".$to[$i]."<br>";
cmi_add_to_log('email','delivery','email not send to '.$to[$i]);
} 

}

} 

?>
</td>
<td width="5%"> </td>
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