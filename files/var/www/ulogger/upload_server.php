<?php
  //error_reporting(E_ALL | E_STRICT);
  
  require_once "functions.php";      
  
  // Delete all old upgrade files.
  cleanUpgradeDir();

  require_once "../bootstrap/file-upload/UploadHandler.php";  
  $upload_handler = new UploadHandler();
  
?>