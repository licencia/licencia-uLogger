<?php

/***************************************************
 * Logg in check
 **************************************************/
session_start();

function is_login_page() {           
  $parts = Explode('/', $_SERVER["PHP_SELF"]);
  return ('login.php' == $parts[count($parts) - 1]);
}

// Check if the user is logged in. If not redirect to login.php.
if(!isset($_SESSION['login_user']) && (!is_login_page())){    
  header('Location:login.php?pagename='.basename($_SERVER['PHP_SELF'], ".php"));
}

 /***************************************************
 * Settings
 **************************************************/
 
 // uLogger
define('ULOGGER_VERSION', '1.x-dev');
define('ULOGGER_VERSION_STRING', 'Version ' . ULOGGER_VERSION . ' (Databas %s), maj 2013, Olle Sjögren, Licencia telecom ab');
define('EXT_IP_SERVER', 'http://checkip.dyndns.org');
define('VNC_PORT', '5091');
define('VNC_PASS', 'dude12');
define('DB_NAME', 'ulogger');
define('DB_USER', 'root');
define('DB_PASS', 'test');

// Siplogg
define('APACHE_DIR', '/var/www');
define('TRACE_DIR', '/var/www/trace');


/***************************************************
 * uLogger
 **************************************************/

/**
 * restart_apache
 * reload_apache
 * reboot
 * halt
 * kill_tcpdump
 * restart_eth0
 */
function phpShellExec($command) {
  return shell_exec("sudo /home/ulogger/phpcommands.sh $command 2>&1");
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

function replaceLineInFile($filename, $pattern, $newline) {
  $contents = file_get_contents($filename);
  $lines = file($filename);
  if ($matches = preg_grep($pattern, $lines)) {
    reset($matches); //Get first value
    $new_contents = str_replace($lines[key($matches)], $newline, $contents);
    file_put_contents($filename, $new_contents);
  }
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

function connect_to_db() {
  // Connect to database
  $con = mysqli_connect("localhost", DB_USER, DB_PASS, DB_NAME);
  // Check connection
  if (mysqli_connect_errno($con)) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    return FALSE;
  } else {
    return $con;
  }
}

function setVar($name, $value) {
  if ($con = connect_to_db()) {
    // Create or update
    $command = "INSERT INTO data (name, value) VALUES('" . $name . "', '" . $value . "') ON DUPLICATE KEY UPDATE value = '" . $value . "';";
    mysqli_query($con, $command);
    mysqli_close($con);
  }
}

function getVar($name, $value = "") {
  if ($con = connect_to_db()) {
    $result = mysqli_query($con,"SELECT value FROM data WHERE name = '" . $name. "'");
    if ($row = mysqli_fetch_array($result)) {
      return $row['value'];
    }
    else {
      return $value;
    }
    mysqli_close($con);
  }
}

function getFwIp() {
  if ($extip = @file_get_contents(EXT_IP_SERVER)) {
    $extip = strip_tags($extip);
    return trim(substr($extip, strpos($extip, ':')+1));
  }
  else {
    return FALSE; 
  }
}

function getIPinfo() {
  $ip_address = shell_exec("/sbin/ifconfig eth0 | grep 'inet addr' | awk -F: '{print $2}' | awk '{print $1}'");
  $ip_netmask = shell_exec("/sbin/ifconfig eth0 | grep 'inet addr' | awk -F: '{print $4}' | awk '{print $1}'");
  $ip_gateway = shell_exec("/sbin/route -n | grep '^0.0.0.0' | awk '{print $2}'");
  $output = "IP-adress: " . $ip_address . "";
  $output .= "Subnätmask: " . $ip_netmask;
  $output .= "Gateway: " . $ip_gateway . "\n";
  $output .= shell_exec('/sbin/ifconfig && /sbin/route -n');
  return $output;
}

/**
 * Print HTML
 */
function printHead() {
  //echo '<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">';
  echo '<link href="css/licencia2013.css" rel="stylesheet" type="text/css">';
  echo '<link href="css/siplogg.css" rel="stylesheet" type="text/css">';
  echo '<meta name="HandheldFriendly" content="true" />';
  echo '<meta name="viewport" content="width=device-width, height=device-height, user-scalable=no" />';
  echo '<meta charset="UTF-8" />';
  echo '<script src="js/functions.js"></script>';
  //echo '<script src="js/bootstrap.min.js"></script>';
  echo '<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>' . "\n";
}

function printHeaderMenu($active = 0) {
  $activelist[1] = "";
  $activelist[2] = "";
  $activelist[3] = "";
  $activelist[4] = "";
  $activelist[5] = "";
  $activelist[$active] = "active";
  echo '<header role="banner" id="header"><div id="header-inner"><div id="header-inner-left"><a title="Licencia.se" href="/" rel="home" class="image-link" id="logo">www.licencia.se</a></div><div id="navigation"><div id="navigation-inner"><div id="navigation-inner-right"></div><div id="navigation-inner-left"><nav role="navigation" id="main-menu"><ul class="menu">';
  echo "<li class='leaf $activelist[1]'><a href='/siplogg/index.php'>Hem</a></li>";
  echo "<li class='leaf $activelist[2]'><a href='/siplogg/siplogg.php'>SIP-logg</a></li>";
  echo "<li class='leaf $activelist[3]'><a href='/siplogg/settings.php'>Inställningar</a></li>";
  echo "<li class='leaf $activelist[4]'><a href='login.php?ch=logout'>Logout</a></li>";
  
  echo '</ul></nav></div></div></div></div></header>';
  echo '<div id="spinner" class="spinner" style="display:none;"><img id="img-spinner" src="images/spinner.gif" alt="Loading"/></div>';
}

function printFooter() {
  echo '<div id="footer"><div id="links"><a href="/kontakt1">Uppgraderingar</a> | <a href="/sitemap2">Dokumentation</a></div>';
  echo '<div id="company"><a target="_blank" href="http://www.licencia.se" title="www.licencia.se">Licencia Telecom AB</a> generalagent för <a target="_blank" href="http://www.ericssonlg.com" title="www.ericssonlg.com">Ericsson-LG</a> i Sverige och Baltikum.</div></div>';
}

/***************************************************
 * Siplogg
 **************************************************/

function tcpdumpIsRunning() {
  return 0 < shell_exec('pidof tcpdump 2>&1');
}

/**
 * $command = " sudo /usr/sbin/tcpdump -i eth0 -Z www-data -C 1 -W 5 -vvv -s 0 -w  /var/www/trace/$filename ";
 * -i eth0: Interface
 * -C 1: Maximal filstorlek
 * -W 5: Antal filer i ringbuffert
 * -Z www-data: Kör Tcpdump som denna grupp och användare
 * -z gzip: Komprimera filer om ringebuffert eller maximal filstorlek används. Komprimerar den tidigare filen då lagring till ny påbörjas. Den sista filen komprimeras aldrig.
 */
function tcpdumpStart($max_file_size = 0, $ring_buffer_size = 0, $filter = "") {
  if (!tcpdumpIsRunning()) {
    ($max_file_size > 0) ? $max_file = "-C " . $max_file_size : $max_file = ""; // 1 = 1Mb
    ($ring_buffer_size > 0) ? $ring_buffer = "-W " . $ring_buffer_size : $ring_buffer = "";
    $filename = date('Ymd-His') . ".pcap";
    $path = TRACE_DIR . "/" . $filename;

    // Save filename and time to database
    setVar('siplogg_filename', $filename);
    setVar('siplogg_start_time', time());
    setVar('siplogg_filesize', $max_file_size);
    setVar('siplogg_ring_buffer', $ring_buffer_size);
    setVar('siplogg_filter', $filter);

    // Start Tcpdump
    $command = "sudo /usr/sbin/tcpdump -i eth0 -Z www-data $max_file $ring_buffer -vvv -s 0 -w $path $filter";
    $pid = exec("nohup $command > /dev/null 2>&1 & echo $!");
    return $pid;
  }
}

function deleteAllFiles() {
  $files = glob(TRACE_DIR . "/*"); // get all file names
  foreach($files as $file){ // iterate files
    if(is_file($file))
      unlink($file); // delete file
  }
}

// Open a known directory, and proceed to read its contents
function getFileList($dir) {
  if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
      while (($file = readdir($dh)) !== false) {
        $files[$file] = array('type' => filetype($dir . $file),
                              'name' => $file,
                              'size' => filesize($dir . $file),
                              'date' => date ("F d H:i", filemtime($dir . $file)) );
      }
      closedir($dh);
    }
  }
  asort($files);
  return $files;
}

