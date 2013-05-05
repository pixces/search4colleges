<?php
require_once('cmiadmin/config.php'); 
$message = '';
$expired = 0;
if(isset($_SESSION['user_type'])){
	if(strtolower($_SESSION['user_type']) == strtolower('school')) {
		$userData = get_record_sql('SELECT added_date FROM fe_users WHERE id ='.$_SESSION['s4c_user_id'].' AND status="active"');
		$login30Day = strtotime('+30days', $userData->added_date);
		$currentDate = time();
		$membership = get_record_sql('SELECT id,expiryon,registeredon FROM school_membership WHERE school_id ='.$_SESSION['s4c_user_id'].' AND status="active" ORDER BY added_date DESC ');
		if(empty($membership)){
			$expireDate = round(($login30Day - $currentDate)/ (60*60*24));
			if($currentDate > $login30Day){
				$message = '30 days trial period expired.<br /> Please <a href="membership_buy.html"><b>Renew Membership</b></a>';
				$expired = 1;
			}
			else {
				$message = '30 day trial period will expire with in '.$expireDate.' days. <br />Please <a href="membership_buy.html"><b>Buy Membership</b></a>.';
			}
		}
		else {
			$last30days = strtotime('-30days', $membership->expiryon);			
			$expireDate = round(($membership->expiryon - $currentDate)/ (60*60*24));
			if(($currentDate >= $last30days)){
				$message = 'Membership expire within '.$expireDate.' days. <br />Please <a href="membership_buy.html"><b>Buy</b></a> membership.';
			}
			if($currentDate > $membership->expiryon){
				$message = 'Membership expired. Please <a href="membership_buy.html"><b>Renew Membership</b></a>';
				$expired = 1;
					$update_membership			= new object();	
					$update_membership->id		= $membership->id;
					$update_membership->status	= 'inactive';
					
					if($update_membership){
						update_record('school_membership',$update_membership);
					}	
				//print_r($membership);
			}
		}
	}
}

?>