<?php

require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");
require_once("header.php");


printPageTop("Login to Helikon");
printLoginForm();
logIn();

function printLoginForm() {
    $script = $_SERVER['PHP_SELF'];
    print <<<EOT
     
      
      <form style="text-align: center;" class="form-horizontal" method="post" action="$script">
      <div style="text-align:center; width: 400px; margin: 0 auto; margin-top: 50px;" class="form-group">
      <label style="padding: 1px;" for="username" class="col-sm-2 control-label">Username</label>
			      <div class="col-sm-10">
			      <input type="text" class="form-control" name="username" id="username" placeholder="Username">
			      </div>
			      </div>
			      <div style="text-align: center; width: 400px; margin: auto; margin-top: 20px; margin-bottom: 20px;" class="form-group">
			      <label style="padding: 1px;" for="inputPassword3" class="col-sm-2 control-label">Password</label>
							    <div class="col-sm-10">
							    <input type="password" class="form-control" name="password" id="password" placeholder="Password">
							    </div>
							    </div>
							    <form style="text-align:center;" method="link" action="logIn()">
		<input type="submit" value="login" class="btn btn-primary btn-lg">
	</form><br>



</form>



</body>
</html>
EOT;
}






?>
