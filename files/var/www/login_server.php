<?php

require_once "/home/ulogger/functions.php";

$data = array('action' => 'error'/*, 'statusMsg' => ''*/);
  
if (isset($_POST['action']) && ($_POST['action']=='logout')) {
  $data['action'] = 'logout';
  unset($_SESSION['logged_in']);
  set_message('Du är nu utloggad.', 'success');
}
elseif (valid_user($_POST['user'], $_POST['password']) == 'as') {  
  //($_POST['remember_me'] == 'true/false')
  $data['action'] = 'login';    
  $_SESSION['logged_in'] = 'yes';
}
// Visa att användarnamnet saknas
/*elseif (getVar($_POST['user'] . '_hash', '') == '') {
  set_message('Användarnamnet finns inte.', 'error');  
}*/
else { 
  set_message('Felaktigt användarnamn eller lösenord.', 'error');
}

// Krävs för att IE ska acceptera json object.
header('Content-Type: application/json');

echo json_encode($data);