<?php 
/**
Pulse Lite Voting Script
http://s.technabled.com/PulseVote
**/
if(!$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'){
		die("No direct access to files is allowed");  
	}
include("Pulse.vote.class.php");
$item_id = $_POST['item_id'];
$action = $_POST['action'];
$format = urldecode($_POST['format']);
if(empty($item_id) || empty($action)) {
	$result['error'] = 'invalid_params';
}
$pulse = new Pulse();
if($action=='up'){
		if($pulse->votedBefore($item_id)){
			$result['error'] = 'already_voted';
		} elseif($pulse->voteUp($item_id)===true) {
			setcookie('pulse_item_'.$item_id, 1, time()+60*60*24*30, '/');
			$result['msg'] = $pulse->getFormatted($item_id, $format);
		} elseif($pulse->voteUp($item_id)===false){
			$result['error'] = 'database_error';
		}
} elseif($action=='down'){
		if($pulse->votedBefore($item_id)){ // voted before
			$result['error'] = 'already_voted';
		} elseif($pulse->voteDown($item_id)===true){ // voting done
			setcookie('pulse_item_'.$item_id, 1, time()+60*60*24*30, '/');
			$result['msg'] = $pulse->getFormatted($item_id, $format);
		} elseif($pulse->voteDown($item_id)===false){ // voting fails
			$result['error'] = 'database_erorr';
		}
} else { // passed voting direction is not up or down; possible attack
	$result['error'] = 'database_error';
}
echo json_encode($result);
?>