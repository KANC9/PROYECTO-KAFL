<?php
// errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// validaroes y mas
require_once 'validaciones.php';
include 'passwords.php';
include 'pimienta.php';

// Inicializa las variables
$nombre = isset($_POST['nombre']) ? $_POST['nombre'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;

$errores = array(); // errores

$mensajeNombre = '';
$mensajeEmail = '';
$mensajePassword = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validar el nombre
        if (!vacio($nombre)) {
        $errores['nombre'] = 'El nombre está vacío.';
        } elseif (caracteres($nombre)) {
        $errores['nombre'] = 'El nombre no puede contener los siguientes caracteres --> <>;# ';
        } elseif (!minimol($nombre)) {
        $errores['nombre'] = 'El nombre debe tener 5 caracteres mínimo y máximo 14.';
        } elseif (existeNombre($nombre)) {
        $errores['nombre'] = 'El nombre ya está registrado.';
        }
        // Validar el correo electrónico hj
        if (!vacio($email)) {
        $errores['email'] = 'El correo electrónico está vacío.';
        } elseif (!validarCorreo($email)) {
        $errores['email'] = 'El correo electrónico no es válido.';
        } elseif (existeEmail($email)) {
        $errores['email'] = 'El correo electrónico ya está registrado.';
        }

        // Validar la passwd
        if (!vacio($password)) {
        $errores['password'] = 'La contraseña está vacía.';
        } elseif (!minimop($password)) {
        $errores['password'] = 'La contraseña debe contener al menos 8 caracteres.';
        }
        // rojo si algo mal de validaciones
        $mensajeNombre = isset($errores['nombre']) ? '<p style="color: red;">' . $errores['nombre'] . '</p>' : '';
        $mensajeEmail = isset($errores['email']) ? '<p style="color: red;">' . $errores['email'] . '</p>' : '';
        $mensajePassword = isset($errores['password']) ? '<p style="color: red;">' . $errores['password'] . '</p>' : '';

        // porcesos
        if (empty($errores)) {
        // Realizar la inserción en la base de datos
        $dbname = "KAFL";
        $luser = "kafl";
        $passwordb = "kafl";
        $servidor = "localhost";

        $conn = mysqli_connect($servidor, $luser, $passwordb, $dbname);

        if ($conn->connect_error) {
                die("NO FUNKA: " . $conn->connect_error);
        }

        $generacion_hash = hashear($password);
        $hash_con_sal = $generacion_hash['hash'];
        $sal = $generacion_hash['sal'];
        $hash_pimienta = pimienta($hash_con_sal);
        $sql = "INSERT INTO `user` (Lastname, Email, Hash, sal) VALUES ('$nombre', '$email','$hash_pimienta', '$sal')";

        if ($conn->query($sql) === TRUE) {
                header("Location: proyecto.html");
                exit(); // asegurar ejecucion y luego redireccion
        } else {
                echo "Error: " . $sql . $conn->error;
        }

        $conn->close();
        }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="E.css">
        <title>Registro de Usuario</title>
</head>
<body>
        <div class="formulario_registro">
        <form action="registro.php" class="form" method="post">
                <h1 class="registro">REGISTRO</h1>

                <div class="contexto">
                <label for="nombre" class="label">Nombre de Usuario</label>
                <input type="text" id="nombre" name="nombre" class="input" placeholder="Nombre de usuario" value="<?php echo $nombre ?>">
                <?php echo $mensajeNombre; ?>
                </div>

                <div class="contexto">
                <label for="email" class="label">Correo Electrónico</label>
                <input type="text" id="email" name="email" class="input" placeholder="Correo electrónico" value="<?php echo $email ?>">
                <?php echo $mensajeEmail; ?>
                </div>

                <div class="contexto">
                <label for="password" class="label">Contraseña</label>
                <input type="password" id="password" name="password" class="input" placeholder="Contraseña">
                <?php echo $mensajePassword; ?>
                </div>

                <input type="submit" class="submitBTN" value="Registrarse">
        </form>
        </div>
</body>
</html>

