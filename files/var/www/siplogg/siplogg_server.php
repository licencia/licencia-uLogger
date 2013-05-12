<?php

include_once "functions.php";

$data = array('action' => 'none'/*, 'statusMsg' => '', 'errorMsg' => ''*/);
 
if (isset($_POST['action']) && !empty($_POST['action'])) {
  $data['action'] = $_POST['action'];
  switch($data['action']) {
    case 'deleteallfiles' :
      //$data['statusMsg'] = 'Alla filer har raderats.';
      set_message('Alla filer har raderats.', 'success');
      deleteAllFiles();
      break;    
    case 'deleteselectedfiles' :
      if (!empty($_POST['files_to_delete'])) {
        $fileCount = sizeof(($_POST['files_to_delete']));
        if ($fileCount > 0) {
          //$data['statusMsg'] =  "$fileCount filer har raderats.";  
          set_message("$fileCount fil/filer har raderats.", 'success');          
          foreach ($_POST['files_to_delete'] as $key => $value) {
            unlink(TRACE_DIR . "/" . $value['value']);
          }    
        }
      }
      break;
    case 'start' :
      $data['running'] = TRUE;
      tcpdumpStart($_POST['max_file_size'], $_POST['ring_buffer_size'], $_POST['filter']);
      $data['filename'] = getVar('siplogg_filename', '');
      $data['start_time'] = getVar('siplogg_start_time', 0);
      break;
    case 'stop' :
      //$data['errorMsg'] = phpShellExec('kill_tcpdump');
      phpShellExec('kill_tcpdump');      
      break;
    /*case 'updatefilelist' :
      $data['errorMsg'] = "filelist";
      $data['fileList'] = getFileListHTML();
      break;      */
    case 'getstatus' :
      $size = getSize();
      /*$data['dt'] = $size['dt'];
      $data['df'] = $size['df'];
      $data['du'] = $size['du'];
      $data['dp'] = $size['dp'] . '%';*/
      $data['ts'] = $size['ts'];
      $data['fs'] = $size['fs'];
      $data['tp'] = $size['tp'] . '%';
      $data['running'] = tcpdumpIsRunning();
      $data['filename'] = getVar('siplogg_filename', '');
      $data['start_time'] = getVar('siplogg_start_time', 0);
      break;
  }  
}

// Krävs för att IE ska acceptera json object.
header('Content-Type: application/json');  

echo json_encode($data);