<!-- Ashley Thomas and Sasha Levy
  Helikon
  adddata.php
  5/19/14

This is basically the login/signup page.
 -->

 <?php

require_once("MDB2.php");
require_once("/home/cs304/public_html/php/MDB2-functions.php");
require_once("athomas2-dsn.inc");
require_once("header.php");

//always have this to check if user is logged in yet, and then actually try to sign them up/log them in
printPageTop("Helikon");
checkLogInStatus();
logIn();
signIn();

?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.js"></script>
<script>
$(document).ready(function(){
$('#username').keyup(username_check);
});
	
//function that checks if username is a duplicate and accordingly tells the user
function username_check(){	
var username = $('#username').val();
if(username == "" || username.length < 4){
$('#username').css('border', '3px #CCC solid');
$('#tick').hide();
}else{

jQuery.ajax({
   type: "POST",
   url: "check2.php",
   data: 'username='+ username,
   cache: false,
   success: function(response){
    if(response == 1){
    	$('#username').css('border', '3px #C33 solid');	
    	$('#tick').hide();
    	$('#cross').fadeIn();
    }
    else{
    	$('#username').css('border', '3px #090 solid');
    	$('#cross').hide();
    	$('#tick').fadeIn();
    }
  }
});
}
}

</script>

<style type="text/css">
#tick {
  display:none;
}
#cross {
display:none;
}
       
#intro { 
  background: url(images/logo.png) 50% 0;  
  height: auto;  
  margin: 0 auto; 
  width: 100%; 
  position: relative; 
  box-shadow: 0 0 50px rgba(0,0,0,0.8);
  padding: 100px 0;
}
#about { 
  background: url(images/home.jpg) 50% 0 fixed; 
  height: auto;  
  margin: 0 auto; 
  width: 100%; 
  position: relative; 
  box-shadow: 0 0 50px rgba(0,0,0,0.8);
  padding: 200px 0;
}
#signup { 
  background: url(images/background.jpg) 50% 0 fixed; 
  height: auto;
  margin: 0 auto; 
  width: 100%; 
  position: relative; 
  box-shadow: 0 0 50px rgba(0,0,0,0.8);
  padding: 100px 0;
  color: #fff;
}

/* Non-essential demo stuff */
.hero-unit {
  background-color: #fff;
  box-shadow: 0 0 20px rgba(0,0,0,0.1);
 }
.media-object { 
 width: 64px; height: 64px; padding-bottom: 30px;
 }
</style>

<body>
	
<!--the navbar when you're logged out: this is where the user can log in-->
<div style="background-image: url();" class="navbar navbar-default">
   <div class="navbar-inner">
   <div class="navbar-header">
 
      <a class="brand" href="index.php"><img src="images/logofinal.png"></a> 
  
   </div>
</div>
   <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
   <ul class="nav navbar-nav">
   <li><a href="#about">About</a></li>
   <li><a href="#signup">Sign Up</a></li>
   </ul>
   <ul class="nav navbar-nav navbar-right">
   <form method="post" action="<?php $_SERVER['PHP_SELF'] ?>" class="navbar-search pull-right">
   <input type="text" class="span2" name="loginusername" id="loginusername" placeholder="Username">
   <input type="password" class="span2" name="loginpassword" id="loginpassword" placeholder="Password">
   <input type="submit" value="Log In" class="btn btn-primary"></form> 
   </div>
   </div>
   </div>
		   
	<!-- Section #1: logo -->
	<section id="intro" data-speed="6" data-type="background">
		<div class="container">
			<div class="row-fluid">
	    
	    	</div>
	    
	    </div>
	</section>

	<!-- Section #2: information about helikon-->
	<section id="about" data-speed="4" data-type="background">
		<div class="container">
			<div class="row-fluid">
		        <div class="span4 well">
		          <h2>Helikon</h2>
		          <ul><li>See recent media, rate and write reviews
                <li>Connect with your friends and make new friends
<li>See what your friends are watching and listening to
<li>Edit your profile and add your favorite media to your favorites list
</ul>
 </div><!-- /.span4 -->
	    	</div>
	    </div>
	</section>

	<!-- Section #3: signing up -->
	<section id="signup" data-speed="2" data-type="background">
		<div class="container">
			<div class="page-header">
				<h1>Sign Up for Helikon<small> It&#39s free!</small></h1>
			</div>
			<div class="row-fluid">
		        <div class="span4">
		          
      <form style="text-align:center;" class-"form-horizontal" method="post" action="<?php $_SERVER['PHP_SELF']  ?>">
      <div style="text-align:center; width:400px; margin: auto; margin-top: 50px;" class="form-group">
    <label for="name" class="col-sm-2 control-label">Name</label> 
    <div class="col-sm-10"><input type="text" class="form-control" name="name" id="name"></div></div>
<div style="text align: center; width:400px; margin: auto; margin-top: 20px; margin-bottom: 20px;" class="form=group">
    <label style="padding: 1px;" for="email" class="col-sm-2 control-label">Email</label> <div style="margin-top:10px;" class="col-sm-10"><input type="text" class="form-control" name="email" id="email"></div></div>
<div style="text align: center; width:400px; margin: auto; margin-top: 20px; margin-bottom: 20px;" class="form=group">
  <label style="padding: 1px;" for="username" class="col-sm-2 control-label">Username</label> 
   <div style="margin-top: 10px;" class="col-sm-10"><input style="display:inline; width:93%;" type="text" class="form-control" name="username" id="username">
<img id="tick" src="images/tick.png" width="16" height="16"/>
<img id="cross" src="images/cross.png" width="16" height="16"/>
</div></div>
<div style="text-align:center; width: 400px; margin: auto; margin-top: 20px; margin-bottom:20px;" class="form-group">
  <label style="padding: 1px;" for="password" class="col-sm-2 control-label">Password</label> <div style="margin-top: 10px;" class="col-sm-10"><input type="password" class="form-control" name="password" id="password"></div></div>
<div style="text align: center; width:400px; margin: auto; margin-top: 20px; margin-bottom: 20px;" class="form=group">
    <label style="padding:1px;" for="password" class="col-sm-2 control-label">Confirm Password</label><div style="margin-top:10px;" class="col-sm-10"> <input type="password" class="form-control" name="password1" id="password1"></div></div>
<div style="text align: center; width:400px; margin: auto; margin-top: 50px; margin-bottom: 20px;" class="form=group">
	<input style="margin-top: 20px;" type="submit" value="Sign Up" class="btn btn-default btn-lg">
	<p style="text-align: center;"></div>
</form>
	    	</div>
	    </div>
	</section>

</body>
