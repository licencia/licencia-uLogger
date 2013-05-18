<!DOCTYPE html>
<?php require_once "/home/ulogger/functions.php"; ?>
<html>
<head>
  <?php printHead('Licencia uLogger'); ?>
  <link rel="stylesheet" href="file-upload/jquery.fileupload-ui.css">
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
        <?php include("templates/messages.tpl.php"); ?>   
        <h1 class="page-title">Serverinställningar</h1>
                
        <fieldset class="top-buffer">
          <legend>IP-adress</legend>          
          <button id="show-ip-btn" class="btn btn-link pull-right">Visa/dölj IP-information</button>          
          <div class="control-group">
            <label class="checkbox">
              <input type="checkbox" id="dhcp" <?php if (getVar('ulogger_ip_dhcp', "") == 'true') { echo "checked"; }; ?>> Använd DHCP 
              (<a href="#" id="ip-info-tooltip" rel="tooltip" data-html="true" title="<?php echo getIPtooltip(); ?>">aktuell IP</a>).
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
          <button id="changeip" class="btn">Ändra IP-adress</button>          
          <div id='show-ip' class="hidden top-buffer well alert-success pre"><?php echo getIPinfo(); ?></div>
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
          <p>Fjärrstyr uLoggser från Windows genom att installera klienten 
          <a href='../downloads/UltraVNC_1_1_8_X86_Setup.exe'>UltraVNC Viewer</a> 
           och anslut mot adressen <?php echo getFwIpHtml(); ?>.
            Mer information om VNC-klienten finns på sidan <a href='http://www.uvnc.com'>www.uvnc.com</a>.
          </p>
        </fieldset>   

        <fieldset class="top-buffer">
        <legend>Uppgradera uLogger</legend>
          <p>?</p>
          
          <span class="btn btn-success fileinput-button">
            <i class="icon-plus icon-white"></i>
            <span>Välj fil ...</span>
            <input id="fileupload" type="file" multiple="" name="files[]">
          </span>          
          
          <div id="progress" class="progress progress-success progress-striped top-buffer">
              <div class="bar" style="width: 2%;"></div>
          </div>   
          <p id="files"></p>
        </fieldset> 
      
        <fieldset class="top-buffer">
          <legend>Avstängning/omstart av server</legend>
          <p><span class="label label-important">Varning!</span> Om du kopplar ur strömmen eller tar ur/sätter i SD-kortet 
          utan att först stänga av servern kan SD-kortet sluta fungera och behöva återställas.</p>
          <button id="halt" class="btn btn-inverse">Stäng av</button>
          <button id="reboot" class="btn btn-inverse">Starta om</button>
          <a href="#" id="version-btn" class="btn btn-info pull-right" rel="popover" 
          data-content="<?php echo sprintf(ULOGGER_VERSION_STRING, getVar('ulogger_version', '')); ?>">Visa version</a>  
        </fieldset>
        
      </div>
    </div>
      
    <!-- END CONTENT -->
    </div>
  </div>
  <?php include("templates/footer.tpl.php"); ?>
  <?php include("templates/scrips.tpl.php"); ?>
  <script src="file-upload/jquery.iframe-transport.js"></script>
  <script src="file-upload/jquery.fileupload.js"></script>
  <script src="ulogger/settings.js"></script>                       
  
</body>
</html>