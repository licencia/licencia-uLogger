$(document).ready(function(){

  // Ska ej anropas då visa/dölj klickas
  $(":button").click(handleButtonClicks);

  // Show/hide static IP fields
  $('#dhcp').click (function () {
    ($(this).is (':checked')) ? $('#fixed-ip').hide() : $('#fixed-ip').show();
  });
  ($('#dhcp').prop('checked')) ? $('#fixed-ip').hide() : $('#fixed-ip').show();

  // Ladda upp en fil
  $("#fileupload").click(initUploads);

  // Jämför lösenord
  $("#changepassword").attr("disabled", "disabled");
  $("#password, #password2").keyup(function() {    
    if ($("#password").val() == $("#password2").val()) {
      $("#password-check").html('<span class="text-success"> OK</span>');
      $("#changepassword").removeAttr("disabled");
    } else {
      $("#password-check").html('<span class="text-error"> Matchar ej</span>');
      $("#changepassword").attr("disabled", "disabled");
    };  
  }); 
  
});

function initUploads(){
  $('#fileupload').fileupload({
    url: 'ulogger/upload_server.php',
    dataType: 'json',
    beforeSend: function(){$("#progress").removeClass('hidden');},
    done: function (e, data) {
      console.log( data.result.files);
      $.getJSON('ulogger/settings_server.php', { action: 'upload', filename: data.result.files['0'].name }, function(data) {
        console.log( data.action);
        location.reload();
      });
    },
    progressall: function (e, data) {
    var progress = parseInt(data.loaded / data.total * 100, 10);
    $('#progress .bar').css(
      'width',
      progress + '%'
    );
    }
  });
};

// Ajax call
function handleButtonClicks(){

  var id = this.id;

  $.ajax({
    url:"ulogger/settings_server.php",
    type: 'POST',
    dataType: 'json',
    data: {
      action: this.id,
      port: $('#http_port').val(),
      dhcp: $('#dhcp').prop('checked'),
      password: $('#password').val(),
      ip_address: $('#ip_address').val(),
      ip_gateway: $('#ip_gateway').val(),
      ip_netmask: $('#ip_netmask').val()
    },
    beforeSend: function(){$("#" + id).button('loading');},
  })
  .done(function(result){
  
    //Update password
    if (result.action=="changepassword") {
      window.location = '/login.php';
    }  
    
    //Install upgrade
    if (result.action=="extract") {
      if (!result.errorMsg) {
        $("#extract").addClass('hidden');
        $('.modal-body').html('<h4>Installerade filer</h4><pre>' + result.statusMsg + '</pre>');
        $('#myModal').modal('show');
      } else {
        showMessage('<div class="pre">' + result.errorMsg + '</div>', 'error');
      }
    }

    // Change ip
    if (result.action=="changeip") {
      if (!result.errorMsg) {
        showMessage(result.statusMsg, 'success');
        // Starta om eth0.
        $.ajax({
          url:"ulogger/settings_server.php",
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
            window.location = '/index.php';
          }
          count--;
        }, 1000);
      } else {
        showMessage(result.errorMsg, 'error');
      }
    }

  })
  .complete(function(){$("#" + id).button('reset');})
  .fail(function(xhr, ajaxOptions, thrownError){
    //showMessage("AJAX-fel: " + xhr.status + " (" + thrownError + ").", "error");
  });
};