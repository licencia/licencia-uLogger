<!DOCTYPE html>
<?php include_once "functions.php"; ?>
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
  <?php printHeaderMenu(3); ?>
  <div id="main"><div id="main-inner"><div role="main" class="column" id="content">
    <!-- CONTENT -->
    <img alt="" src="images/header-settings.png">
    <h1 class="page-title">Serverinställningar</h1>
    <div class='messages status hidden'><span id="status-message">dummy</span></div>
    <div class='messages error hidden'><span id="error-message">dummy</span></div>
    
    <fieldset id="settings">
      <legend>IP-adress</legend>
      <p>
        <label>Använd DHCP:</label>
        <input type="checkbox" id="dhcp" <?php if (getVar('ulogger_ip_dhcp', "") == 'true') { echo "checked"; }; ?>>
        <a id='show-ip-link' href="">Visa/dölj IP-information</a>
      </p>
      <div id="fixed-ip"><p>
        <div><label>IP-adress: </label><input type="text" id="ip_address" value="<?php echo getVar('ulogger_ip_address', ""); ?>"></div>
        <div><label>Subnätmask: </label><input type="text" id="ip_netmask" value="<?php echo getVar('ulogger_ip_netmask', ""); ?>"></div>
        <div><label>Gateway: </label><input type="text" id="ip_gateway" value="<?php echo getVar('ulogger_ip_gateway', ""); ?>"></div>
      </p></div>
      <button id="changeip">Spara</button>
      <div id='show-ip'><pre><?php echo getIPinfo(); ?></pre></div>
    </fieldset>

    <fieldset id="port">
      <legend>HTTP-portar</legend>
      <p>Anger vilka portar som webbservern svarar på.</p>
      <p><label>Webbserverport: </label>80 + <input type="text" id="http_port" size="5" value="<?php echo getVar('ulogger_http_port', ""); ?>"></p>
      <button id="setport">Ändra port</button>
    </fieldset>

    <fieldset id="vnc">
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

    <fieldset id="turn-off-button">
      <legend>Avstängning/omstart av server</legend>
      <p><strong>Viktigt!</strong> Om du kopplar ur strömmen eller tar ur/sätter i SD-kortet utan att först stänga av servern kan SD-kortet sluta fungera och behöva återställas.</p>
      <button id="halt">Stäng av</button>
      <button id="reboot">Starta om</button>
    </fieldset>
    
    <fieldset id="turn-off-button">
      <legend>Uppgradera</legend>
      <div id="fine-uploader"></div> 
      <span id="upgrade-to">Upgrade to</span>      
    </fieldset>

    <p id="version"><?php echo sprintf(ULOGGER_VERSION_STRING, getVar('ulogger_version', '')); ?></p>

    <!-- CONTENT -->
    <?php printFooter(); ?>
  </div></div></div>
</div>
</body>
</html>