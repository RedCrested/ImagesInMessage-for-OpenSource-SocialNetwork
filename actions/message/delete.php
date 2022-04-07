<?php
/**
 * Open Source Social Network
 *
 * @package   Open Source Social Network
 * @author    Open Social Website Core Team <info@openteknik.com>
 * @copyright (C) OpenTeknik LLC
 * @license   Open Source Social Network License (OSSN LICENSE)  http://www.opensource-socialnetwork.org/licence
 * @link      https://www.opensource-socialnetwork.org/
 * 
 * @author    Rafael Amorim <amorim@rafaelamorim.com.br>
 * @copyright (C) Rafael Amorim
 * @link      https://www.rafaelamorim.com.br/
 */

header('Content-Type: application/json'); 
$id = input('id');
$type = input('type');
$message = ossn_get_message($id);

if(!$id){
	echo json_encode(array(
		'type' => $type,
		'status' => 0,
	));
	exit;
}
$userguid = ossn_loggedin_user()->guid;
if($type == 'all' && $message->message_from == $userguid){

	if (strpos($message->message, '[image=') !== false) {  //erase image file when user delete a message #4
		$filename =  ImagesInMessage_GetFileName($message->message);
		if (!ImagesInMessage_DeleteFile($filename)) {
			error_log("Error on delete file ".$filename);
		}
	}
	
	$message->message = ''; //delete message data
	$message->data->is_deleted = true;
	if($message->save()){
			echo json_encode(array(
				'type' => $type,
				'status' => true,
				'id' => $id,
			));
			exit;
	}
}
if($type == 'me' && $message->message_from == $userguid){
	$message->data->is_deleted_from = true;
	if($message->save()){
			echo json_encode(array(
				'type' => $type,
				'status' => true,
				'id' => $id,
			));
			exit;
	}
}
if($type == 'me' && $message->message_to == $userguid){
	$message->data->is_deleted_to = true;
	if($message->save()){
			echo json_encode(array(
				'type' => $type,
				'status' => true,
				'id' => $id,
			));
			exit;
	}
}

echo json_encode(array(
	'type' => $type,
	'status' => false,
	'id' => $id,
));
exit;