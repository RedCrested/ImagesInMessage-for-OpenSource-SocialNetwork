<?php
/**
 * Open Source Social Network
 *
 * @package   ImagesInMessage
 * @author    Rafael Amorim <amorim@rafaelamorim.com.br>
 * @copyright (C) Rafael Amorim
 * @license   OSSNv4  http://www.opensource-socialnetwork.org/licence/
 * @link      https://www.rafaelamorim.com.br/
 */

$send = new ImagesInMessage;
$message = input('message');
$image = input('image-attachment');

if ($image){
    $image = " [image=".$image."]";
    $message = $message . $image;
}

if(trim(ossn_restore_new_lines($message)) == ''){
	echo 0;
	exit;
}
$to = input('to');
//[E] Check user existence before sending message #1883
if(!ossn_user_by_guid($to)) {
    echo 0;
    exit;
}
if ($message_id = $send->send(ossn_loggedin_user()->guid, $to, $message)) {
	$user = ossn_user_by_guid(ossn_loggedin_user()->guid);
	
	$instance = ossn_get_message($message_id);
	$message = $instance->message;
	
	$params['message_id'] = $message_id;
	$params['user'] = $user;
	$params['message'] = $message;
	$params['instance'] = $instance;
	$params['view_type'] = 'actions/send';
	echo ossn_plugin_view('messages/templates/message-send', $params);
} else {
	echo 0;
}
echo "<script>$('#message-send-".$to."').find('.image-data').html('');</script>";
echo "<script>$('#message-send-".$to."').find('[name=image-attachment]').val('');</script>";
//force scrolldown again
echo "<script>$('#message-append-".$to."').animate({ scrollTop: $('#message-append-".$to."')[0].scrollHeight+500}, 1000);</script>";

//messages only at some points #470
// don't mess with system ajax requests
exit;
