<?php

require_once "functions.php";

$data = array('action' => 'error'/*, 'statusMsg' => ''*/);

if (isset($_POST['action']) && ($_POST['action']=='logout')) {
  $data['action'] = 'logout';
  unset($_SESSION['logged_in']);
  setcookie('remember_me', "gone", time() - 100, '/');
  set_message('Du är nu utloggad.', 'success');
}
elseif (valid_user($_POST['user'], $_POST['password'])) {
  //($_POST['remember_me'] == 'true/false')
  //set_message($_POST['remember_me'], 'error');
  
  //setcookie('remember_me', 'aaaaaaaaaaaaaaaaaa', '/');
  
  //set_message($_COOKIE['remember_me'], 'error');
  
  
  if ($_POST['remember_me']) {    
    setcookie('remember_me', create_hash($_POST['password']), time() + REMEMBER_ME_COOKIE, '/');
  }
  else {
    unset($_COOKIE['remember_me']);
    if (isset($_COOKIE['remember_me'])) {
      setcookie('remember_me', "gone", time() - 100, '/');
    }
  }  
      
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



