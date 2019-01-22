<!-- 


/****************************



This page consists of the the long form (coded in HTML) which will get the personal information of a user, and then the payment form to which a user can pay.

A summary of what is happening on this page.

1.) An example can be found at this specific URL of this page -> http://bokharilovesyou.com/cac/app/plan-detail.php?planId=5e9462984eaa4160b33b9b13dfe07276
2.) We primarily make the same API call which we made in the previous page ('app.php') to get all the plans again. (please note: that on this page, we need to get THIS specific plan's ID as well as its validation hash)
3.) Then, we perform a Get Request to https://api.abcfinancial.com/rest/{clubNumber}/clubs/plans/{paymentPlanId} inserting the dynamic paymentPlanID (we get this within the json object of our previous 'plans' call).
4.) We retrieve the planValidationHash
5.) We make another request to get the 'PayPage' (this is the iframe from which a user can input their billing details) on https://api.abcfinancial.com/rest/{clubNumber}/members/paypage/paypagecreateagreement
6.) This returns a createAgreementPayPageURL within its json object.
7.) We get that createAgreementPayPageURL and embed it within our HTML.


~~~~~~~~

i.) The next part is to separate both of these forms (the iframe, as well as our handcoded html form) into a two-step form.
ii.) This is what we have done by using some basic javascript.
iii.) The form is validated on the frontend, and then moves on, to the paypage.
iv.) Please note: that the form consists of three 'hidden fields'. These three hidden fields are important as they yield the planID, planValidationHash as well as the transactionID (for this example, on the live demo, I have not kept it as hidden, however once live - it has to be hidden).
v.) By doing what we have done above, we get the planID as well as the planValidationHash - however we would still need to have the transactionID in order to make a successfully post our data to abc financial's servers.
vi.) We get this transactionID through javascript - please refer to their documentation - they mention that we would need to write a new JavaScript function to the pay page to receive the transaction ID, source: https://i.imgur.com/zg2YgMQ.png
vii.) This specific function would basically return a transactionID once the payment form has been successfully submitted. 
viii.) As soon as the payment form has been submitted, we on the front end using javascript fetch the transaction ID, pass it on, onto our hidden field, and then submit our form to our servers primarily, and then post our form's data to abc financial's servers.

Below is a breakdown of what has been told above.



~~~~~~~~

Once we've understood how this page is working, please head over to ../js/app.js to understand more



****************************\

 -->




<!-- A simple HTML head -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>CAC Test</title>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link href="../css/app.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Roboto+Mono:400,500,700" rel="stylesheet">

<!-- Styles for this page -->
<style>
  fieldset {
    display: flex; 
    flex-direction: column;
    margin-bottom: 1em;
    width: 100%;
  }

  label {
    margin-bottom: .2em;
  }

  .parent {
    display: flex; 
    justify-content: space-between;
    flex-wrap: wrap;
  }

  form {
    max-width: 1000px; 
    margin-left: auto;
    margin-right: auto;
    padding: 40px; 
    margin-top: 20px;
    box-shadow: 0 0 27px 0 rgba(214, 231, 233, 0.52);
    border-radius: 5px;

  }

  input {
    overflow: visible;
    padding: 10px 0;
    border-radius: 5px;
    border: 0;
    border-bottom: 1px solid #e2e2e2;
    border-radius: 0;
    width: 100%;
  }

  input:focus {
    outline:0;
  }


  input::placeholder {
    color: #dedede;
    font-size: 0.8em;
}


@media (min-width: 768px) {
  fieldset {
    max-width: 300px;
  }
}



.small,
.smallEmail,
.small-verify-email {
    /* font-size: 8px; */
    /* padding-left: 20px; */
    display: block;
    color: red;
    font-size: 10px;
    position: absolute;
    top: 0;
    right: -20px;
    width: 100%;
    max-width: 50%;
    display: none;
}

.dob-label,
.labelEmail,
.verify-email {
  position: relative;
}

