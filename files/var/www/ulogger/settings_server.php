<?php

require_once "functions.php";

function validateUpgradeFile($filename) {
  $version_string = phpShellExec("get_tar_comment " . UPLOAD_DIR . '/' . $filename);  
  // Check  if the file includs a valid version comment.  
  $pos = strpos($version_string, UPGRADE_VERSION_ID);
  if ($pos === false) {    
    return false;
  }
  else {
    $version_string = str_replace(UPGRADE_VERSION_ID, "", $version_string);
    return $version_string;
  }   
}

function setIP($dhcp, $ip_address = '', $ip_gateway = '', $ip_netmask = '') {
  if ($dhcp == 'true') {
    // Set dhcp
    setVar('ulogger_ip_dhcp', 'true');
    $config = "iface eth0 inet dhcp\n";
  }
  else {
    // Validera IP
    $error = '';
    $ip_address = $_POST['ip_address'];
    $ip_gateway = $_POST['ip_gateway'];
    $ip_netmask = $_POST['ip_netmask'];
    if (!filter_var($ip_address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) { $error .= "IP-adress "; }
    if (!filter_var($ip_gateway, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) { $error .= "gateway "; }
    if (!filter_var($ip_netmask, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) { $error .= "nätmask "; }
    // Set static IP
    if (empty($error)) {
      setVar('ulogger_ip_dhcp', 'false');
      setVar('ulogger_ip_address', $ip_address);
      setVar('ulogger_ip_gateway', $ip_gateway);
      setVar('ulogger_ip_netmask', $ip_netmask);
      $config = "iface eth0 inet static\naddress $ip_address\ngateway $ip_gateway\nnetmask $ip_netmask\n";
    }
    else {
      throw new Exception("Felaktig: " . $error);
    }  
  }
  file_put_contents("/home/ulogger/interfaces.d", $config);    
  return true;      
} 

function setHtmlPort($port) {
  // Update virtual hosts
  $filename = '/etc/apache2/sites-available/default';
  $pattern = "'<virtualhost'si";
  $newline = "<VirtualHost *:80 *:" . $port . ">\n";
  replaceLineInFile($filename, $pattern, $newline);
  // Update ports
  file_put_contents('/etc/apache2/myports.conf', "Listen " . $port);
  setVar('ulogger_http_port', $port);
  // Reload Apache  
  return phpShellExec('reload_apache');
}

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