$(document).ready(function(){

  // Ska ej anropas då visa/dölj klickas
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

  // Initiera Tooltip för denna sida.          
  $("a").tooltip({ 'selector': '', 'placement': 'bottom' });  

  // Initiera en popover
  $("#version-btn").popover({ 'selector': '', 'placement': 'bottom' });  

});



// Ajax call
function handleButtonClicks(){
  
  var id = this.id;
  
  $.ajax({
    url:"/siplogg/settings_server.php",
    type: 'POST',
    //cache: false,
    dataType: 'json',
    data: {
      action: this.id,
      port: $('#http_port').val(),
      dhcp: $('#dhcp').prop('checked'),
      ip_address: $('#ip_address').val(),
      ip_gateway: $('#ip_gateway').val(),
      ip_netmask: $('#ip_netmask').val()
    },    
    beforeSend: function(){$("#" + id).button('loading');},
    complete: function(){$("#" + id).button('reset');},
    success:function(result){  
   
      // Change ip
      if (result.action=="changeip") {
        if (!result.errorMsg) {
          showMessage(result.statusMsg, 'success');
          // Starta om eth0.
          $.ajax({
            url:"/siplogg/settings_server.php",
            type: 'POST',
            data: {action: 'restart_eth0'}
          });          
        } else {
          showMessage(result.errorMsg, 'error');
        }                
      }
      
      //Set port
      if (result.action=="setport") {
        if (!result.errorMsg) {
          showMessage(result.statusMsg, 'success');
        } else {
          showMessage(result.errorMsg, 'error');
        }                
      }

      //Halt
      if (result.action=="halt") {
        if (!result.errorMsg) {
          showMessage(result.statusMsg, 'warning');
          var count = 30;
          var countdown = setInterval(function(){
            $("span#countdown").html(count);
            if (count == 0) {
              showMessage("Nu kan du koppla ur strömmen till uLoggern.", 'success');
              clearTimeout(countdown);
            }
            count--;
          }, 1000);
        } else {
          showMessage(result.errorMsg, 'error');
        }                
      }
      
      //Reboot
      if (result.action=="reboot") {
        if (!result.errorMsg) {
          showMessage(result.statusMsg, 'success');
          var count = 45;
          var countdown = setInterval(function(){
            $("span#countdown").html(count);
            if (count == 0) {
              window.location = '/siplogg/index.php';
            }
            count--;
          }, 1000);
        } else {
          showMessage(result.errorMsg, 'error');
        }                
      }      
      
    },
    error:function(xhr, ajaxOptions, thrownError){showError("Status: " + xhr.status + " (" + thrownError + ").");} 
  });
};