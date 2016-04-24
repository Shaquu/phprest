<?php
require_once('utils.php');
// connect to the mysql database
function connectSql()
{
  $host = '';
  $username = '';
  $password = '';
  $database = '';
  $link = connect($host, $username, $password, $database);
  return $link;
}

function allowedMethods($method){
  switch ($method) {
    case 'GET':
      return true;
    default:
      return false;
  }
}

function updateLapi($username, $password, $link){
  $password = cryptSha512($password, $username);
  $lapi = generateLapi();
  $cryptedLapi = cryptSha512($lapi, $username);
  $sql = "UPDATE `users` SET lapi='$cryptedLapi' WHERE user='$username' AND password='$password'";
  $result = query($link,$sql);
  if(mysqli_affected_rows($link) > 0)
    return $lapi;
  else return 0;
}

function generateLapi(){
  $bits = 20;
  return bin2hex(openssl_random_pseudo_bytes($bits));
}

?>
