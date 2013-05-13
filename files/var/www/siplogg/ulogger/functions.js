$(document).ready(function(){

  //Markera aktiv meny
  var pathname = window.location.pathname;
  $('#main-menu a[href="' + pathname + '"]').parent().addClass('active');
  $('#main-menu .dropdown a[href="' + pathname + '"]').parent().parent().parent().addClass('active');
  $('#left-menu a[href="' + pathname + '"]').parent().addClass('active');
 
  //Hantera knapptryckningar
  $(".login-button, .logout-button").click(handleLogin);   
});

// login/logout
function handleLogin(){  
  $.ajax({
    url:"/siplogg/login_server.php",                    
    type: 'POST',
    cache: false,
    data: {
      action: this.id,
      user: $('#user').val(),
      password: $('#password').val(),
      remember_me: $('#remember_me').prop('checked')
    },
    dataType: 'json',
    success:function(result){  
      if (result.action=="login" || result.action=="logout") {
        window.location = '/';
      } else {
        location.reload();
      }
    },
    error:function(xhr, ajaxOptions, thrownError){showError("Status: " + xhr.status + " (" + thrownError + ").");} 
  });
};

function showMessage(message, type) {
  var data = '<div class="alert alert-' + type + '">' +
             '<button data-dismiss="alert" class="close" type="button">×</button>'
             + message + '</div>';
  $('#jquery-messages').append(data);
}

/*
// Print status messages
function showStatus(message) {
  if (message) {
    $('.messages.status').show();
    $("#status-message").html(message);
  } else {
    $('.messages.status').hide();
  }  
}    

function showError(message) {
  if (message) {
    $('.messages.error').show();
    $("#error-message").html(message);
  } else {
    $('.messages.error').hide();
  }  
}    

function showMessages(result) {
  showError(result.errorMsg);
  showStatus(result.statusMsg);
};*/