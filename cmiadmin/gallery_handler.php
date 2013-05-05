<?php
require_once('config.php');

$result = array();

$result['time'] = date('r');
$result['addr'] = substr_replace(gethostbyaddr($_SERVER['REMOTE_ADDR']), '******', 0, 6);
$result['agent'] = $_SERVER['HTTP_USER_AGENT'];

if (count($_GET)) {
	$result['get'] = $_GET;
}
if (count($_POST)) {
	$result['post'] = $_POST;
}
if (count($_FILES)) {
	$result['files'] = $_FILES;
}

// we kill an old file to keep the size small
/*if (file_exists('script.log') && filesize('script.log') > 102400) {
	unlink('script.log');
}*/

/*$log = @fopen('script.log', 'a');
if ($log) {
	fputs($log, print_r($result, true) . "\n---\n");
	fclose($log);
}*/


// Validation

$error = false;

if (!isset($_FILES['Filedata']) || !is_uploaded_file($_FILES['Filedata']['tmp_name'])) {
	$error = 'Invalid Upload';
}

/**
 * You would add more validation, checking image type or user rights.
 *

if (!$error && $_FILES['Filedata']['size'] > 2 * 1024 * 1024)
{
	$error = 'Please upload only files smaller than 2Mb!';
}

if (!$error && !($size = @getimagesize($_FILES['Filedata']['tmp_name']) ) )
{
	$error = 'Please upload only images, no other files are supported.';
}

if (!$error && !in_array($size[2], array(1, 2, 3, 7, 8) ) )
{
	$error = 'Please upload only images of type JPEG, GIF or PNG.';
}

if (!$error && ($size[0] < 25) || ($size[1] < 25))
{
	$error = 'Please upload an image bigger than 25px.';
}
*/


// Processing

/**
 * Its a demo, you would move or process the file like:
 *
 * move_uploaded_file($_FILES['Filedata']['tmp_name'], '../uploads/' . $_FILES['Filedata']['name']);
 * $return['src'] = '/uploads/' . $_FILES['Filedata']['name'];
 *
 * or
 *
 * $return['link'] = YourImageLibrary::createThumbnail($_FILES['Filedata']['tmp_name']);
 *
 */

if ($error) {

	$return = array(
		'status' => '0',
		'error' => $error
	);

} else {

	$return = array(
		'status' => '1',
		'name' => $_FILES['Filedata']['name']
	);

	// Our processing, we get a hash value from the file
	$return['hash'] = md5_file($_FILES['Filedata']['tmp_name']);

	// ... and if available, we get image data
	$info = getimagesize($_FILES['Filedata']['tmp_name']);
	$target_path = '../uploads/gallery/';
	if(!file_exists($target_path)){
		mkdir($target_path,0777);
	}
	$originalfilename = basename($_FILES['Filedata']['name']);
	$image = upload_image('Filedata',$target_path);


	//insert into db
	//return the id of the record

	/*$log = @fopen('script.log', 'a');
	if ($log) {
		fputs($log, print_r($_POST, true) . "\n---\n");
		fclose($log);
	}*/
		
	//$return['file_id'] = substr(md5("$destinationfile"),0,5);
	$return['name'] = $image;

	if ($info) {
		$return['width'] = $info[0];
		$return['height'] = $info[1];
		$return['mime'] = $info['mime'];
	}

}


// Output

/**
 * Again, a demo case. We can switch here, for different showcases
 * between different formats. You can also return plain data, like an URL
 * or whatever you want.
 *
 * The Content-type headers are uncommented, since Flash doesn't care for them
 * anyway. This way also the IFrame-based uploader sees the content.
 */

if (isset($_REQUEST['response']) && $_REQUEST['response'] == 'xml') {
	// header('Content-type: text/xml');

	// Really dirty, use DOM and CDATA section!
	echo '<response>';
	foreach ($return as $key => $value) {
		echo "<$key><![CDATA[$value]]></$key>";
	}
	echo '</response>';
} else {
	// header('Content-type: application/json');

	echo json_encode($return);
}

?>