.show {
  display: block !important;
}


.warning {
    border-bottom: 2px solid red;
}

select {
  height: 2em;
  background-color: #fff;
  border: 1px solid #eee;
  box-shadow: 0 0 27px 0 rgba(214, 231, 233, 0.52);
}

.hidden {
  visibility: hidden;
  opacity: 0;
  display: none;
  transition: 0.2s ease;
}

.show-me {
  visibility: visible;
  opacity: 1;
  display: block;
  transition: 0.2s ease;
}

.loader-parent {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  z-index: 9999;
  filter: invert(100%);
}

.loader-parent img {
  width: 8em;
  height: 8em;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -47%);
  transition: 0.2s ease;
}

.overlay {
  background-color: rgba(255,255,255,0.6);
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  transition: 0.2s ease;
}

.effect-hidden {
  opacity: 0;
  visibility: hidden;
}

.loader-translate {
  transform: translate(-50%, -50%);
  transition: 0.2s ease;
}

.effect-visible {
  transition: 0.2s ease;
  opacity: 1;
  visibility: 1;
}


</style>
<!-- //End Styles -->





</head>
<!-- //End Head -->
<body class="">



<!-- Making a GET Request to fetch the plans -->

<?php 
// Create a stream

$opts = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"app_id: b296df2d\r\n" .
              "app_key: ff93310d0c0b3d8f7596aaf4dc29d01e\r\n" .
              "clubNumber: 9003\r\n".
              "onlyPromoPlans: false\r\n".
              "inClubPlans: true\r\n".
              "ignorePlanAvailabilityWindow: false\r\n".
              "Accept: application/json;charset=utf-8\r\n"
  )
);

$context = stream_context_create($opts);

// Open the file using the HTTP headers set above
$file = file_get_contents('https://api.abcfinancial.com/rest/9003/clubs/plans', false, $context);
// Save this specific URL as a variable
$planIdUrl = 'https://api.abcfinancial.com/rest/9003/clubs/plans/';
// Get the planID
$idVariable = $_GET['planId'];
// Concanicate the above URL as well as this page's planID to create a new URL
$planIdAPI = $planIdUrl . $idVariable;
// Get the file Contents of our newly concanicated and generated URL
$fileTwo = file_get_contents($planIdAPI, false, $context);
// decode the json to use it as a PHP Array
$fileTwoJson = json_decode($fileTwo, true);
// print_r($fileTwoJson) ***********. YOU CAN PRINT THIS TO SEE THE FULL ARRAY;

// Get the planValidationHash
$planValidationHash = $fileTwoJson['paymentPlan']['planValidation'];


// Make another Get Request to get the Paypage's URL which would be embedded within the iframe's source.

// Create a stream
$optsPay = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"app_id: b296df2d\r\n" .
              "app_key: ff93310d0c0b3d8f7596aaf4dc29d01e\r\n".
              "Accept: application/json;charset=utf-8\r\n"
  )
);

$contextPay = stream_context_create($optsPay);

// Open the file using the HTTP headers set above
$paypage = file_get_contents('https://api.abcfinancial.com/rest/9003/members/paypage/paypagecreateagreement', false, $contextPay); ?>
<br><br>
<?php 
// Decoding the content
  $json = json_decode($paypage, true);
?>
  <br><br>



<!-- Below consists of HTML integrated with our PHP variables to display dynamic outcome such as the planID, planValidationHash, etc (please read the top to understand more) -->
<div class="hard-background container text-center pt-5">
  <h1>Plan Detail</h1>
  <?php echo 'PlanID = ' . $_GET['planId']; ?>



  <div>
