$(document).ready(function(){

  // Ska ej anropas då visa/dölj klickas
  $(":button").click(handleButtonClicks);

  // Show/hid IP info
  $("#show-ip-btn").click(function() {
    $("#show-ip").hasClass('hidden') ? $("#show-ip").removeClass('hidden') : $("#show-ip").addClass('hidden');
    return false;
  });

  $('#popover-ram').popover({
    html : true,
    placement : 'bottom',
    trigger : 'hover',
    title : function() {
      return $("#popover-ram-head").html();
    },
    content : function() {
      return $("#popover-ram-body").html();
    }
  });

  $('#popover-cpu').popover({
    html : true,
    placement : 'bottom',
    trigger : 'hover',
    title : function() {
      return $("#popover-cpu-head").html();
    },
    content : function() {
      return $("#popover-cpu-body").html();
    }
  });
  
  function getDate(offset){
    var now = new Date();
    var hour = 60*60*1000;
    var min = 60*1000;
    return new Date(now.getTime() + (now.getTimezoneOffset() * min) + (offset * hour));
  }
  $("#client-date").html(getDate(2));

});

// Ajax call
function handleButtonClicks(){

  var id = this.id;

  $.ajax({
    url:"ulogger/settings_server.php",
    type: 'POST',
    dataType: 'json',
    data: {
      action: this.id
    },
    beforeSend: function(){$("#" + id).button('loading');},
  })
  .done(function(result){

      //Extern IP
    if (result.action=="extip") {
      if (!result.errorMsg) {
        $("#extip-holder").html('Extern IP: <span class="text-success">' + result.statusMsg + '</span>');
      } else {
        showMessage('<div class="pre">' + result.errorMsg + '</div>', 'error');
      }
    }    

  })
  .complete(function(){$("#" + id).button('reset');})
  .fail(function(xhr, ajaxOptions, thrownError){
    //showMessage("AJAX-fel: " + xhr.status + " (" + thrownError + ").", "error");
  });
};