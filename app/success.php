<?php 
 session_start();
?>


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
</head>

<h1>Success</h1>
<p><strong>Server Response:</strong> <?php echo $_SESSION['serverResponse']; ?></p>

<p><strong>Object posted to ABC Financial Servers:</strong></p>
<p><?php echo $_SESSION['jsonData']; ?></p>

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