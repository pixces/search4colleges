<?php
function genRandomString($length = 10) {
    $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
    $string ='' ;
    for ($p = 0; $p < $length; $p++) {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }
 
    return $string;
}
?>