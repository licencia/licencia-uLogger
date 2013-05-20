<?php

/***************************************************
 * Database access
 **************************************************/

define('DB_NAME', 'ulogger');
define('DB_USER', 'root');
define('DB_PASS', 'ulogger');


//MySQLi
/*
function connect_to_db() {
  // Connect to database
  $mysqli = new mysqli("localhost", DB_USER, DB_PASS, DB_NAME);
  // Check connection
  if (mysqli_connect_errno()) {
    //printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
  }
  else {
    return $mysqli;
  }
}

function getVar($name, $value = "") {
  if ($con = connect_to_db()) {        
    $result = $con->query($con,"SELECT value FROM data WHERE name = '" . $name. "'");        
    if ($row = $con->fetch_assoc()) {
      return $row['value'];
    }
    else {
      return $value;
    }
    $con->close();
  }
}
*/

//MySQL

function connect_to_db() {
  // Connect to database
  $con = @mysqli_connect("localhost", DB_USER, DB_PASS, DB_NAME);
  // Check connection
  if (mysqli_connect_errno($con)) {
    //echo "Failed to connect to MySQL: " . mysqli_connect_error();
    return FALSE;
  } else {
    return $con;
  }
}

function setVar($name, $value) {
  if ($con = connect_to_db()) {
    // Create or update
    $command = "INSERT INTO data (name, value) VALUES('" . $name . "', '" . $value . "') ON DUPLICATE KEY UPDATE value = '" . $value . "';";
    mysqli_query($con, $command);
    mysqli_close($con);
  }
}

function getVar($name, $value = "") {
  if ($con = connect_to_db()) {
    $result = mysqli_query($con,"SELECT value FROM data WHERE name = '" . $name. "'");
    if ($row = mysqli_fetch_array($result)) {
      return $row['value'];
    }
    else {
      return $value;
    }
    mysqli_close($con);
  }
}

?>