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

function allowedTables($table){
  switch ($table) {
    case 'cars':
      return true;
    default:
      return false;
  }
}

function getData($table, $filter_data, $link){
  if(!allowedTables($table))
    return 0;
    
  $sql = "SELECT * FROM `$table`";

  if($filter_data){
    $sql .= " WHERE ";
    foreach($filter_data as $x => $x_value) {
      $sql .= "$x = $x_value ";
    }
  }

  $result = query($link,$sql);
  if ($result) {
    $result_data = array();
    while($row = $result->fetch_array(MYSQL_ASSOC)) {
            $result_data[] = $row;
    }
    return $result_data;
  } else return 0;
}

?>
