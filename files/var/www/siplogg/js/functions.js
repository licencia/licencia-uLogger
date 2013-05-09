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
};