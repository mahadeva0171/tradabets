<?php

include('database/mydb.php');
  if (empty($_GET['recipient_code'])) {
  	header("location:javascript://history.go(-1)");
  }
   $stored_recipient_code = '';
   if (isset($_POST['transfer'])) {
   	  $recipient_code = $_GET['recipient_code'];
   	  $reason = mysqli_real_escape_string($con, $_POST['reason']);
   	  $amount = mysqli_real_escape_string($con, $_POST['amount']);
   	 $sql = mysqli_query($con, "SELECT * FROM transfer_recipient WHERE recipient_code = '$recipient_code'") or die(mysqli_error());

   	 if ($sql->num_rows > 0) {
   	 	$data = mysqli_fetch_array($sql);
   	 	$stored_recipient_code = $data['recipient_code'];
   	 }
   	 if ($recipient_code !== $stored_recipient_code) {
   	 	echo "<script>alert('Recipient Code stored does not match with the code received');</script>";
   	 }
   	 else {

   	 	  $url = "https://api.paystack.co/transfer";
		  $fields = [
		    'source' => "balance",
		    'amount' => $amount * 100,
		    'recipient' => $recipient_code,
		    'reason' => "$reason"
		  ];
		  $fields_string = http_build_query($fields);
		  //open connection
		  $ch = curl_init();
		  
		  //set the url, number of POST vars, POST data
		  curl_setopt($ch,CURLOPT_URL, $url);
		  curl_setopt($ch,CURLOPT_POST, true);
		  curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		    "Authorization: Bearer SECRET_KEY",
		    "Cache-Control: no-cache",
		  ));
		  
		  //So that curl_exec returns the contents of the cURL; rather than echoing it
		  curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
		  
		  //execute post
		  $result = curl_exec($ch);
		  // echo $result;

		  $initiate = json_decode($result);
		   $status = $initiate->status;
		  $message = $initiate->data->status;
		  $reference = $initiate->data->reference;
		  $amount = $initiate->data->amount;
		  $reason = $initiate->data->reason;
		  $transfer_code = $initiate->data->transfer_code;
		  $createdAt = $initiate->data->createdAt;

		  if ($status == true && $message == "success") {
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
		 else {
		 	if ($message == "otp") {
		 		header('Location: finalize_transfer.php?transfer_code='.$transfer_code.'&recipient_code='.$recipient_code);
		 		exit();
		 	}
		 	else {
		 		echo "<script> alert('Error: Transfer could not be initiated, Contact the developer'); </script>";
		 	}
		 }
   	}
  }
?>


<body>
	<h1> Initiate transfer to this account name:</h1>
	  <span>
	    <?php
	    if (empty($account_name)) {

	    } else
	    echo $account_name;
	    ?>
	  </span>
	</h1>
	<br>
	<div class="container">
		<div class="row">
			<form method="post" action="">

			<label>Reason</label>
			 <input type="text" class="form-control" name="reason" value="" required/><br>
			<label>Amount</label>
			 <input type="number" class="form-control" name="amount" value="" required/><br>

			<input type="submit" name="transfer" value="TRANSFER" class="btn admin-reg-btn btn-block" />

			</form>
		</div>

		<div>
			<label>Verify an Account</label>
				<a href="verify.php">VERIFY</a>
		</div>
	</div>
</body>

