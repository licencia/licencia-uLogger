<?php

session_start();

 /***************************************************
 * Settings
 **************************************************/

// GLOBALS
$allowed_pages = array('index.php', 'login_server.php', 'login.php', 'logout.php');
$logged_in = isset($_SESSION['logged_in']) && ($_SESSION['logged_in'] == 'yes');
 
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
 * Logg in check
 **************************************************/

function current_page() {           
  $parts = Explode('/', $_SERVER["PHP_SELF"]);
  return $parts[count($parts) - 1];
}

function allowed_page() {           
  global $allowed_pages;    
  return in_Array(current_page(), $allowed_pages);
}

if (!$logged_in && !allowed_page()) {
  header('Location:login.php');
  exit();
}
elseif ($logged_in && (current_page() == 'login.php')) {
  header("Location: http://{$_SERVER['SERVER_NAME']}/");
  exit();
}

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
function printHead($pageTitle) {
  echo '<meta charset="utf-8">';
  echo '<title>' . $pageTitle . '</title>';
  include("templates/header.tpl.php");
}

/***************************************************
 * Messages (from drupal drupal_set_message, 
   drupal_get_messages and theme_status_messages).
 **************************************************/

function set_message($message = NULL, $type = 'status', $repeat = TRUE) {
  if ($message) {
    if (!isset($_SESSION['messages'][$type])) {
      $_SESSION['messages'][$type] = array();
    }
    if ($repeat || !in_array($message, $_SESSION['messages'][$type])) {
      $_SESSION['messages'][$type][] = $message;
    }
  }
  return isset($_SESSION['messages']) ? $_SESSION['messages'] : NULL;
}

function get_messages($type = NULL, $clear_queue = TRUE) {
  if ($messages = set_message()) {
    if ($type) {
      if ($clear_queue) {
        unset($_SESSION['messages'][$type]);
      }
      if (isset($messages[$type])) {
        return array($type => $messages[$type]);
      }
    }
    else {
      if ($clear_queue) {
        unset($_SESSION['messages']);
      }
      return $messages;
    }
  }
  return array();
}

function theme_messages() {
  $output = '';
  $status_heading = array('success' => '', 'info' => '', 'error' => 'Fel! ', 'warning' => 'Varning! ');
  foreach (get_messages() as $type => $messages) {  
    if (count($messages) > 1) {
      $output .= "<div class=\"alert alert-block alert-$type\">\n";
      $output .= '<button type="button" class="close" data-dismiss="alert">&times;</button>';
      $output .= "<h4>$status_heading[$type]</h4>";
      $output .= " <ul>\n";
      foreach ($messages as $message) {
        $output .= '  <li>' . $message . "</li>\n";
      }
      $output .= " </ul>\n";
    }
    else {
      $output .= "<div class=\"alert alert-$type\">\n";
      $output .= '<button type="button" class="close" data-dismiss="alert">&times;</button>';
      $output .= "<strong>$status_heading[$type]</strong>" . $messages[0];
    }    
    $output .= "</div>\n";
  }
  return $output;
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
    //return $pid;
    return $filename;
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
  $fileTable = "<tbody>";
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
  if ($id == 0) $fileTable .= "<tr><td>Inga sparade loggfiler ...</td></tr></tbody>";
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
  $df = 1000000; //disk_free_space(APACHE_DIR);
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

