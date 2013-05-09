$(document).ready(function(){
  getStatus();
  $(":button").click(handleButtonClicks);  
});

/* Display log time */
var clockTicking = false;
function logTime(startTime)
{
  totalSec = Math.round(new Date().getTime() / 1000) - startTime;
  hours = parseInt(totalSec / 3600) % 24;
  minutes = parseInt(totalSec / 60) % 60;
  seconds = parseInt(totalSec % 60, 10);
  result = (hours < 10 ? "0" + hours : hours) + ":"
           + (minutes < 10 ? "0" + minutes : minutes) + ":"
           + (seconds < 10 ? "0" + seconds : seconds);
  jQuery("#log-time").html(result); 
  if (clockTicking==true) {
    setTimeout(function(){logTime(startTime);},500);
  }
};
      
function handleButtonClicks(){   
  $.ajax({
    url:"/siplogg/siplogg_server.php",                  
    type: 'POST',
    cache: false,
    data: {
      action: this.id,
      filter: $('#filter').val(),
      max_file_size: $('#max_file_size').val(),
      ring_buffer_size: $('#ring_buffer_size').val(),
      files_to_delete: $("#file-table :input").serializeArray()
    },
    dataType: 'json',
    success:function(result){  
      showMessages(result); 
      if (result.action=="start") {
        $('#running').show();
        $(".not-when-running").attr("disabled", "disabled");
        $("#current-file").text(result.filename);               
      }      
      if (result.action=="stop") {
        $('#running').hide();        
      }
      updateFileList();
    },
    complete:function(){setTimeout(getStatus, 1000);},
    error:function(xhr, ajaxOptions, thrownError){showError("Status: " + xhr.status + " (" + thrownError + ").");} 
  });
};      

function getStatus(){     
  $.ajax({
    url:"/siplogg/siplogg_server.php",                  
    type: 'POST',
    cache: false,
    data: {action: 'getstatus'},
    dataType: 'json',
    success:function(result){                   
      //showMessages(result); 
      updateStatus(result);
      setTimeout(getStatus, 5000);      
    },    
    error:function(xhr, ajaxOptions, thrownError){
      showError("Status: " + xhr.status + " (" + thrownError + ").");
      setTimeout(getStatus, 5000);
    }   
  });
}; 
      
function updateStatus(result){
  $("#max-trace-size").html("Tillgängligt loggutrymme  (" + result.fs + " av " + result.ts + " används)");
  $(".ts div").css("width", result.tp);
  $("#meter-tp").text(result.tp);
  $("#total-disk-size").html("Total diskstorlek (" + result.du + " av " + result.dt + " används)");
  $(".dt div").css("width", result.dp);
  $("#meter-dp").text(result.dp);
  if (result.running == true) {
    $('#running').show();
    $(".not-when-running").attr("disabled", "disabled");
    $("#current-file").text(result.filename);        
    if (clockTicking==false) {
      clockTicking = true;
      logTime(result.start_time);      
    };
  } else if (result.running == false) {
    $('#running').hide();
    $(".not-when-running").removeAttr("disabled");
    clockTicking = false;
  }
}

function updateFileList(){     
  $.ajax({
    url:"/siplogg/siplogg_server.php",                  
    type: 'POST',
    cache: false,
    data: {action: 'updatefilelist'},
    dataType: 'json',
    success:function(result){                   
      //showMessages(result); 
      $("#file-table").html(result.fileList);
    },    
    error:function(xhr, ajaxOptions, thrownError){showError("Status: " + xhr.status + " (" + thrownError + ").");}   
  });
}; 