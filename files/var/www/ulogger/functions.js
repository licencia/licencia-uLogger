$(document).ready(function(){
  //Markera aktiv meny
  var pathname = window.location.pathname;
  $('#main-menu a[href="' + pathname + '"]').parent().addClass('active');
  $('#main-menu .dropdown a[href="' + pathname + '"]').parent().parent().parent().addClass('active');
  $('#left-menu a[href="' + pathname + '"]').parent().addClass('active');
 
  //Hantera knapptryckningar
  $(".login-button, .logout-button").click(handleLogin);   
  
  // Initiera/visa bootstrap tooltip för länkar.          
  $("a").tooltip();    
});

// login/logout
function handleLogin(){  
  $.ajax({
    url:"ulogger/login_server.php",                    
    type: 'POST',
    //cache: false,
    dataType: 'json',
    data: {
      action: this.id,
      user: $('#user').val(),
      password: $('#password').val(),
      remember_me: $('#remember_me').prop('checked')
    },    
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
  if (message) {
    var data = '<div class="alert alert-' + type + '">' +
               '<button data-dismiss="alert" class="close" type="button">×</button>'
               + message + '</div>';
    $('#jquery-messages').append(data);
  }
}