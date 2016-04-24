<?php

function cryptSha512($value, $salt)
{
  $global = '+seCrypt091234/?';
  return hash_hmac('sha512', $value.$salt, $global);
}

?>
