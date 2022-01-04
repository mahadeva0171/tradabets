<?php
//only a post with paystack signature header gets our attention
if ((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST' ) || !array_key_exists('HMAC SHA512', $_SERVER) ) 
	exit();
// Retrieve the request's body
$input = @file_get_contents("php://input");
define('PAYSTACK_SECRET_KEY','sk_live_18bbde5880afaa893adabc8552711ceadca1cbf2');
// validate event do all at once to avoid timing attack
if($_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] !== hash_hmac('sha512', $input, PAYSTACK_SECRET_KEY))
    exit();

// parse event (which is json string) as object
// Do something - that will not take long - with $event
$event = json_decode($input);
$reference = $event->data->reference;
$status = $event->data->status;
switch ($event->event) {
	case 'transfer.success':
		$sql = "UPDATE transfer_initiated SET status = '$status' WHERE reference = '$reference'";
		$result = $con->query($sql);
		break;
	case 'transfer.failed':
		$sql = "UPDATE transfer_initiated SET status = '$status' WHERE reference = '$reference'";
		$result = $con->query($sql);
		break;
	default:
		echo 'Received unknown event type ' .$event->event;
		break;
}
http_response_code(200);
exit();

?>