<!-- Form Start -->

 <form class="pt-4 pb-4 mb-4 my-form text-left" id="contact" action="submitform.php" method="post">
    <h3 class="pb-4">Member Information</h3>

    <div class="parent name-parent">
      <fieldset>
        <label for="firstName">First Name <span id="firstNameRequired" class="required">*</span></label>
        <input class="required firstName" name="firstName" placeholder="First Name" type="text" tabindex="1" autofocus>
      </fieldset>

      <fieldset>
        <label for="lastName">Last Name <span id="lastNameRequired" class="required">*</span></label>
        <input class="required lastName" name="lastName" placeholder="Last Name" type="text" tabindex="1" autofocus>
      </fieldset>

      <fieldset>
        <label for="middleName">MI</label>
        <input name="middleName" placeholder="MI" type="text" tabindex="1" autofocus>
      </fieldset>
    </div>

    <div class="parent email-parent">
      <fieldset>
        <label class="labelEmail" for="email">Email Address <span id="emailRequired" class="required">*</span><span class="smallEmail">Not a valid Email</span></label>
        <input class="required eMail" name="email" placeholder="Email Address" type="text" tabindex="1" required autofocus>
      </fieldset>

      <fieldset>
        <label class="verify-email" for="email">Verify Email <span id="verifyEmailRequired" class="required">*</span><span class="small-verify-email">Emails don't match</span></label>
        <input class="required eMailMatch" name="verifyEmail" placeholder="Email Address" type="text" tabindex="1" autofocus>
      </fieldset>
    </div>

    <div class="parent mailing-address-parent">
      <fieldset>
        <label for="mailingAddress">Mailing Address (including apt or unit #) <span id="mailingAddressRequired" class="required">*</span></label>
        <input class="required" name="mailingAddress" placeholder="Mailing Address" type="text" tabindex="1" autofocus>
      </fieldset>

      <fieldset>
        <label for="city">City <span id="cityRequired" class="required">*</span></label>
        <input class="required" name="city" placeholder="City" type="text" tabindex="1" autofocus>
      </fieldset>
    </div>


    <div class="parent postal-parent">
      <fieldset>
        <label for="postalCode">Postal Code <span id="postalCode" class="required">*</span></label>
        <input class="required" name="postalCode" placeholder="Postal Code" type="text" tabindex="1" autofocus>
      </fieldset>

      <fieldset>
        <label class="dob-label" for="dob">Date of Birth <span id="dobRequired" class="required">*</span><span class="small small-dob">Has to be in the format MM/DD/YYYY</span></label>
        <input class="required dob-input" name="dob" placeholder="MM/DD/YYYY" type="text" tabindex="1" autofocus>
      </fieldset>
    </div>

    <div class="parent gender-parent">
      <fieldset>
        <label for="gender">Gender</label>
          <select style="width: 100%" id="gender" name="gender">
            <option value="" selected="selected">Not Specified</option>
            <option value="MALE">Male</option>
            <option value="FEMALE">Female</option>
          </select>
      </fieldset>

      <fieldset>
        <label for="homePhone">Home Phone <span id="homePhoneRequired" class="required">*</span></label>
        <input class="required" id="homePhone" type="text" value="" maxlength="15">
      </fieldset>
    </div>


    <div class="parent phone-parent">
      <fieldset>
        <label for="mobilePhone">Mobile Phone</label>
        <input id="mobilePhone" type="text" value="" maxlength="15">
      </fieldset>

      <fieldset>
        <label for="workPhone">Work Phone</label>
        <input id="workPhone" type="text" value="" maxlength="15">
      </fieldset>

      <fieldset>
        <label for="extension">Extension</label>
        <input id="extension" type="text" value="" maxlength="15">
      </fieldset>
    </div>  

    <div class="parent driver-parent">
      <fieldset>
        <label for="license">Drivers License</label>
        <input id="license" type="text" value="" maxlength="15">
      </fieldset>

      <fieldset>
        <label for="employer">Employer</label>
        <input id="employer" type="text" value="" maxlength="15">
      </fieldset>
    </div> 


    <div class="parent hear-parent">
      <fieldset>
      <label for="campaign">How did you hear about us <span id="campaignRequired" class="required">*</span></label>
      <select class="required" style="width: 100%" id="campaign" name="campaign"><option value="none" selected="selected">-Select One-</option><option value="1535e7880fc847f0af17f1be759bf51c">Billboard</option><option value="0062704fb7eb45cd9563a29a0d3649bb">Chicago Cubs and Gallagher Way</option><option value="b19078c327c6495ab53541718572337a">Complimentary Guest Pass</option><option value="80a8b557ee324544900383a890a7b32e">F45</option><option value="ec3a18b7c2484661a0666561b919d71b">Groupon</option><option value="a2321d46bb694c799545b9cafdd54fa0">I was a Former Member</option><option value="3335F66B36708EB6E05302E014AC5EF3">Mailer</option><option value="6e43a9cc5093449b88e11fa1b5b8c46a">Member Referral</option><option value="8162ccce1d4e40669c025c370dce4843">My Property Manager</option><option value="3335F66B36768EB6E05302E014AC5EF3">Newspaper/Magazine</option><option value="e2b830ab99544c96bbbe9795c87f95c3">Not Available</option><option value="a283b804eb6548cfa5487d3c454e54f7">Online Search</option><option value="fd4e9dae1b4949579700c265b68b14fd">Other</option><option value="d7e41cab15f6423fa5120e4a6724c1c2">Social Media</option><option value="f268feace6c541358d947b3bc563c3f1">Summer Days</option><option value="2CA38FC99715A725E05301E014AC328E">Television</option><option value="9bda46e8a33d46ba96a5989a6fb7c2fe">Walking or Driving By</option><option value="c809426ef87f4ab3bc11c2582ac4ae22">Yoga in the Ballpark Referral Offer</option></select>
      </fieldset>
    </div> 


    <div class="parent hidden-parent">
      <fieldset>
        <label for="paymentPlanID">Payment Plan ID</label>
        <input name="paymentPlanID" id="paymentPlanID" type="text" value="<?php echo $_GET['planId']; ?>" maxlength="15">
      </fieldset>

      <fieldset>
        <label for="planValidationHash">Plan Validation Hash</label>
        <input name="planValidationHash" id="planValidationHash" type="text" value="<?php echo $planValidationHash; ?>" maxlength="15">
      </fieldset>
    </div>

    <div class="parent transaction-parent">
      <fieldset>
        <label for="transactionID">Transaction ID</label>
        <input name="transactionID" id="transactionID" type="text" value="" maxlength="15">
      </fieldset>

    </div>


    <button style="width: 100%; text-transform: uppercase;" class="mt-3 mb-3 btn btn-dark" name="next" id="next" type="button">Proceed To Payment</button>


      <button style="width: 100%; text-transform: uppercase; display: none;" class="mt-3 mb-3 btn btn-dark" name="submit" type="submit" id="contact-submit" data-submit="...Sending">Submit</button>
    </fieldset>
  </form>


<!-- //Form End -->


    <button style="width: 100%; text-transform: uppercase;     
    max-width: 250px;
    margin-left: auto;
    margin-right: auto;
    margin-top: 3em !important;" class="mt-3 mb-3 btn btn-dark hidden" name="prev" type="prev" id="prev">Go Back</button>


  <iframe id="payPage" class="hidden" name="select_frame" style="width: 100%; height: 100%; min-height: 800px; padding: 0px; border: none;" src="<?php echo $json['result']['createAgreementPayPageUrl']; ?>"></iframe>
  </div>
</div>


<div class="loader-parent effect-hidden">
  <img src="https://samherbert.net/svg-loaders/svg-loaders/oval.svg">
</div>

<div class="overlay effect-hidden"></div>


<!-- JavaScript Files -->
    <script
      src="https://code.jquery.com/jquery-3.2.1.min.js"
      integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
      crossorigin="anonymous"></script>
  <script src="../js/app.js"></script>
<!-- //Javascript Files End -->

<script>
  var planJsonData = <?php echo json_encode($file) ?>;
  var plansJson = JSON.parse(planJsonData);
</script>

</body>
</html>