$(document).ready(function(){


  // SKa ej anropas då visa/dölj klickas
  $(":button").click(handleButtonClicks);

  // Show/hide static IP fields
  $('#dhcp').click (function () {       
    ($(this).is (':checked')) ? $('#fixed-ip').hide() : $('#fixed-ip').show();      
  });
  ($('#dhcp').prop('checked')) ? $('#fixed-ip').hide() : $('#fixed-ip').show();

  // Show/hid IP info
  $("#show-ip-btn").click(function() {
    $("#show-ip").hasClass('hidden') ? $("#show-ip").removeClass('hidden') : $("#show-ip").addClass('hidden');
    return false;
  });
  
});


// Ajax call
function handleButtonClicks(){
  
  $("#setport").button('loading');
  //$(".alert").hide();
  
  $.ajax({
    url:"/siplogg/settings_server.php",
    type: 'POST',
    cache: false,
    data: {
      action: this.id,
      port: $('#http_port').val(),
      dhcp: $('#dhcp').prop('checked'),
      ip_address: $('#ip_address').val(),
      ip_gateway: $('#ip_gateway').val(),
      ip_netmask: $('#ip_netmask').val()
    },
    dataType: 'json',
    beforeSend: function(){$('#spinner').show();},
    complete: function(){$('#spinner').hide(); },
    success:function(result){            
      showMessages(result);            
      // Change ip
      if (result.action=="changeip") {
        // Omstart måste utföras efter att meddelande visats, annars visas inte meddelandet.
        $.ajax({
          url:"/siplogg/settings_restart_eth0.php",
          type: 'POST'
        });
      }
      //Reboot
      if (result.action=="reboot") {
        var count = 45;
        var countdown = setInterval(function(){
          $('#spinner').show();
          $("span#countdown").html(count);
          if (count == 0) {
            window.location = '/siplogg/index.php';
          }
          count--;
        }, 1000);
      }
      //Set port
      if (result.action=="setport") {
        $("#setport").button('reset');
        $(".alert").html(result.statusMsg);
        $(".alert").removeClass('hidden');
      }
      //Halt
      if (result.action=="halt") {
        var count = 30;
        var countdown = setInterval(function(){
          $('#spinner').show();
          $("span#countdown").html(count);
          if (count == 0) {
            $("#status-message").html("Nu kan du koppla ur strömmen till uLoggern.");
            $('#spinner').hide();
            return false;
          }
          count--;
        }, 1000);
      }            
    },
    error:function(xhr, ajaxOptions, thrownError){showError("Status: " + xhr.status + " (" + thrownError + ").");} 
  });
};


















