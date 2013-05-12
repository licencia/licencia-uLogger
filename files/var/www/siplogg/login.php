<!DOCTYPE html>
<?php include_once "functions.php"; ?>
<html>
<head>
  <?php printHead('Licencia uLogger'); ?>  
</head>
<body>
  <div id="wrap"><div class="container">    
    <!-- START CONTENT -->            
    
    <div class="form-signin">
      <h2 class="form-signin-heading">Logga in</h2>
      <?php echo theme_messages(); ?>
      <input type="text" class="input-block-level" id="user" placeholder="Användarnamn">
      <input type="password" class="input-block-level" id="password" placeholder="Lösenord">
      <label class="checkbox">
        <input type="checkbox" value="remember-me" id="remember_me"> Fortsätt vara inloggad          
      </label>
      <button class="btn btn-large btn-primary login-button" id="login">Logga in</button>
    </div>        
    
    <!-- END CONTENT -->
  </div></div>  
  <?php include("templates/scrips.tpl.php"); ?>
</body>
</html>
