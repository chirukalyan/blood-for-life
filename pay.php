<?php
// Replace these with your actual PhonePe API credentials
 
$merchantId = 'PGTESTPAYUAT86'; // sandbox or test merchantId
// $apiKey="099eb0cd-02cf-4e2a-8aca-3e6c6aff0399";
$apiKey = "96434309-7796-489d-8924-ab56988a6076";
$redirectUrl = 'http://localhost/finalproject/welcome.php';
 
// Set transaction details
$order_id = uniqid(); 
$name=$_POST['name'];
$email=$_POST['email'];
$mobile=$_POST['phone'];
$amount = $_POST['amount']; // amount in INR
$description = 'Blood Donation Camp';
 
 
$paymentData = array(
    'merchantId' => $merchantId,
    'merchantTransactionId' => "MT7850590068188104", // test transactionID
    "merchantUserId"=>"MUID123",
    'amount' => $amount*100,
    'redirectUrl'=>$redirectUrl,
    'redirectMode'=>"REDIRECT",
    'callbackUrl'=>$redirectUrl,
    "merchantOrderId"=>$order_id,
   "mobileNumber"=>$mobile,
   "message"=>$description,
   "email"=>$email,
   "shortName"=>$name,
   "paymentInstrument"=> array(    
    "type"=> "PAY_PAGE",
  )
);
 
 
 $jsonencode = json_encode($paymentData);
 $payloadMain = base64_encode($jsonencode);
 $salt_index = 1; //key index 1
 $payload = $payloadMain . "/pg/v1/pay" . $apiKey;
 $sha256 = hash("sha256", $payload);
 $final_x_header = $sha256 . '###' . $salt_index;
 $request = json_encode(array('request'=>$payloadMain));
                
$curl = curl_init();
curl_setopt_array($curl, [
  CURLOPT_URL => "https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay",
// https://api-preprod.phonepe.com/apis/pg-sandbox
//   CURLOPT_URL => "https://api-preprod.phonepe.com/apis/pg-sandbox",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
   CURLOPT_POSTFIELDS => $request,
  CURLOPT_HTTPHEADER => [
    "Content-Type: application/json",
     "X-VERIFY: " . $final_x_header,
     "accept: application/json"
  ],
]);
 
$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);
 
if ($err) {
  echo "cURL Error #:" . $err;
} else {
   $res = json_decode($response);
    echo "<pre>";
    print_r($res);
    echo "</pre>";
if(isset($res->success) && $res->success=='1'){
$paymentCode=$res->code;
$paymentMsg=$res->message;
$payUrl=$res->data->instrumentResponse->redirectInfo->url;

//  echo "";
header('Location:'.$payUrl) ;
}
}
          
?>