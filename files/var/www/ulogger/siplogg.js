$(document).ready(function(){
  updateStatus();
  $(":button").click(handleButtonClicks);
});

/* Display log time */
var clockTicking = false;
function logTime(startTime){
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
    url:"ulogger/siplogg_server.php",
    type: 'POST',
    dataType: 'json',
    data: {
      action: this.id,
      filter: $('#filter').val(),
      max_file_size: $('#max_file_size').val(),
      ring_buffer_size: $('#ring_buffer_size').val(),
      files_to_delete: $("#file-table :input").serializeArray()
    }
  })
  .done(function(result){location.reload();})
  .fail(function(xhr, ajaxOptions, thrownError){
    //showMessage("AJAX-fel: " + xhr.status + " (" + thrownError + ").", "error");
  });
};

function updateStatus(){
  $.ajax({
    url:"ulogger/siplogg_server.php",
    type: 'POST',
    data: {action: 'getstatus'},
    dataType: 'json',
  })
  .done(function(result){
    setStatus(result);
    if (result.running == true) {
      setTimeout(updateStatus, 5000);
    }
  })
  .fail(function(xhr, ajaxOptions, thrownError){
    //showMessage("AJAX-fel: " + xhr.status + " (" + thrownError + ").", "error");
    setTimeout(updateStatus, 5000);
  });

};

function setStatus(result){
  // Disk size
  $("#disk-status h5").html("Loggutrymme " + result.tp + " (" + result.fs + " av " + result.ts + " används)");
  $("#disk-status .bar").css("width", result.tp);

  // Running
  if (result.running == true) {
    $("#disk-status .progress").addClass('active');
    $("#time-status").removeClass('hidden');
    $(".not-when-running").attr("disabled", "disabled");
    $("#current-file").text(result.filename);
    if (clockTicking==false) {
      clockTicking = true;
      logTime(result.start_time);
    };
  } else {
    $("#disk-status .progress").removeClass('active');
    $("#time-status").addClass('hidden');
    $(".not-when-running").removeAttr("disabled");
    clockTicking = false;
  }
}