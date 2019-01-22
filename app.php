<!-- 


/****************************

This is the checkout form made for Chicago Athletic Clubs which makes calls to ABC Financial Servers in order to get and post data.

The frontend is written in JavaScript with jQuery, whereas the backend's in PHP and occasionally uses curl to get or post requests.


The directory consists of three folders

APP -> Entries.json, error_log, plan-detail.php, submitform.php, success.php
CSS -> Consists of some css, but most of the pages are using inlined css on top of the page.
JS -> Consists of the core as well as the global js file.
app.php -> This file.
error_log -> consits of the errors.
data.json -> Just an example json object (not relevant).
test.html -> not relevant.


Just a small summary of what is going on:

1.) There are currently two pages which would be rendered on the frontend.
2.) First one is this, app.php - whereas the other one is app/plan-detail.php
3.) This one (app.php) basically gets all the plans making a call to ABC Financial's servers using the required headers.
4.) The other one (app/plan-detail.php) consists of the checkout form of the specific plan selected by a user.
5.) The other one (app/plan-detail.php) consists of a two step form which first yields the personal information of a user and validates it through JavaScript, and the next step consists of the payment part of the same form. However, the payment form is embedded as an iframe.
5.) More detail of the app/plan-detail.php file can be found on its own php file. 


*********************
MORE INFORMATION ON WHAT IS GOING ON THIS PAGE
*********************

1.) This page can be found on a demo url which is -> http://bokharilovesyou.com/cac/app.php
2.) It gets all the plans and displays them within a forech loop, the call is made to this url https://api.abcfinancial.com/rest/9003/clubs/plans
3.) Each plan has its own planID as well as other properties which can be fetched in json, i.e, data['plans'].
4.) In order to see the full json object as well as the response, please echo $data in PHP - or just simply console.log('plansJson') if you want to see them within the front-End.
5.) More of this is explained in the next file, that is app/plan-detail.php


~~~~~~~~

Once we've understood how this page is working, please head over to app/plan-detail.php to understand more



*****************************\


 -->


<!-- A simple HTML Head -->

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>CAC Test</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Bootstrap -->
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <link href="css/app.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Roboto+Mono:400,500,700" rel="stylesheet">
</head>

<!-- //End Head -->

<!-- Begin Body -->
<body class="plans-page">

<!-- Making a GET Request from ABC Financial's Servers  -->

<?php 
// Create a stream
$opts = array(
  'http'=>array(
    'method'=>"GET",
    // Setting necessary or relevant headers
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
?>

<!-- HTML to create the dom -->
<div class="hard-background container text-center pt-5">
  <h1>Plans Overview</h1>
  <div class="container pt-5 pb-5 text-center">
    <div class="row">
<!--  Decoding $file to get a PHP Array -->
    <?php $data = json_decode($file, true); ?>
    <!-- The loop begins -->
    <?php  foreach($data['plans'] as $value) { ?>
     <div class="col-md-4 mt-3"> 
      <div class="plan-box">  
        <!-- Dynamically adding a parameter in the URL of each plan to get the Plan ID (However, this doesn't seem to be too necessary) -->
        <a href="app/plan-detail.php?planId=<?php echo $value['planId'];?>"><h2><?php echo $value['planName'];?></h2></a>
      </div>
    </div>
        <?php } ?>
   <!-- End the loop -->
    </div>
  </div>
</div>


<!-- JavaScript Files -->
    <script
      src="https://code.jquery.com/jquery-3.2.1.min.js"
      integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
      crossorigin="anonymous"></script>
	<script src="js/app.js"></script>
<!-- //Javascript Files End -->

<!-- Complete json object from ABC Financial's servers if needed to be accessed within the frontend -->
<script>
  var planJsonData = <?php echo json_encode($file) ?>;
  var plansJson = JSON.parse(planJsonData);
</script>
<!-- //End Complete json object -->

</body>
<!-- //End Body -->

<!-- 

Please read more on the plan-detail page

 -->


</html>
