<?php

include_once "functions.php";

$data = array('action' => 'error'/*, 'statusMsg' => ''*/);
  
if (isset($_POST['action']) && ($_POST['action']=='logout')) {
  $data['action'] = 'logout';
  unset($_SESSION['logged_in']);
  set_message('Du är nu utloggad.', 'success');
}
elseif (($_POST['user'] == 'b') && ($_POST['password'] == 'b')) {
  //($_POST['remember_me'] == 'true/false')
  $data['action'] = 'login';    
  $_SESSION['logged_in'] = 'yes';
}
else { 
  $data['statusMsg'] = 'Felaktigt användarnamn eller lösenord.'; 
  set_message('Felaktigt användarnamn eller lösenord.', 'error');
}

// Krävs för att IE ska acceptera json object.
header('Content-Type: application/json');

echo json_encode($data);