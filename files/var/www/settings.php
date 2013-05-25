<?php   
  //Load raspcontrol
  namespace raspcontrol;
  spl_autoload_register();
  set_include_path('ulogger');
  
  require_once "ulogger/functions.php";
?>

<!DOCTYPE html>
<html>
<head>
  <?php printHead('Licencia uLogger'); ?>
  <link rel="stylesheet" href="bootstrap/file-upload/jquery.fileupload-ui.css">
</head>
<body>
  <?php include("ulogger/templates/menu.tpl.php"); ?>
  <div id="page">
    <div class="container">    
    <!-- START CONTENT -->
    
    <div class="row">
      
      <div class="span3">
        <?php include("ulogger/templates/menu.left.tpl.php"); ?>
      </div>      
      
      <div class="span9">       
        <?php include("ulogger/templates/messages.tpl.php"); ?>   
        <h1 class="page-title">Serverinställningar</h1>

        <fieldset class="top-buffer">
          <legend>Administratörslösenord</legend> 
          <p>Ange nytt lösenord för användaren <strong><?php echo getVar('ulogger_admin_user', "admin"); ?></strong>.</p>
          <div id="change-password">          
            <div class="form-inline control-group">
              <label class="input-small">Lösenord: </label>
              <input type="password" id="password" placeholder="Nytt lösenord">
              <input type="password" id="password2" placeholder="Upprepa lösenord">
              <span id="password-check"></span>
            </div>            
          </div>
          <button id="changepassword" class="btn">Spara lösenord</button>     
        </fieldset>      

        <fieldset class="top-buffer">
          <legend>IP-adress</legend> 
          <p>Ange IP-adress för uLogger.</p>
          <div class="control-group">
            <label class="checkbox">
              <input type="checkbox" id="dhcp" <?php if (getVar('ulogger_ip_dhcp', "") == 'true') { echo "checked"; }; ?>> Använd DHCP 
              (<a href="#" id="ip-info-tooltip" rel="tooltip" data-html="true" title="<?php echo Rbpi::ip(); ?>">aktuell IP</a>).
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
        </fieldset>        

        <fieldset class="top-buffer">
          <legend>HTTP-portar</legend>
          <p>Ange vilka portar som webbservern svarar på. Om du vill nå uLogger externt måste port 
          <span class="text-success">80</span> eller <span class="text-success"><?php echo getVar('ulogger_http_port', ""); ?></span> vara styrd till <span class="text-success"><?php echo Rbpi::ip(); ?></span> i kundens brandvägg.</p>
          <div class="form-inline control-group">
            <label>Webbserverport: </label> 80 + 
            <input type="text" id="http_port" class="input-small" size="5" value="<?php echo getVar('ulogger_http_port', ""); ?>">
          </div>     
          <button id="setport" class="btn" data-loading-text="sparar...">Ändra port</button>
        </fieldset>              
      
        <fieldset class="top-buffer">
          <legend>TightVNC Server</legend>
          <p>Fjärrstyr uLogger från Windows genom att installera klienten 
          <a href='http://www.uvnc.com'>UltraVNC Viewer</a> och anslut sedan mot           
          <span class="text-success">http://extern-ip:<?php echo VNC_PORT; ?></span> (lösenord: <?php echo VNC_PASS; ?>)
          </p>
          <p>Om du vill nå VPN-servern externt måste port <span class="text-success"><?php echo VNC_PORT; ?></span> vara styrd till <span class="text-success"><?php echo Rbpi::ip(); ?></span> i kundens brandvägg.</p>
        </fieldset>   

        <fieldset class="top-buffer">
        <legend>Uppgradera uLogger</legend>
          <p>Ladda upp en uppgradering. Om uppgraderingen är giltig visas en uppgraderingsknapp som startar själva uppgraderingen.</p>          
          <div id="progress" class="progress progress-success progress-striped top-buffer2 hidden">
              <div class="bar" style="width: 0%;"></div>
          </div>             
          <span class="btn btn-success fileinput-button">
            <i class="icon-upload icon-white"></i>
            <span>Ladda upp ...</span>
            <input id="fileupload" type="file" multiple="" name="files[]">
          </span>  
          
          <?php if (getVar('ulogger_upgrade_filename', '') != ''): ?> 
          <button id="extract" class="btn btn-danger">
            <i class="icon-play icon-white"></i>
            <span>Uppgradera till version <?php echo getVar('ulogger_upgrade_version', ''); ?></span>            
          </button>     
          <?php endif; ?>  

          <!-- Modal upgrade info -->
          <div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
              <h3 id="myModalLabel">Uppgradering klar</h3>
            </div>
            <div class="modal-body"></div>
            <!--div class="modal-footer">
              <button class="btn" data-dismiss="modal" aria-hidden="true">OK</button>    
            </div-->
          </div>          
        </fieldset>         
      
        <fieldset class="top-buffer">
          <legend>Avstängning/omstart av server</legend>
          <p><span class="label label-important">Varning!</span> Om du kopplar ur strömmen eller tar ur/sätter i SD-kortet 
          utan att först stänga av servern kan SD-kortet sluta fungera och behöva återställas.</p>
          <button id="halt" class="btn btn-inverse">Stäng av</button>
          <button id="reboot" class="btn btn-inverse">Starta om</button>
        </fieldset>
        
      </div>
    </div>
      
    <!-- END CONTENT -->
    </div>
  </div>
  <?php include("ulogger/templates/footer.tpl.php"); ?>
  <?php include("ulogger/templates/scrips.tpl.php"); ?>
  <script src="bootstrap/file-upload/jquery.iframe-transport.js"></script>
  <script src="bootstrap/file-upload/jquery.fileupload.js"></script>
  <script src="ulogger/settings.js"></script>                       
  
</body>
</html>