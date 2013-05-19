<?php

require_once "/home/ulogger/functions.php";

$data = array('action' => 'none', 'statusMsg' => '', 'errorMsg' => '');

if (isset($_GET['action']) && !empty($_GET['action'])) {
  $data['action'] = $_GET['action'];
  switch($data['action']) {
    case 'upload' :
      $filename = $_GET['filename'];
      //if ($version_string = getUpgradeFile()) {
      if ($version_string = validateUpgradeFile($filename)) {            
        $data['action'] = $version_string;
        setVar('ulogger_upgrade_filename', $filename);
        setVar('ulogger_upgrade_version', $version_string);
        //set_message("En uppgradering till version $version_string har detekterats.", 'success');
      }
      else {
        set_message("$filename innehåller inte någon uppgradering.", 'warning');
        cleanUpgradeDir();
      }
      break; 
  }
}

if (isset($_POST['action']) && !empty($_POST['action'])) {
  $data['action'] = $_POST['action'];
  switch($data['action']) {
  
    /*case 'checkfile' :
      $filename = $_POST['filename'];
      $data['action'] = shell_exec("tar --test-label -f /var/www/uploads/$filename 2>&1");      
      //tar --test-label -f /var/www/uploads/ulogger.1.x-dev.tar.gz
    break;*/

    case 'extract' :
      $result = phpShellExec('extract_tar ' . UPLOAD_DIR . '/' . getVar('ulogger_upgrade_filename', ''));      
      if (strrpos($result, "ulogger-tar-error") === false) {        
        setVar('ulogger_version', getVar('ulogger_upgrade_version', ''));
        cleanUpgradeDir();
        $data['statusMsg'] = $result;        
      }
      else {        
        $data['errorMsg'] = $result;
      }      
      break;
    
    case 'changeip' :
      $dhcp = $_POST['dhcp'];
      try {
        setIP($dhcp, $_POST['ip_address'], $_POST['ip_gateway'], $_POST['ip_netmask']);
        if ($dhcp == 'true') {
          $data['statusMsg'] = "DHCP har aktiverats och eth0 startas om. Kontroller vilken IP du fått i DHCP-servern.";
        } else {
          $data['statusMsg'] = "Statisk IP har aktiverats och eth0 startas om (ny ip: <a href='http://" . $_POST['ip_address'] . "'>" . $_POST['ip_address'] . " </a>).";
        }
      }
      catch(Exception $e) {
        $data['errorMsg'] = 'Message: ' . $e->getMessage();
      }   
      break;
      
    case 'reboot' :
      $data['statusMsg'] = "uLogger startas om ... <span id='countdown'>45</span> sekunder.";
      $data['errorMsg'] = phpShellExec('reboot');
      break;
      
    case 'halt' :
      $data['statusMsg'] = "Vänta <span id='countdown'>30</span> sekunder eller tills dess att endast de båda röda lamporna lyser i uLoggservern innan du kopplar ur strömmen!";
      if ($output = phpShellExec('halt')) {
        $data['errorMsg']  = "Servermeddelande: $output";
      }
      break;
      
    case 'setport' :
      $validport = filter_var($_POST['port'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1024, 'max_range' => 65535)));
      if ($validport) {
        $port = $_POST['port'];
        $data['statusMsg'] = "Webbservern lyssnar nu på port 80 och $port.";          
        $data['errorMsg'] = setHtmlPort($port);
      }
      else {
        $data['errorMsg'] = "Felaktig port. Tillåtet portintervall är 1024 - 65535.";
      }
      break;
      
    case 'restart_eth0' :
      phpShellExec('restart_eth0');
      break;
  }  
}

// Krävs för att IE ska acceptera json object.
header('Content-Type: application/json');

echo json_encode($data);