<?php
error_reporting(0);
require_once("utils/sql/seez_get.php");
require_once("utils/crypt.php");
require_once("utils/authorize.php");
$method = $_SERVER['REQUEST_METHOD'];

if(!empty($_SERVER['PATH_INFO']) && allowedMethods($method)){

  // get the HTTP method, path and body of the request
  $request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
  $input = json_decode(file_get_contents('php://input'),true);

  // retrieve the username and lapi from the path
  $username = array_shift($request);
  $lapi = array_shift($request);
  $table = array_shift($request);

  $filter = explode(',', trim(array_shift($request)));
  $filter_data = array();
  if($filter){
    foreach ($filter as $value) {
      $newValue = explode('=', trim($value));
      if(sizeof($newValue) >= 2)
        $filter_data[array_shift($newValue)] = array_shift($newValue);
    }
  }

  $respond = array(
    "system" => array(
        // 0 == none, 1 == error, 2 == success, 3 == info
        "method" => $method,
        "message_code" => 0,
        "message" => "Message not set"
    ),
    "data" => array(
        "username" => $username
    ),
    "response" => array()
  );

  switch ($method) {
    case 'GET':
      if(strlen($username) < 5 || strlen($lapi) < 5 || strlen($table) < 1){
        $respond['system']['message_code'] = 1;
        $respond['system']['message'] = 'Arguments too short';
        die(json_encode($respond));
      }
  }

  $lapi = cryptSha512($lapi, $username);

  $auth = authUser($username, $lapi);
  if($auth == 0){
    $respond['system']['message_code'] = 3;
    $respond['system']['message'] = 'Wrong auth data';
    die(json_encode($respond));
  }

  // connect to the mysql database
  $link = connectSql();
  $result = getData($table, $filter_data, $link);

  switch ($method) {
    case 'GET':
      if($result == 0){
        $respond['system']['message_code'] = 3;
        $respond['system']['message'] = 'Cannot get data';
      } else {
        $respond['system']['message_code'] = 2;
        $respond['system']['args'] = $filter_data;;
        $respond['system']['message'] = 'Weee :)';
        $respond['response'] = $result;
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
