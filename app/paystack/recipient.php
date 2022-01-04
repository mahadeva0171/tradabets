<?php
if (!empty($_GET['name']) && !empty($_GET['account_number']) && !empty($_GET['bank_code'])) {
  $name = $_GET['name'];
  $account_number = $_GET['account_number'];
  $bank_code = $_GET['bank_code'];

   $url = "https://api.paystack.co/transferrecipient";
   $fields = [
   	 'type' => "nuban",
   	 'name' => $name,
   	 'account_number' => $account_number,
   	 'bank_code' => $bank_code,
   	 'currency' => 'NGN'
   ];
   $fields_string = http_build_query($fields);
   //open connection
   $ch = curl_init();

   //set the url, number of POST vars, POST data
   curl_setopt($ch,CURLOPT_URL,$url);
   curl_setopt($ch,CURLOPT_POST,$true);
   curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
   curl_setopt($ch,CURLOPT_HTTPHEADER,array(
      "Authorization: Bearer sk_live_18bbde5880afaa893adabc8552711ceadca1cbf2",
  	  "Cache-Control: no-cache",
   ));

   //So that curl_exec returns the contents of the cURL, rather than echoing it
   curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);

   //execute post
   $result = curl_execute($ch);
   //echo $result;
   //var_dump($result);

   $info = json_decode($result);
   $recipient_name = $info->data->name;
   $recipient_code = $info->data->recipient_code;
   $type = $info->data->type;
   $Acct_Numb = $info->data->account_number;
   $Bank_Code = $info->data->bank_code;
   $Bank_Name = $info->data->bank_name;
   $currency = $info->data->currency;
   $createdAt = $info->data->createdAt;
   if ($info->status) {
     include('database/mydb.php');
     $stmt = $con->prepare("INSERT INTO transfer_recipient (name, recipient_code, type, account_number, bank_code, bank_name, currency, createdAt) VALUES (?,?,?,?,?,?,?,?)");
     $stmt->bind_param("ssssssss", $recipient_name, $recipient_code, $type, $Acct_Numb, $Bank_Code, $Bank_Name, $currency, $createdAt);
     $stmt->execute();
     if (!$stmt) {
       // code...
      echo 'There was an error'.mysqli_error($con);
     }
     else {
      header('Location: initiate.php?recipient_code='.$recipient_code);
      exit();
     }

   }

}
else {
  header("Location: error.html");
 exit();
}
?>