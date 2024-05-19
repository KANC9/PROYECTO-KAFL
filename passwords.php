<?php
function hashear($password) {
 $sal = mt_rand(1000, 9999);
 $password_a_hashear = $password ;
 $hash = hash('sha256', $password . $sal);
  return array('hash' => $hash, 'sal' => $sal);
}
?>

