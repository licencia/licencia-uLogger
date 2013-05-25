$(document).ready(function(){
  //Markera aktiv meny
  var pathname = window.location.pathname;
  $('#main-menu a[href="' + pathname + '"]').parent().addClass('active');
  $('#main-menu .dropdown a[href="' + pathname + '"]').parent().parent().parent().addClass('active');
  $('#left-menu a[href="' + pathname + '"]').parent().addClass('active');

  //Hantera knapptryckningar
  $(".login-button, .logout-button").click(handleLogin);
  
  $(document).keypress(function(e) {
    if(e.which == 13) {
        handleLogin();
    }
  });

  // Initiera/visa bootstrap tooltip för länkar.
  $("a").tooltip();
});

// login/logout
function handleLogin(){
  $.ajax({
    url:"ulogger/login_server.php",
    type: 'POST',
    dataType: 'json',
    data: {
      action: this.id,
      //remember_me: $('#remember_me').prop('checked'),
      user: $('#user').val(),
      password: $('#password').val()
    },
  })
  .done(function(result){
      if (result.action=="login" || result.action=="logout") {
        window.location = '/';
      } else {
        location.reload();
      }
  })
  .fail(function(xhr, ajaxOptions, thrownError){
    showMessage("AJAX-fel: " + xhr.status + " (" + thrownError + ").", "error");
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