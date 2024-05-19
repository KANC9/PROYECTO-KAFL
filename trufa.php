<?php
function trufa($hash_con_pimienta){
$hash=$hash_con_pimienta;
$parte1 = substr($hash,0, 35);
$parte2 = substr($hash, 37);
$result = $parte1.$parte2;
   return $result;
}
?>
