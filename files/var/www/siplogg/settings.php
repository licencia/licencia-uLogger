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
    <!-- START CONTENT -->
    
    <div class="row">
      
      <div class="span3">
        <?php include("templates/menu.left.tpl.php"); ?>
      </div>      
      
      <div class="span9">   
        <?php echo theme_messages(); ?>   
        <div class="alert hidden"></div>
        <h1 class="page-title">Serverinställningar</h1>
        
        <fieldset class="top-buffer">
          <legend>IP-adress</legend>          
          <button id="show-ip-btn" class="btn btn-link pull-right">Visa/dölj IP-information</button>          
          <div class="control-group">
            <label class="checkbox">
              <input type="checkbox" id="dhcp" <?php if (getVar('ulogger_ip_dhcp', "") == 'true') { echo "checked"; }; ?>> Använd DHCP              
            </label>
          </div>          
          <div id="fixed-ip">          
            <div class="form-inline control-group">
              <label class="input-small">IP-adress: </label>
              <input type="text" id="ip_address" value="<?php echo getVar('ulogger_ip_address', ""); ?>"> 
            </div>          
            <div class="form-inline control-group">
              <label class="input-small">Subnätmask: </label>
              <input type="text" id="ip_netmask" value="<?php echo getVar('ulogger_ip_netmask', ""); ?>">
            </div>            
            <div class="form-inline control-group">
              <label class="input-small">Gateway: </label>
              <input type="text" id="ip_gateway" value="<?php echo getVar('ulogger_ip_gateway', ""); ?>">
            </div>  
          </div>
          <button id="changeip" class="btn">Spara</button>          
          <div class="hidden alert-info top-buffer" id='show-ip'>
            <pre><?php echo getIPinfo(); ?></pre>
          </div>
        </fieldset>        

        <fieldset class="top-buffer">
          <legend>HTTP-portar</legend>
          <p>Anger vilka portar som webbservern svarar på.</p>
          <div class="form-inline control-group">
            <label>Webbserverport: </label> 80 + 
            <input type="text" id="http_port" class="input-small" size="5" value="<?php echo getVar('ulogger_http_port', ""); ?>">
          </div>            
          <button id="setport" class="btn" data-loading-text="sparar...">Ändra port</button>
        </fieldset>              
      
        <fieldset class="top-buffer">
          <legend>TightVNC Server</legend>
          <p>Fjärrstyr uLoggservern från Windows, installera klienten 
          <a href='../downloads/UltraVNC_1_1_8_X86_Setup.exe'>UltraVNC Viewer</a> (<a href='http://www.uvnc.com'>www.uvnc.com</a>).
          <ul><li>
          <?php      
            if ($ip = getFwIp()) {
              echo sprintf('Extern IP: <a href="http://%1$s:%2$s">%1$s:%2$s</a> (lösenord: %3$s)', $ip, VNC_PORT, VNC_PASS);
            }
            else {
              echo "Fel: Extern IP kunde inte detekteras";
            }      
          ?>
          </li></ul>
          </p>
        </fieldset>      
      
        <fieldset class="top-buffer">
          <legend>Avstängning/omstart av server</legend>
          <p><strong>Viktigt!</strong> Om du kopplar ur strömmen eller tar ur/sätter i SD-kortet utan att först stänga av servern kan SD-kortet sluta fungera och behöva återställas.</p>
          <button id="halt" class="btn">Stäng av</button>
          <button id="reboot" class="btn">Starta om</button>
        </fieldset>
        
      </div>
    </div>
      
    <!-- END CONTENT -->
    </div>
  </div>
  <?php include("templates/footer.tpl.php"); ?>
  <?php include("templates/scrips.tpl.php"); ?>
  <script src="ulogger/settings.js"></script>                       
  
</body>
</html>