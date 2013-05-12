<!DOCTYPE html>
<?php include_once "functions.php"; ?>
<html>
<head>
  <?php printHead('Licencia uLogger'); ?>
</head>
<body>
  <?php include("templates/menu.tpl.php"); ?>
  <div id="wrap"><div class="container">
    <?php echo theme_messages(); ?>
    <!-- START CONTENT -->
      
      <div class="hero-unit">
        <h1>Licencia uLogger</h1>
        <p>Med hjälp av Licencia uLogger kan du enkelt sätta upp en loggserver hos en kund där du behöver övervaka SIP-trafiken. Licencia uLogger körs på Raspberry PI vilket är en liten enkortsdator som kan utföra det mesta en PC kan.</p>
        <p><a href="#" class="btn btn-primary btn-large">Läs mer &raquo;</a></p>
      </div>

      <div class="row">
        <div class="span6">
          <h2>SIP-logg</h2>
          <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui. </p>
          <p><a class="btn" href="#">View details &raquo;</a></p>
        </div>
        <div class="span2">
          <h2>Fjärrstyr med VNC</h2>
          <p>uLogger kan fjärrstyras med en VNC klient och har som standard programmet TightVNC Server installerat. Med klienten Ultra VNC Viewer kommer du åt servern via ett grafiskt gränssnitt. </p>
          <p><a class="btn" href="#">View details &raquo;</a></p>
       </div>
        <div class="span4">
          <h2>Heading</h2>
          <p>Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.</p>
          <p><a class="btn" href="#">View details &raquo;</a></p>
        </div>
      </div>
      
    <!-- END CONTENT -->
  </div></div>
  <?php include("templates/footer.tpl.php"); ?>
  <?php include("templates/scrips.tpl.php"); ?>
</body>
</html>
