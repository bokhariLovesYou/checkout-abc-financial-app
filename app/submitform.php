<?php

// creating a php session.
  session_start();
// getting the transaction ID, (this was needed so that we could redirect the user to the error or the success page). However, please note: we need to validate the form more in depth, -- The form fields need to be validated further on so that phone numbers can only have phone numbers, addresses can have only addresses, and zipcode as well as stats can have only them vice versa -- so that the response of ABC's servers is presented to the user on a fail submission.
$transactionID = $_POST['transactionID'];

// check if a form was submitted
if( !empty( $_POST ) ){

// convert form data to json format
    $postArray = array(
      "paymentPlanId" => $_POST['paymentPlanID'],
      "planValidationHash" => $_POST['planValidationHash'],
      "salesPersonId" => '',
      "campaignId" => '',
      "macAddress" => '',
      "activePresale" => 'true',
      "sendAgreementEmail" => 'true',
      "agreementContactInfo" => array(
          "firstName" => $_POST['firstName'],
          "middleInitial" => $_POST['middleName'],
          "lastName" => $_POST['lastName'],
          "homePhone" => $_POST['homePhone'],
          "cellPhone" => $_POST['mobilePhone'],
          "workPhone" => $_POST['workPhone'],
          "workExt" => $_POST['extension'],
          "email" => $_POST['email'],
          "gender" => $_POST['gender'],
          "birthday" => $_POST['dob'],
          "driversLicense" => $_POST['license'],
          "employer" => $_POST['employer'],
          "wellnessPorgramId" => '',
          "barcode" => '',
              "agreementAddressInfo" => array(
                  "addressLine1" => $_POST['mailingAddress'],
                  "addressLine2" => '',
                  "city" => $_POST['city'],
                  "state" => 'IL',
                  "zipCode" => $_POST['postalCode'],
                  "country" => 'US',
              ),
             "emergencyContact" => array(
                  "ecFirstName" => '',
                  "ecLastName" => '',
                  "ecPhone" => '',
                  "ecPhoneExtension" => '',
                 ),
           ),
            "thirdPartyPaymentMethod" => '',
               "payPageBillingInfo" => array(
                  "payPageDraftCreditCard" => array(
                      "draftCreditCardTransactionId" => $_POST['transactionID'],
                ),
               "payPageDueTodayCreditCard" => array(
                  "todayCreditCardTransactionId" => $_POST['transactionID'],
               ),
            ),
    );

// encode the format
$jsonSubmitData = json_encode( $postArray );
// create a new file called entries.json in the directory
$fileSubmitData = 'entries.json';
// save the submitted data in a session to call later on
$_SESSION['jsonData'] = $jsonSubmitData;
// putting these entries in the newly created entries.json file
file_put_contents( $fileSubmitData, $jsonSubmitData, FILE_APPEND);
} 

// if we do not have the transaction ID 
if(!isset($transactionID) || trim($transactionID) == '')
{
  // redirect to the error page
   header("Location: error.php");  
} else {
  // redirect to the success page
  header("Location: success.php");
}

// AGAIN, PLEASE NOTE: the above redirects are made just for examples. Ideally it should always go
// to the error page if the response from abc financial's servers is not a 200 upon posting our 
// form's json object to their servers
                                      

//  ***************** Finally, post the newly computed json object to ABC Financial's servers

$ch = curl_init('https://api.abcfinancial.com/rest/9003/members/agreements');   // where to post                                                                   
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonSubmitData);                                                                  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json',
    'app_id: b296df2d',
    'app_key: ff93310d0c0b3d8f7596aaf4dc29d01e',
    'clubNumber: 9003', 

    'Content-Length: ' . strlen($jsonSubmitData)

  )                                                                       
);                                                                                                                   

$result = curl_exec($ch);
// save the server response in a new variable. Please note: this variable is echoed on the next page which is success.php.
$_SESSION['serverResponse'] = $result;

?>
