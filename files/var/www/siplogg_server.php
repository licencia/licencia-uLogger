<?php

//Load raspcontrol
namespace ulogger\raspcontrol;
spl_autoload_register();
$hdd = Storage::hdd();

//Common functions  
require_once "/home/ulogger/functions.php";

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

$data = array('action' => 'none'/*, 'statusMsg' => '', 'errorMsg' => ''*/);
 
if (isset($_POST['action']) && !empty($_POST['action'])) {
  $data['action'] = $_POST['action'];
  switch($data['action']) {
    case 'deleteallfiles' :
      set_message('Alla filer har raderats.', 'success');
      deleteAllFiles();
      break;    
    case 'deleteselectedfiles' :
      if (!empty($_POST['files_to_delete'])) {
        $fileCount = sizeof(($_POST['files_to_delete']));
        if ($fileCount > 0) {
          set_message("$fileCount fil/filer har raderats.", 'success');          
          foreach ($_POST['files_to_delete'] as $key => $value) {
            unlink(TRACE_DIR . "/" . $value['value']);
            $data['action'] = TRACE_DIR . "/" . $value['value'];
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
      phpShellExec('kill_tcpdump');      
      break;
    case 'getstatus' :
      //$size = getSize();
      $data['ts'] = $hdd[0]['total']; //$size['ts'];
      $data['fs'] = $hdd[0]['used']; //$size['fs'];
      $data['tp'] = $hdd[0]['percentage'] . '%'; //$size['tp'] . '%';
      $data['running'] = tcpdumpIsRunning();
      $data['filename'] = getVar('siplogg_filename', '');
      $data['start_time'] = getVar('siplogg_start_time', 0);
      break;
  }  
}

// Krävs för att IE ska acceptera json object.
header('Content-Type: application/json');  

echo json_encode($data);