<!DOCTYPE html>
<?php include_once "functions.php"; ?>
<html>
<head>
  <title>Licencia uLogger</title>
  <?php printHead(); ?>
  <script src="js/siplogg.js"></script>
</head>
<body>
<div id="page">
  <?php printHeaderMenu(2); ?>
  <div id="main"><div id="main-inner"><div role="main" class="column" id="content">
    <!-- CONTENT -->
    <img alt="" src="images/header-siplogg.png">
    <h1 class="page-title">SIP-logg</h1>
    <div class='messages status hidden'><span id="status-message">dummy</span></div>
    <div class='messages error hidden'><span id="error-message">dummy</span></div>
            
    
    
    <fieldset id="tcpdump_settings">
      <legend>Inställningar</legend>
      <span id='running' style='display: none; float: right;'><img border='0' alt='' title='' src='images/spinner.gif'></span>
      <p><label>Maximal filstorlek: </label>
      <select name='max_file_size' id='max_file_size'>
        <option value="0">Obegränsad</option>
        <option value="1">1 Mbyte</option>
        <option value="10">10 Mbyte</option>
        <option value="100">100 Mbyte</option>
      </select></p>
      <p><label>Använd ringbuffer: </label>
      <select name='ring_buffer_size' id='ring_buffer_size'>
        <option value="0">Nej</option>
        <option value="5">5 filer</option>
        <option value="10">10 filer</option>
        <option value="25">25 filer</option>
      </select></p>
      <p><label>Loggfilter: </label>
        <input type="text" name="filter" id="filter"> (t.ex. "port 5060")
      </p>
        <button id="start" class=" btn btn-primary not-when-running">Starta logg</button>
        <button id="stop">Stoppa logg</button>
        
            <div class="btn-group">
    <button class="btn">Left</button>
    <button class="btn">Middle</button>
    <button class="btn">Right</button>
    </div>
        
    </fieldset>
    
    <fieldset id="status">
      <legend>Status</legend>
      <div id="time-file">
        <div><strong>Tid:</strong> <span id="log-time">00:00:00</span></div>
        <div><strong>Fil:</strong> <span id="current-file">-</span></div>
      </div>
      <h4 id="max-trace-size"></h4>
      <div class="meter ts"><div style="width: 0%"></div></div>
      <div id="meter-tp"></div>
      <h4 id="total-disk-size"></h4>
      <div class="meter dt"><div style="width: 0%"></div></div>
      <div id="meter-dp"></div>
    </fieldset>
    
    <fieldset id="logg">
      <legend>Loggfiler</legend>
      <table id='file-table'>
        <?php echo getFileListHTML(TRACE_DIR . "/"); ?>
      </table>
      <button id="deleteallfiles" class="not-when-running">Radera alla filer</button>
      <button id="deleteselectedfiles" class="not-when-running">Radera markerade filer</button>
    </fieldset>
    
    <!-- CONTENT -->
    <?php printFooter(); ?>
  </div></div></div>
</div>
</body>
</html>