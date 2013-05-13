<!DOCTYPE html>
<?php include_once "functions.php"; ?>
<html>
<head>
  <?php printHead('Licencia uLogger'); ?>
</head>
<body>
  <?php include("templates/menu.tpl.php"); ?>
  <div id="page">
    <div class="container">
    <?php include("templates/messages.tpl.php"); ?>        
    <!-- START CONTENT -->      
      <div class="hero-unit span6 center text-center">
        <h1 class="muted">Denna sida saknas</h1>        
        <h3 class="text-error">Fel 404</h3>                
        <p><a href="/" class="btn btn-primary btn-large top-buffer">Till startsidan &raquo;</a></p>  
      </div>      
      <div class="text-center"><img width="200px" src="img/404.jpg"></div>      
    <!-- END CONTENT -->
    </div>
  </div>
  <?php include("templates/footer.tpl.php"); ?>
  <?php include("templates/scrips.tpl.php"); ?>
</body>
</html>
