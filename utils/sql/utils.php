<?php

function query($link, $sql)
{
  $result = mysqli_query($link,$sql);
  return $result;
}

function connect($host, $user, $password, $database)
{
    $link = mysqli_connect($host, $user, $password, $database);
    if (!$link) {
        $respond = array(
          "system" => array(
              // 0 == none, 1 == error, 2 == success, 3 == info
              "message_code" => 1,
              "message" => "Cannot connect"
          )
        );
        die(json_encode($respond));
    }
    mysqli_set_charset($link,'utf8');
    return $link;
}

?>
