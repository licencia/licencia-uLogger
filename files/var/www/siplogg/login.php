<?php

include_once "functions.php";

// Login script
if (isset($_REQUEST['ch']) && $_REQUEST['ch'] == 'login') {
	if($_REQUEST['uname'] == 'b' && $_REQUEST['pass'] == 'b')
		$_SESSION['login_user'] = 1;
	else
		$_SESSION['login_msg'] = 1;
}

// Logout script
if (isset($_REQUEST['ch']) && $_REQUEST['ch'] == 'logout') {
	unset($_SESSION['login_user']);
	header('Location:login.php');
  exit;
}

// Redirect
if (isset($_SESSION['login_user'])) {
	if(isset($_REQUEST['pagename']))
    header('Location:'.$_REQUEST['pagename'].'.php');
	else
	  header('Location:index.php');
  exit;  
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Licencia uLogger</title>
  <?php printHead(); ?>
  <link href="fine-upload/fineuploader-3.5.0.css" rel="stylesheet">  
  <script src="fine-upload/jquery.fineuploader-3.5.0.min.js"></script>
  <script src="js/settings.js"></script>  
</head>
<body>
<div id="page">
  <?php printHeaderMenu(0); ?>
  <div id="main"><div id="main-inner"><div role="main" class="column" id="content">
    <!-- CONTENT -->
  <div class="container">
    <div class="content">
      <div class="row">
        <div class="login-form">
          <h2>Login</h2>
          <form action="">
            <fieldset>
              <div class="clearfix">
                <input type="text" placeholder="Username" name="uname" id="uname">
              </div>
              <div class="clearfix">
                <input type="password" placeholder="Password" name="pass" id="pass">
              </div>
              <input type="submit" value="Login">
              <input type="hidden" name="ch" value="login">
            </fieldset>
          </form>
        </div>
      </div>
    </div>
  </div> <!-- /container -->
  				<?php	
					//display the error msg if the login credentials are wrong!
						if(isset($_SESSION['login_msg'])){
							echo 'Wrong username and password !';
							unset($_SESSION['login_msg']);
						}
					?>
  
    <!-- CONTENT -->
    <?php printFooter(); ?>
  </div></div></div>
</div>
</body>
</html>