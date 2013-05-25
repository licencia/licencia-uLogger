$(document).ready(function(){
  getStatus();
  $(":button").click(handleButtonClicks);
});

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
  .done(function(result){location.reload();});
};

function getStatus(){
  $.ajax({
    url:"ulogger/siplogg_server.php",
    type: 'POST',
    data: {action: 'getstatus'},
    dataType: 'json',
  })
  .done(function(result){
    setStatus(result);
    if (result.running == true) {
      $('#log-duration').stopwatch({
        format: '{d? dagar och} {h? timmar och} {m} min {s} sek', 
        startTime: (result.log_duration * 1000)
      }).stopwatch('start');
      $("#current-file").text(result.filename);
      $("#disk-status .progress").addClass('active');
      $("#time-status").removeClass('hidden');
      $(".not-when-running").attr("disabled", "disabled");    
      setTimeout(updateStatus, 5000);
    }
    else if ($("#log-duration").stopwatch()) {
      $("#log-duration").stopwatch('reset');
      $("#disk-status .progress").removeClass('active');
      $("#time-status").addClass('hidden');    
      $(".not-when-running").removeAttr("disabled");
    }         
  });
};

function updateStatus(){
  $.ajax({
    url:"ulogger/siplogg_server.php",
    type: 'POST',
    data: {action: 'updatestatus'},
    dataType: 'json',
  })
  .done(function(result){
    setStatus(result);
    if (result.running == true) {setTimeout(updateStatus, 5000);}
  })
  .fail(function(xhr, ajaxOptions, thrownError){setTimeout(updateStatus, 5000);});
};

function setStatus(result){
  $("#disk-status h5").html("Loggutrymme " + result.tp + " (" + result.fs + " av " + result.ts + " används)");
  $("#disk-status .bar").css("width", result.tp);
};    