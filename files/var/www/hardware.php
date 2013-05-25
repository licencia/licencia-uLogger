<?php

namespace raspcontrol;
spl_autoload_register();
set_include_path('ulogger');

require_once "ulogger/functions.php";

$uptime = Uptime::uptime();
$ram = Memory::ram();
$swap = Memory::swap();
$cpu = CPU::cpu();
$cpu_heat = CPU::heat();
$hdd = Storage::hdd();
$net_connections = Network::connections();
$net_eth = Network::ethernet();
$users = Users::connected();

function icon_alert($alert) {
  echo '<i class="icon-';
  switch($alert) {
    case 'success':
      echo 'ok';
      break;
    case 'warning':
      echo 'warning-sign';
      break;
    default:
      echo 'exclamation-sign';
  }
  echo '"></i>';
}

function shell_to_html_table_result($shellExecOutput) {
	$shellExecOutput = preg_split('/[\r\n]+/', $shellExecOutput);

	// remove double (or more) spaces for all items
	foreach ($shellExecOutput as &$item) {
		$item = preg_replace('/[[:blank:]]+/', ' ', $item);
		$item = trim($item);
	}

	// remove empty lines
	$shellExecOutput = array_filter($shellExecOutput);

	// the first line contains titles
	$columnCount = preg_match_all('/\s+/', $shellExecOutput[0]);
	$shellExecOutput[0] = '<tr><th>' . preg_replace('/\s+/', '</th><th>', $shellExecOutput[0], $columnCount) . '</th></tr>';
	$tableHead = $shellExecOutput[0];
	unset($shellExecOutput[0]);

	// others lines contains table lines
	foreach ($shellExecOutput as &$item) {
		$item = '<tr><td>' . preg_replace('/\s+/', '</td><td>', $item, $columnCount) . '</td></tr>';
	}

	// return the build table
	return '<table class=\'table table-striped\'>'
				. '<thead>' . $tableHead . '</thead>'
				. '<tbody>' . implode($shellExecOutput) . '</tbody>'
			. '</table>';
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
    <div class="container hardware">    
    <!-- START CONTENT -->     
    <div class="row">
      
      <div class="span3">
        <?php include("ulogger/templates/menu.left.tpl.php"); ?>
      </div>      
      
      <div class="span9">    
        <?php include("ulogger/templates/messages.tpl.php"); ?>   
        <h1 class="page-title">Systemstatus</h1>
        
        <fieldset id="check-system" class="top-buffer">
          <legend><i class="icon-cog"></i> System<span class="icon"></span></legend>
          <div class="infos">
            <p>Hostname: <span class="text-success"><?php echo Rbpi::hostname(true); ?></span></p>
            <p>Distribution: <span class="text-success"><?php echo Rbpi::distribution(); ?></span></p>
            <p>Kernel: <?php echo Rbpi::kernel(); ?></p>
            <p>Firmware: <?php echo Rbpi::firmware(); ?></p>
            <p>Uptime: <?php echo $uptime; ?></p>
            <p>Server time: <?php echo date('D F d Y h:i:s T O'); ?></p>                                     
          </div>
        </fieldset>
        
        <fieldset id="check-network" class="top-buffer">
          <legend><i class="icon-globe"></i> Network <span class="icon"><?php echo icon_alert($net_connections['alert']); ?></span></legend>                
          <div class="infos">
            <button id="show-ip-btn" class="btn btn-link pull-right">Visa/dölj IP-information</button> 
            <p>IP: <span class="text-success"><?php echo Rbpi::ip(); ?></span> &middot; Subnet: <span class="text-success"><?php echo Rbpi::subnet(); ?></span> &middot; Gateway: <span class="text-success"><?php echo Rbpi::gateway(); ?></span></p>
            <p id="extip-holder">External IP: <button id="extip" class="btn btn-link">Försök detektera extern IP ...</button></p>            
            <p>Received: <strong><?php echo $net_eth['down']; ?>Mb</strong> &middot; Sent: <strong><?php echo $net_eth['up']; ?>Mb</strong> &middot; Total: <?php echo $net_eth['total']; ?>Mb</p>
            <p>Connections: <?php echo $net_connections['connections']; ?></p>
            <div id='show-ip' class="hidden top-buffer well alert-success pre"><h5>ifconfig</h5><?php echo Rbpi::ifconfig(); ?><h5>routes</h5><?php echo Rbpi::route(); ?></div>
          </div>
        </fieldset>             
       
        <fieldset id="check-ram" class="top-buffer">
          <legend><i class="icon-asterisk"></i> RAM</legend>                
          <div class="infos">
            <div class="progress progress-striped" id="popover-ram">
                <div class="bar bar-<?php echo $ram['alert']; ?>" style="width: <?php echo $ram['percentage']; ?>%;"><?php echo $ram['percentage']; ?>%</div>
            </div>
            <div id="popover-ram-head" class="hide">Top RAM eaters</div>
            <div id="popover-ram-body" class="hide"><?php echo shell_to_html_table_result($ram['detail']); ?></div>
              Free: <span class="text-success"><?php echo $ram['free']; ?>Mb</span>  &middot; Used: <span class="text-warning"><?php echo $ram['used']; ?>Mb</span> &middot; Total: <?php echo $ram['total']; ?>Mb
          </div>            
        </fieldset>  
        
        <fieldset id="check-swap" class="top-buffer">
          <legend><i class="icon-refresh"></i> SWAP</legend>                
          <div class="infos">
            <div class="progress progress-striped">
              <div class="bar bar-<?php echo $swap['alert']; ?>" style="width: <?php echo $swap['percentage']; ?>%;"><?php echo $swap['percentage']; ?>%</div>
            </div>
            Free: <span class="text-success"><?php echo $swap['free']; ?>Mb</span>  &middot; Used: <span class="text-warning"><?php echo $swap['used']; ?>Mb</span> &middot; Total: <?php echo $swap['total']; ?>Mb
          </div>
        </fieldset>   

        <fieldset id="check-cpu" class="top-buffer">
          <legend><i class="icon-tasks"></i> CPU <span class="icon"><?php echo icon_alert($cpu['alert']); ?></span></legend>    
          <div class="infos">
            <p>Loads: <?php echo $cpu['loads']; ?> [1 min] &middot; <?php echo $cpu['loads5']; ?> [5 min] &middot; <?php echo $cpu['loads15']; ?> [15 min]</p>
            <p>Tunning at <span class="text-success"><?php echo $cpu['current']; ?></span> (min: <?php echo $cpu['min']; ?>  &middot;  max: <?php echo $cpu['max']; ?>)</p>
            <p>Governor: <strong><?php echo $cpu['governor']; ?></strong></p>
            <p><strong>Heat: </strong><span class="text-success"><?php echo $cpu_heat['degrees']; ?>°C</span></p> 
            <div class="progress progress-striped" id="popover-cpu">
              <div class="bar bar-<?php echo $cpu_heat['alert']; ?>" style="width: <?php echo $cpu_heat['percentage']; ?>%;"><?php echo $cpu_heat['percentage']; ?>%</div>
            </div>
            <div id="popover-cpu-head" class="hide">Top CPU eaters</div>
            <div id="popover-cpu-body" class="hide"><?php echo shell_to_html_table_result($cpu_heat['detail']); ?></div>
                                   
          </div>
        </fieldset>           

        <fieldset id="check-storage" class="top-buffer">
          <legend><i class="icon-hdd"></i> Storage</legend>    
          <?php for ($i=0; $i<sizeof($hdd); $i++) { ?>
          <div> <i class="icon-folder-open"></i> <?php echo $hdd[$i]['name']; ?></div>
          <div class="infos"> 
            <div class="progress progress-striped">
              <div class="bar bar-<?php echo $hdd[$i]['alert']; ?>" style="width: <?php echo $hdd[$i]['percentage']; ?>%;"><?php echo $hdd[$i]['percentage']; ?>%</div>
            </div>          
            <p>
            Free: <span class="text-success"><?php echo $hdd[$i]['free']; ?>b</span>
            &middot; Used: <span class="text-warning"><?php echo $hdd[$i]['used']; ?>b</span>
            &middot; Total: <?php echo $hdd[$i]['total']; ?>b
            &middot; Format: <?php echo $hdd[$i]['format']; ?>
            </p>
          </div>
          <?php } ?>
         </fieldset>  

        <fieldset id="check-users" class="top-buffer">
          <legend><i class="icon-user"></i> Users <span class="icon"><span class="badge"><?php echo sizeof($users); ?></span></span></legend>                
          <div class="infos">
            <ul class="unstyled">
              <?php
                if (sizeof($users) > 0) {
                  for ($i=0; $i<sizeof($users); $i++)
                    echo '<li><span class="text-success">', $users[$i]['user'] ,'</span> since ', $users[$i]['date'], ' at ', $users[$i]['hour'], ' from <strong>', $users[$i]['ip'] ,'</strong> ', $users[$i]['dns'], '</li>', "\n";
                }
                else
                  echo '<li>no user logged in</li>';
              ?>
            </ul>
          </div>
        </fieldset>  
      
      </div>
    </div>      
    <!-- END CONTENT -->
    </div>
  </div>
  <?php include("ulogger/templates/footer.tpl.php"); ?>
  <?php include("ulogger/templates/scrips.tpl.php"); ?>
  <script src="ulogger/hardware.js"></script>
</body>
</html>











 
