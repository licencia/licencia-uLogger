<?php require_once "ulogger/functions.php"; ?>

<!DOCTYPE html>
<html>
<head>
  <?php printHead('Licencia uLogger'); ?>
</head>
<body>
  <?php include("ulogger/templates/menu.tpl.php"); ?>
  <div id="page">
    <div class="container">
    <?php include("ulogger/templates/messages.tpl.php"); ?>        
    <!-- START CONTENT --> 
      <div class="hero-unit">
        <h1>Licencia uLogger</h1>
        <p>Med hjälp av Licencia uLogger kan du enkelt sätta upp en loggserver hos en kund där du behöver övervaka SIP-trafiken. Licencia uLogger körs på Raspberry PI vilket är en liten enkortsdator som kan utföra det mesta en PC kan.</p>
        <?php if ($logged_in): ?>
        <p><a href="/siplogg.php" class="btn btn-primary btn-large">Ta mig dit nu &raquo;</a></p>
        <?php endif; ?>
        
      </div>

      <div class="row">
            
        <div class="span2">
          <div>
            <a class="thumbnail top-buffer" href="#lightbox1" data-toggle="lightbox">
              <img alt="Click to view the lightbox" src="img/ulogger-01-small.png">
            </a>
          </div>     
          <div>  
            <a class="thumbnail top-buffer" href="#lightbox2" data-toggle="lightbox">
              <img alt="Click to view the lightbox" src="img/ulogger-02-small.png">
            </a>
          </div>   
        </div>
                  
        <div class="span5">
          <h2>SIP-logg</h2>
          <p class="lead">Logga IP-trafik direkt till ett SD-kort i uLogger och kontrollera allt över nätet, direkt från din telefon eller en PC.</p>
          <p>uLogger dumpar trafiken med TCP-dump och stöder filter så väl som uppdelning i mindre filer och ringbuffert för hantering av längre loggar. </p>
          <p>Kvarstående diskkapacitet och tid för loggen visas i relatid.</p>
          <?php if ($logged_in): ?>
          <p><a class="btn btn-success" href="siplogg.php">Börja logga &raquo;</a></p>
          <?php endif; ?>
        </div>

        <div class="span5">
          <h2>Fjärrstyr med VNC</h2>
          <div>
            <a class="thumbnail vnc1-image" href="#lightbox3" data-toggle="lightbox">
              <img alt="Click to view the lightbox" src="img/vnc-01-small.png">
            </a>
          </div>
          <p class="top-buffer">uLogger kan fjärrstyras med en VNC klient och har som standard programmet TightVNC Server installerat. Med klienten Ultra VNC Viewer kommer du åt servern via ett grafiskt gränssnitt. </p>
          <?php if ($logged_in): ?>  
          <p><a class="btn btn-success" href="settings.php">Till inställningar &raquo;</a></p>
          <?php endif; ?>
       </div>
       
       <!-- Hidden images -->
       <div id="lightbox1" class="lightbox hide fade" tabindex="-1" role="dialog" aria-hidden="true">
          <div class='lightbox-content'>
          <img src="img/ulogger-01.png">          
          </div>
        </div>
        <div id="lightbox2" class="lightbox hide fade" tabindex="-1" role="dialog" aria-hidden="true">
          <div class='lightbox-content'>
          <img src="img/ulogger-02.png">
          </div>
        </div>
        <div id="lightbox3" class="lightbox hide fade" tabindex="-1" role="dialog" aria-hidden="true">
          <div class='lightbox-content'>
          <img src="img/vnc-01.png">
          <div class="lightbox-caption"><p>Med UltraVNC Viewer kan du fjärrstyra uLogger via ett grafiskt gränssnitt.</p></div>
          </div>
        </div>   
       
    </div>     
      
    <!-- END CONTENT -->
    </div>
  </div>
  <?php include("ulogger/templates/footer.tpl.php"); ?>
  <?php include("ulogger/templates/scrips.tpl.php"); ?>
</body>
</html>
