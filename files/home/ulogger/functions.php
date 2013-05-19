<?php

session_start();

 /***************************************************
 * Settings
 **************************************************/

// GLOBALS
$allowed_pages = array('index.php', 'login_server.php', 'login.php', 'logout.php');
$logged_in = isset($_SESSION['logged_in']) && ($_SESSION['logged_in'] == 'yes');
 
// uLogger
define('ULOGGER_VERSION_STRING', 'Licencia uLogger version %s, &copy; 2013.');
//define('EXT_IP_SERVER', 'http://checkip.dyndns.org');
define('VNC_PORT', '5091');
define('VNC_PASS', 'ulogger');
//Database
define('DB_NAME', 'ulogger');
define('DB_USER', 'root');
define('DB_PASS', 'ulogger');
//define('ULOGGER_USER', 'licencia');
//define('ULOGGER_PASS', 'ulogger');
//define('UPGRADE_FILE_MASK', '/var/www/FILES/uploads/*.tar.gz');
define('UPLOAD_DIR', '/var/www/FILES/uploads');
define('UPGRADE_VERSION_ID', 'ulogger-update-');


// Siplogg
define('APACHE_DIR', '/var/www');
define('TRACE_DIR', '/var/www/FILES/trace');

/***************************************************
 * Try to connect to the database
 **************************************************/
 
if (mysqli_connect_errno(@mysqli_connect("localhost", DB_USER, DB_PASS, DB_NAME))) {
  set_message("Anslutningen till MySQL misslyckades: " . mysqli_connect_error(), 'error', FALSE);
}

/***************************************************
 * Password
 **************************************************/

require_once "password.php";

function add_user($username, $password) {
  $hash = create_hash($password);
  setVar($username . '_hash', $hash);
  return $hash;
}
 
function valid_user($username, $password) {
  return validate_password($password, getVar($username . '_hash', ''));
}
 
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
  $_SESSION['last_page'] = current_page();
  header('Location:login.php');
  exit();
}
elseif ($logged_in && isset($_SESSION['last_page'])) {
  $last_page = $_SESSION['last_page'];
  unset($_SESSION['last_page']);
  header("Location:$last_page");
}
elseif ($logged_in && (current_page() == 'login.php')) {
  header("Location: http://{$_SERVER['SERVER_NAME']}/");
  exit();
}

/***************************************************
 * Database access
 **************************************************/

//require_once "/home/ulogger/secret.php";
 
function connect_to_db() {
  // Connect to database
  $con = @mysqli_connect("localhost", DB_USER, DB_PASS, DB_NAME);
  // Check connection
  if (mysqli_connect_errno($con)) {
    //echo "Failed to connect to MySQL: " . mysqli_connect_error();
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

/***************************************************
 * uLogger (common functions)
 **************************************************/

function phpShellExec($command) {
  return shell_exec("sudo /home/ulogger/phpcommands.sh $command 2>&1");
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

function printHead($pageTitle) {
  echo '<meta charset="utf-8">';
  echo '<title>' . $pageTitle . '</title>';
  include("ulogger/templates/header.tpl.php");
}

function cleanUpgradeDir() {
  $files = glob(UPGRADE_FILE_MASK);
  foreach ($files as $filename) {
    unlink($filename);
  }
  setVar('ulogger_upgrade_filename', '');
  setVar('ulogger_upgrade_version', '');  
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

/***************************************************
 * Session messages (based on Drupal messages)
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
 * Not used
 **************************************************/

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

