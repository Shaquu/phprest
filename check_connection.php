<?php
    
$link = mysqli_connect('eu-cdbr-azure-north-e.cloudapp.net', 'b330117eb5e28b', '3af6f03d', 'rest.shaq');
if (!$link) {
    $respond = array(
      "system" => array(
          // 0 == none, 1 == error, 2 == success, 3 == info
          "message_code" => 1,
          "message" => "Cannot connect"
      )
    );
} else {
    $respond = array(
      "system" => array(
          "message_code" => 2,
          "message" => "Connected"
      )
    );
    mysqli_close($link);
}
die(json_encode($respond));
	
?>