function getFileListHTML() {
  $files = getFileList(TRACE_DIR . "/");
  //$fileTable = "<table id='file-table'>";
  $fileTable = "";
  $id = 0;
  foreach ($files as $file) {
    if ($file['type'] == 'file') {
      $fileTable .= "<tr><td class='file'><input type='checkbox' value='" . $file['name'] . "' name='file_" . $id . "'></td>"
                  . "<td><a href=../trace/" . $file['name'] . ">" . $file['name'] . "</a></td>"
                  . "<td>" .  $file['date'] . "</td>"
                  . "<td>" .  formatSize($file['size']) . "</td></tr>";
      $id += 1;
    }
  }
  if ($id == 0) $fileTable .= "<tr><td>Inga sparade loggfiler ...</td></tr>";
  //$fileTable .= "</table>";
  return $fileTable;
}

function filesize_recursive($path){
  if(!file_exists($path)) return 0;
  if(is_file($path)) return filesize($path);
  $ret = 0;
  foreach(glob($path."/*") as $fn)
  $ret += filesize_recursive($fn);
  return $ret;
}

function formatSize($bytes)
{
  $types = array( 'B', 'KB', 'MB', 'GB', 'TB' );
  for( $i = 0; $bytes >= 1024 && $i < ( count( $types ) -1 ); $bytes /= 1024, $i++ );
    return( round( $bytes, 2 ) . " " . $types[$i] );
}

function getSize() {
  /* get disk space free (in bytes) */
  $df = disk_free_space(APACHE_DIR);
  /* and get disk space total (in bytes)  */
  $dt = disk_total_space(APACHE_DIR);
  /* now we calculate the disk space used (in bytes) */
  $du = $dt - $df;
  /* percentage of disk used - this will be used to also set the width % of the progress bar */
  $dp = sprintf('%.2f',($du / $dt) * 100);
  /* size of trace directory */
  $fs = filesize_recursive(TRACE_DIR);
  /* max trace file space */
  $ts = $df + $fs;
  /* procentage of trace storage used */
  $tp = sprintf('%.2f',($fs / $ts) * 100);
  return array('df' => formatSize($df), 'dt' => formatSize($dt), 'du' => formatSize($du), 'dp' => $dp, 'ts' => formatSize($ts), 'fs' => formatSize($fs), 'tp' => $tp);
}

