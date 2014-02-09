<?php
/**
 * Created by IntelliJ IDEA.
 * User: zainulabdeen
 * Date: 07/12/13
 * Time: 1:31 PM
 * To change this template use File | Settings | File Templates.
 */

/*
$dbhost = 'localhost';
$dbname = 'search4c';
$dbuser = 'root';
$dbpass = '';
*/

$dbhost = 'localhost';
$dbname = 'searchg1_search4c';
$dbuser = 'searchg1_search4';
$dbpass = 'fN#B;O;EUaSg';


//require_once("config.php");
$master_table = 'gallery';

$conn = mysql_connect($dbhost,$dbuser,$dbpass);
if ($conn){
    mysql_selectdb($dbname);
}

//threshold time
$interval = time() - (24 * 60 * 60);    //all where date_added is on or before 24 hrs from now


$approval_status = 'inprocess';
$item_status = 'active';

//get the id's of all unapproved image
$sQl = "SELECT `id` FROM ".$master_table." WHERE `approved` = '".$approval_status."' AND `status` = '".$item_status."' AND `added_date` >= ".$interval;
$galleryItems = mysql_query($sQl,$conn);

$idList = array();
if($galleryItems){
    $count = 0;
    while ($row = mysql_fetch_assoc($galleryItems)) {
       $idList[] = $row['id'];
    }
}

if (!$idList){
     echo "No results found to update";
     exit;
}

//update all these ids with approval status = 'approved';
$sQl = " UPDATE $master_table SET `approved` = 'approved' WHERE `id` IN  ( ".implode(',',$idList)." ) ";

//record the list of ids and status in the log

if ( mysql_query($sQl) ){
    echo "All images / videos approved successfully";
} else {
    echo "Cannot auto approve gallery items";
}