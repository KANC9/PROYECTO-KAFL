<?php
function pimienta($hash_con_sal){
$hash=$hash_con_sal;
$letranumero=mt_rand(19,99);
$parte1 = substr($hash,0,35);
$parte2 = substr($hash,35);
$result = $parte1.$letranumero.$parte2;
   return $result;
}
?>
