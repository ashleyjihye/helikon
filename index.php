<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Helikon</title>

	<!-- Bootstrap -->
	<link href="css/bootstrap.min.css" type="text/css" rel="stylesheet">

</head>
<body>
	
<style type="text/css">
	body{
	    background: url("background.jpg") no-repeat top center fixed;
	    background-size: cover;
	    margin: 0;
	    padding: 0;
	    height: 100%;
	    width: 100%;
	}
	}

</style>

	<script src="https://code.jquery.com/jquery.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>
	<img src="helikon.png" style="position: absolute; margin: auto; left:0; right:0; top:0; bottom:0;" alt="helikonlogo">
	<form style="text-align:center;" method="link" action="login.php">
		<input type="submit" value="Log In" class="btn btn-primary btn-lg">
	</form><br>
	<form style="text-align:center;" method="link" action="#signup">
		<input type="submit" value="Sign In" class="btn btn-default btn-lg">
	<p style="text-align: center;">
	</form><br>



<?php

require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");
require_once("header.php");

printPageTop("Sign In to Helikon");
printSignUpForm();
signIn();
logIn();

function printSignUpForm() {
    $script = $_SERVER['PHP_SELF'];
    print <<<EOT
      <div id="signup">
<form method="post" action="$script">
  <label for="name">Name</label> <input type="text" name="name" id="name"><br>
  <label for="email">Email</label> <input type="text" name="email" id="email"><br>
  <label for="username">Username</label> <input type="text" name="username" id="username"><br>
  <label for="password">Password</label> <input type="password" name="password" id="password"><br>
    <label for="password">Confirm Password</label> <input type="password" name="password1" id="password1"><br>
  <input type="submit" value="login">
</form>
</div>
EOT;
}

?>


</body>
</html>
