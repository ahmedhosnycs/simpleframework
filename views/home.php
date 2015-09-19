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
      <h1>Madeo Task</h1><span> by <a href='http://ahmedhosnycs.com'>Ahmed H. Ibrahim</a></span>
    </div>
    <!-- Form Module-->
    <div class="module form-module">
      <div class="toggle"><i class="fa fa-times fa-pencil"></i>
        <div class="tooltip">Click Me</div>
      </div>
      <div class="form">
        <h2>Login to your account</h2>
        <form id="login-form" method="POST" action="user/login">
          <input id="loginemail" name="email" type="email" placeholder="Email Address"/>
          <input id="loginpassword" name="password" type="password" placeholder="Password"/>
          <button>Login</button>
        </form>
      </div>
      <div class="form">
        <h2>Create an account</h2>
        <form id="register-form" method="POST" action="user/register">
          <input type="hidden" name="token" value="<?php echo $token ?>"/>
          <input id="username" name="username" type="text" placeholder="Username"/>
          <input id="password" name="password" type="password" placeholder="Password"/>
          <input id="email" name="email" type="email" placeholder="Email Address"/>
          <input id="phone" name="phone" type="tel" placeholder="Phone Number"/>
          <input id="dob" name="dob" type="date" placeholder="Date of Birth"/>
          <button type="submit">Register</button>
        </form>
      </div>
      <!-- <div class="cta"><a href="">Forgot your password?</a></div> -->
    </div>
  </body>
<script src='http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js'></script>
<script src="public/js/happy.methods.js"></script>
<script src="public/js/happy.js"></script>
<script src="public/js/index.js"></script>
</html>
