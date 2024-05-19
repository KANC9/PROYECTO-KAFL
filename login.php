<!DOCTYPE html>
<html lang="es">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <meta http-equiv="X-UA-Compatible" content="ie=edge">
 <link rel="stylesheet" href="E.css">
 <title>login_usuario</title>
</head>
<body>
 <div class="formulario_registro">
  <form action="" class="form" method="post">
   <h1 class="registro">LOGIN</h1>

   <div class="contexto">
	<label for="name_email" class="label">NAME/EMAIL</label>
	<input type="text" id="name_email" name="name_email" class="input" placeholder="Email de usuario">
   </div>

   <div class="contexto">
	<label for="password" class="label">PASSWORD</label>
	<input type="password" id="password" name="password" class="input" placeholder="Contraseña de usuario">
   </div>

   <input type="submit" class="submitBTN" value="Iniciar sesión">
  </form>
 </div>
</body>
</html>

<?php
include ('trufa.php');
#guardar nombre usario
session_start(); // Inicia la sesión
#cookies para enviar a la siguinete pagina
if(isset($_COOKIE['Token'])) {
	$token_cookie = $_COOKIE['Token'];

	$dbname = "KAFL";
	$luser = "kafl";
	$passwordb = "kafl";
	$servidor = "localhost";

	$conn = mysqli_connect($servidor, $luser, $passwordb, $dbname);
	if (!$conn) {
    	die("NO FUNKA: " . mysqli_connect_error());
	}
#guardar cookies
	$query_token = "SELECT * FROM user_cookies WHERE Cookie = '$token_cookie'";
	$results = mysqli_query($conn, $query_token);
	if(mysqli_num_rows($results) == 1) {
    	header("Location: formulario.php");
    	exit();
	}

	$conn->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$name_email = $_POST["name_email"];
	$password = $_POST["password"];

	$random = bin2hex(random_bytes(16));
#usuario y password
	$dbname = "KAFL";
	$luser = "kafl";
	$passwordb = "kafl";
	$servidor = "localhost";

	$conn = mysqli_connect($servidor, $luser, $passwordb, $dbname);
	if (!$conn) {
    	die("NO FUNKA: " . mysqli_connect_error());
	}
#contraseña comprobar con hash en bd 
	$query = "SELECT * FROM user WHERE (Lastname = '$name_email' OR Email = '$name_email')";
	$results = mysqli_query($conn, $query);

	if(mysqli_num_rows($results) == 1 ) {
    	$consulta = mysqli_fetch_assoc($results);
    	$lastname = $consulta['Lastname']; // Obtener el apellido del usuario
    	$sal_return = $consulta['Sal'];
    	$hash_p_return = $consulta['Hash'];
    	$Hash_restaur = trufa($hash_p_return);

    	$hashear = hash('sha256', $password . $sal_return);
    	$Hash_user = $hashear;
    	if ($Hash_user === $Hash_restaur) {
        	$sql = "INSERT INTO user_cookies (Lastname, Cookie, FECHA_caducida) VALUES ('$lastname', '$random', NOW() + INTERVAL 1 MINUTE)";
        	mysqli_query($conn, $sql);

        	setcookie("Token", $random, time() + 6000, "/"); //tempo cookies
        	$_SESSION['lastname'] = $lastname; // Guardar el apellido en la sesión
        	mysqli_close($conn);

        	header("Location: formulario.php");
    	} else {
        	echo "<p>Contraseña incorrecta</p>";
    	}
	}
} else {
	// esto es por si la petición no es POST
}
?>
