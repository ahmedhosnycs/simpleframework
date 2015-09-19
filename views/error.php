<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    <title>Madeo Login</title>
    <link rel="stylesheet" href="/madeo/public/css/reset.css">
    <link rel='stylesheet prefetch' href='http://fonts.googleapis.com/css?family=Roboto:400,100,300,500,700,900|RobotoDraft:400,100,300,500,700,900'>
    <link rel='stylesheet prefetch' href='http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'>
    <link rel="stylesheet" href="/madeo/public/css/style.css">
 
  </head>

  <body>
    <!-- Ahmed Title-->
    <div class="pen-title">

      <?php if(is_array($errors)) {
      			for($i=0;$i<count($errors); $i++) { ?>
      				<h1><?php echo $errors[$i];?></h1>
      		 <?php }
  		} else {
  			echo "<h1>$errors</h1>";
  		}?>
    </div>
   </body>
   <script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
</html>