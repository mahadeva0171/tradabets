<?php
include('database/mydb.php');
  if (empty($_GET['transfer_code'])) {
  	// code...
  	header("location:javascript://history.go(-1)");
  }
  if (isset($_POST['transfer'])) {
	  $recipient_code = $_GET['recipient_code'];
	  $transfer_code = $_GET['transfer_code'];
	  $otp = mysqli_real_escape_string($con, $_POST['otp']);

	  $url = "https://api.paystack.co/transfer/finalize_transfer";
	  $fields = [
	  	"transfer_code" => $transfer_code;
	  	"otp" => $otp;
	  ];
	  $fields_string = http_build_query($fields);

  //open connection
  $ch = curl_init();
  
  //set the url, number of POST vars, POST data
  curl_setopt($ch,CURLOPT_URL, $url);
  curl_setopt($ch,CURLOPT_POST, true);
  curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer sk_live_18bbde5880afaa893adabc8552711ceadca1cbf2",
    "Cache-Control: no-cache",
  ));
  
  //So that curl_exec returns the contents of the cURL; rather than echoing it
  curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
  
  //execute post
  $result = curl_exec($ch);
  // echo $result;
  // var_dump($result);

  $finalize = json_decode($result);
  $status = $finalize->status;
  $message = $finalize->data->status;
  $reference = $finalize->data->reference;
  $amount = $finalize->data->amount;
  $reason = $finalize->data->reason;
  $transfer_code = $finalize->data->transfer_code;
  $createdAt = $finalize->data->createdAt;

  if ($status) {
  	$sql = "INSERT INTO transfer_initiated (reference, amount_in_cobo, reason, status, transfer_code, createdAt) VALUES ('$reference','$amount','$reason','$message','$transfer_code','$createdAt')";
  	$result = $con->query($sql);
  	if (!result) {
  		// code...
  		echo "<script> alert('Error: Transfer initiated details could not be stored in the database'); </script>";
  	}
  	else {
  		header("Location: success.html");
  		exit();
  	}
  }
  else
  	echo "<script> alert('Error: Transfer could not be finalized, contact the developer'); </script>";

 }
?>

<?php
$recipient_code = $_GET['$recipient_code'];
$sql2 = mysqli_query($con, "SELECT * FROM transfer_recipient WHERE recipient_code = '$recipient_code'") or die(mysqli_error());
	if ($sql->num_rows > 0) {
		// code...
	   $data2 = mysqli_fetch_array($sql2);
	   $account_name = $data2['name'];
	}
?>

<body>
	<h1>Finalize a Transfer to this acccount name:
		<span>
			<?php
			if (empty($account_name)) {
				// code...
			} else
			echo $account_name;
			?>
		</span>
	</h1>
	<br>
	
	<div class="container">
		<div class="row">

			<form method="post" action="">
				<label>Amount</label>
				<input type="number" class="form-control" name="otp" value="" placeholder="enter otp" required/><br>

			<input type="submit" name="transfer" value="FINALIZE" class="btn admin-reg-btn btn-block" />

			</form>
			<div>
				<label>Verify an Account</label>
				<a href="verify.php">VERIFY</a>
			</div>
		</div>
	</div>
</body>











