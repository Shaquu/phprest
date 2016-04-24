<?php
error_reporting(0);
require_once("utils/sql/seez_register.php");
require_once("utils/crypt.php");
$method = $_SERVER['REQUEST_METHOD'];

if(!empty($_SERVER['PATH_INFO']) && allowedMethods($method)){

  // get the HTTP method, path and body of the request
  $request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
  $input = json_decode(file_get_contents('php://input'),true);

  // connect to the mysql database
  $link = connectSql();

  // retrieve the username and password from the path
  $username = array_shift($request);
  $password = array_shift($request);

  $respond = array(
    "system" => array(
        // 0 == none, 1 == error, 2 == success, 3 == info
        "method" => $method,
        "message_code" => 0,
        "message" => "Message not set"
    ),
    "data" => array(
        // 0 == no, 1 == yes
        "registred" => 0,
        "username" => $username
    )
  );

  switch ($method) {
    case 'GET':
      if(strlen($username) < 5){
        $respond['system']['message_code'] = 1;
        $respond['system']['message'] = 'Username too short';
        die(json_encode($respond));
      }
      break;
    case 'POST':
      if(strlen($username) < 5 || strlen($password) < 5){
        $respond['system']['message_code'] = 1;
        $respond['system']['message'] = 'Username or password too short';
        die(json_encode($respond));
      }
      break;
  }

  // build SQL command to check if user exists
  $sql = "SELECT count(*) from `users` where user='$username'";

  // check if user already exists
  $result = query($link,$sql);

  $count = mysqli_fetch_array($result)['count(*)'];

  switch ($method) {
    case 'GET':
      if($count == 0){
        $respond['system']['message_code'] = 3;
        $respond['system']['message'] = 'Username not used';
      } else {
        $respond['system']['message_code'] = 3;
        $respond['system']['message'] = 'User registred';
      }
      mysqli_close($link);
      die(json_encode($respond));
    case 'POST':
      if($count != 0){
        $respond['system']['message_code'] = 3;
        $respond['system']['message'] = 'User already exists';
        $respond['data']['registred'] = 0;
        mysqli_close($link);
        die(json_encode($respond));
      }

      $password = cryptSha512($password, $username);

      $sql = "INSERT INTO `users`(`user`, `password`) VALUES ('$username','$password')";
      $result = query($link,$sql);
      if($result){
        $respond['system']['message_code'] = 2;
        $respond['system']['message'] = 'User registred';
        $respond['data']['registred'] = 1;
      } else {
        $respond['system']['message_code'] = 3;
        $respond['system']['message'] = 'User not registred';
        $respond['data']['registred'] = 0;
      }
      mysqli_close($link);
      die(json_encode($respond));
  }

  // close mysql connection
  mysqli_close($link);
} else {
  $respond = array(
    "system" => array(
        "method" => $method,
        "message_code" => 1,
        "message" => "Wrong method"
    )
  );
  die(json_encode($respond));
}
?>
