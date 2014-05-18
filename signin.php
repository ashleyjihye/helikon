<?php

require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");
require_once("header.php");

printPageTop("Sign In to Helikon");
printSignInForm();
signIn();
logIn();

function printSignInForm() {
    $script = $_SERVER['PHP_SELF'];
    print <<<EOT



      <form style="text-align:center;" class-"form-horizontal" method="post" action="$script">
      <div style="text-align:center; width:400px; margin: auto; margin-top: 50px;" class="form-group">
    <label style="padding: 1px;" for="name" class="col-sm-2 control-label">Name</label> 
    <div class="col-sm-10"><input type="text" class="form-control" name="name" id="name"></div></div>
					     

<div style="text align: center; width:400px; margin: auto; margin-top: 20px; margin-bottom: 20px;" class="form=group">
    <label style="padding: 1px;" for="email" class="col-sm-2 control-label">Email</label> <div style="margin-top:10px;" class="col-sm-10"><input type="text" class="form-control" name="email" id="email"></div></div>

<div style="text align: center; width:400px; margin: auto; margin-top: 20px; marign-bottom: 20px;" class="form=group">
  <label style="padding: 1px;" for="username" class="col-sm-2 control-label">Username</label> <div style="margin-top: 10px;" class="col-sm-10"><input type="text" class="form-control" name="username" id="username"></div></div>

<div style="text-align:center; width: 400px; margin: auto; margin-top: 20px; margin-bottom:20px;" class="form-group">
  <label style="padding: 1px;" for="password" class="col-sm-2 control-label">Password</label> <div style="margin-top: 10px;" class="col-sm-10"><input type="password" class="form-control" name="password" id="password"></div></div>

<div style="text align: center; width:400px; margin: auto; margin-top: 20px; margin-bottom: 20px;" class="form=group">
    <label style="padding:1px;" for="password" class="col-sm-2 control-label">Confirm Password</label><div style="margin-top:10px;" class="col-sm-10"> <input type="password" class="form-control" name="password1" id="password1"></div></div>


<div style="text align: center; width:400px; margin: auto; margin-top: 50px; margin-bottom: 20px;" class="form=group">
	<input style="margin-top: 20px;" type="submit" value="Sign Up" class="btn btn-default btn-lg">
	<p style="text-align: center;"></div>

</form>
EOT;
}






?>

</body>
</html>