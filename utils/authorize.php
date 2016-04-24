<?php
require_once('sql/utils.php');

function authUser($user, $lapi){
  
  $host = '';
  $username = '';
  $password = '';
  $database = '';
  $link = connect($host, $username, $password, $database);
  
  $sql = "SELECT count(*) from `users` where user='$user' AND lapi='$lapi'";
  $result = query($link,$sql);
  $count = mysqli_fetch_array($result)["count(*)"];
  mysqli_close($link);
  return $count;
}

?>
