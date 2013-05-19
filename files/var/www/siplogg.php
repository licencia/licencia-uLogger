<?php 

require_once "/home/ulogger/functions.php"; 

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

?>

<!DOCTYPE html>
<html>
<head>
  <?php printHead('Licencia uLogger'); ?>
</head>
<body>
  <?php include("ulogger/templates/menu.tpl.php"); ?>
  <div id="page">
    <div class="container">    
    <!-- START CONTENT -->

    <div class="row"><!--div class="row-fluid"-->
      
      <div class="span3">
        <?php include("ulogger/templates/menu.left.tpl.php"); ?>
      </div>     
      
      <div class="span9">
        <?php include("ulogger/templates/messages.tpl.php"); ?>  
        <h1 class="page-title">SIP-logg</h1>
        <div class="well well-small alert-info hidden" id="time-status">
          <div><strong>Tid:</strong> <span id="log-time">00:00:00</span></div>
          <div><strong>Fil:</strong> <span id="current-file">-</span></div>
        </div>   
        
        <div id="disk-status">
          <h5>Loggutrymme x% (x andvänds av y)</h5>
          <div class="progress progress-success progress-striped">
            <div class="bar" style="width: 40%"></div>
          </div>
        </div>
        <div class="control-group">
          <label class="form-inline">Filstorlek:
          <select id='max_file_size'>
            <option value="0">Obegränsad</option>
            <option value="1">1 Mbyte</option>
            <option value="10">10 Mbyte</option>
            <option value="100">100 Mbyte</option>
          </select>
          </label>                       
          <label class="form-inline">Ringbuffer:
          <select id='ring_buffer_size'>
            <option value="0">Nej</option>
            <option value="5">5 filer</option>
            <option value="10">10 filer</option>
            <option value="25">25 filer</option>
          </select>
          </label>          
          <label class="form-inline">Loggfilter:
            <input type="text" id="filter" placeholder="filter"><span class="help-inline">(t.ex. "port 5060")</span>      
          </label>  
        </div>        
        
        <button id="start" class="btn btn-success not-when-running">Starta logg</button>
        <button id="stop" class="btn btn-danger">Stoppa logg</button> 
      
        <fieldset class="top-buffer">
          <legend>Loggfiler</legend>
          <table id="file-table" class="table table-striped table-condensed autowidth">      
            <thead><tr><th></th><th>Filnamn</th><th>Tid</th><th>Storlek</th></tr></thead>
            <?php echo getFileListHTML(TRACE_DIR . "/"); ?>
          </table>
          <button id="deleteallfiles" class="btn not-when-running">Radera alla filer</button>
          <button id="deleteselectedfiles" class="btn not-when-running">Radera markerade filer</button>              
        </fieldset>
      </div>
      
    </div>

    <!-- END CONTENT -->
    </div>
  </div>
  <?php include("ulogger/templates/footer.tpl.php"); ?>
  <?php include("ulogger/templates/scrips.tpl.php"); ?>
  <script src="ulogger/siplogg.js"></script>
</body>
</html>