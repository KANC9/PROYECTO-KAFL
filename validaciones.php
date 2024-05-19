<?php

function vacio($texto) {
	return !empty($texto);
}

function minimol($texto) {
	$longitud = strlen($texto);
	return $longitud >= 5 && $longitud <= 14;
}

function minimop($texto) {
	$longitud = strlen($texto);
	return $longitud >= 8 && $longitud <= 14;
}

function caracteres($texto) {
	$caracteresProhibidos = array("<", ">", ";", "#");

	foreach ($caracteresProhibidos as $caracter) {
    	if (strpos($texto, $caracter) !== false) {
        	return true;
    	}
	}

	return false;
}

function validarCorreo($email) {
	return filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/^[A-z0-9\\._-]+@[A-z0-9][A-z0-9-]*(\\.[A-z0-9_-]+)*\\.([A-z]{2,6})$/', $email) && checkdnsrr(explode("@", $email)[1], 'MX');
}

if (validarCorreo("peter@testeo.com")) {
	return 'Correo electrónico válido';
} else {
	return 'Correo electrónico inválido';
}

function existeNombre($nombre) {
	$dbname = "KAFL";
	$luser = "kafl";
	$passwordb = "kafl";
	$servidor = "localhost";

	$conn = mysqli_connect($servidor, $luser, $passwordb, $dbname);

	if ($conn->connect_error) {
    	die("NO FUNKA: " . $conn->connect_error);
	}

	$query = "SELECT * FROM `user` WHERE Lastname='$nombre'";
	$result = $conn->query($query);

	$conn->close();

	return $result->num_rows > 0;
}

function existeEmail($email) {
	$dbname = "KAFL";
	$luser = "kafl";
	$passwordb = "kafl";
	$servidor = "localhost";

	$conn = mysqli_connect($servidor, $luser, $passwordb, $dbname);

	if ($conn->connect_error) {
    	die("NO FUNKA: " . $conn->connect_error);
	}

	$query = "SELECT * FROM `user` WHERE Email='$email'";
	$result = $conn->query($query);

	$conn->close();

	return $result->num_rows > 0;
}
?>
