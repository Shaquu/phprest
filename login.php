<?php
error_reporting(0);
require_once("utils/sql/seez_login.php");
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
        "logged" => 0,
        "username" => $username,
        "lapi" => ""
    )
  );

  switch ($method) {
    case 'GET':
      if(strlen($username) < 5 || strlen($password) < 5){
        $respond['system']['message_code'] = 1;
        $respond['system']['message'] = 'Username or password too short';
        die(json_encode($respond));
      }
  }

  $result = updateLapi($username, $password, $link);

  switch ($method) {
    case 'GET':
      if($result == 0){
        $respond['system']['message_code'] = 3;
        $respond['system']['message'] = 'Cannot log in';
      } else {
        $respond['system']['message_code'] = 2;
        $respond['system']['message'] = 'User logged in';
        $respond['data']['logged'] = 1;
        $respond['data']['lapi'] = $result;
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
