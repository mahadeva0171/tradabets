<?php
include("database/mydb.php");
if (isset($POST['verify'])) {
	$AcctNumb = mysqli_real_escape_string($con, $_POST['account_number']);
	$BankCode = mysqli_real_escape_string($con, $_POST['bank_code']);



  $sql = mysqli_query($con, "SELECT * FROM transfer_recipient WHERE account_number = '$AcctNumb'") or die(mysqli_error());

  if ($sql->num_rows > 0) {
  	$data = mysqli_fetch_array($sql);
  	$recipient_code = $data['recipient_code'];
  	$name = $data['name'];
  	echo "<script> alert('Account Number is already verified with correct bank code generating a recipient code, click on INITIATE button to transfer fund');</script>";
  }
  else {
  	$curl = curl_init();
  	  curl_setopt_array($curl, array(
  	  CURLOPT_URL => "https://api.paystack.co/bank/resolve?account_number=".rawurlencode($AcctNumb)."$bank_code=".rawurlencode($BankCode),
  	  CURLOPT_RETURNTRANSFER => true,
  	  CURLOPT_ENCODING => "",
  	  CURLOPT_MAXREDIRS => 10,
  	  CURLOPT_TIMEOUT => 30,
  	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  	  CURLOPT_CUSTOMREQUEST => "GET",
  	  CURLOPT_HTTPHEADER => array(
  	  "Authorization: Bearer sk_live_18bbde5880afaa893adabc8552711ceadca1cbf2",
  	  "Cache-Control: no-cache",
  	  ),
  	));

  	$response = curl_exec($curl);
  	$err = curl_error($curl);
  	curl_close($curl);
  	if ($err) {
  		echo "cURL Error #:" .$err;
  	} else {
  		// echo $response;
  		$result = json_decode($response);
  		$verify = $result->status;
  	}

  		if ($verify) {
  			# code...
  			$name = $result->data->account_name;
  			echo "<script> alert('Account Number is verified with Bank Code');</script>";
  			header('Location: recipient.php?name='.$name.'&account_number='.$AcctNumb.'&bank_code='.$BankCode);
  			exit();
  		}
  		else {
  			echo "<script> alert('Invalid Account Number or Bank Code, it is NOT resolved/verified');</script>";
  		}
  }
}
?>

<!-- inputs for ACCOUNT NUMBER, BANK CODE & Verify Button here -->

<?php

	if (!empty($_POST['account_number']) && !empty($recipient_code)) {
		echo "<label> Recipient Code: ".$recipient_code." </label>
		<a href='initiate.php?recipient_code=".$recipient_code."'>
		<button class='btn btn-md btn-success'>INITIATE</button></a>";
	}
?>
	