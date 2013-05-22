<?php

require_once "/home/ulogger/database.php";

session_start();

 /***************************************************
 * Settings
 **************************************************/

// GLOBALS
$allowed_pages = array('index.php', 'login_server.php', 'login.php', 'logout.php', '404.php');
$logged_in = isset($_SESSION['logged_in']) && ($_SESSION['logged_in'] == 'yes');
 
// uLogger
define('UNKNOWN', 'Unknown');
define('ULOGGER_VERSION_STRING', 'Licencia uLogger version %s, &copy; 2013.');
define('VNC_PORT', '5091');
define('VNC_PASS', 'ulogger');
define('UPGRADE_FILE_MASK', '/var/www/FILES/uploads/*.tar.gz');
define('UPLOAD_DIR', '/var/www/FILES/uploads');
define('UPGRADE_VERSION_ID', 'ulogger-update-');

// Siplogg
define('APACHE_DIR', '/var/www');
define('TRACE_DIR', '/var/www/FILES/trace');

/***************************************************
 * Try to connect to the database
 **************************************************/

/* if (mysqli_connect_errno(@mysqli_connect("localhost", DB_USER, DB_PASS, DB_NAME))) {
  set_message("Anslutningen till MySQL misslyckades: " . mysqli_connect_error(), 'error', FALSE);
}*/

/***************************************************
 * Password
 **************************************************/

require_once "password.php";

function add_user($username, $password) {
  $db_username = $username . '_hash';
  //Skapa bara en användare om användarnamnet är ledigt.
  if (getVar($db_username, '') == '') {    
    $hash = create_hash($password);
    setVar($db_username, $hash);
    return $hash;
  } 
  else
    return FALSE;
  }
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

function formatSize($bytes)
{
  $types = array( 'B', 'KB', 'MB', 'GB', 'TB' );
  for( $i = 0; $bytes >= 1024 && $i < ( count( $types ) -1 ); $bytes /= 1024, $i++ );
    return( round( $bytes, 2 ) . " " . $types[$i] );
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

?>