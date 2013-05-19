<?php
  error_reporting(E_ALL | E_STRICT);
  
  require_once "/home/ulogger/functions.php";      
  
  // Delete all old upgrade files.
  cleanUpgradeDir();

  require_once "UploadHandler.php";  
  $upload_handler = new UploadHandler();
  
?>