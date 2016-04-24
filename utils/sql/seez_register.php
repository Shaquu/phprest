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
    case 'POST':
      return true;
    default:
      return false;
  }
}

?>
