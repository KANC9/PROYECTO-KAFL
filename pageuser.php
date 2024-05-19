<?php
session_start();

//user conectado
if (!isset($_SESSION['lastname'])) {
    echo "Usuario no conectado";
    exit();
}

$lastname = $_SESSION['lastname'];

// Estado de lso Retos
function obtenerEstadoContenedor($nombreContenedor) {
    $output = shell_exec("docker inspect -f '{{.State.Status}}' $nombreContenedor 2>&1");
    $status = trim($output);

    // Si el estado es "running", lo retornamos directamente
    if ($status === "running") {
        return "Running";
    } elseif ($status === "" || strpos($status, "Error:") === 0) {
        return "Death"; // Si el estado está vacío o comienza con "Error:", asumimos que el contenedor no existe o hay un problema
    } else {
        return $status; // Si no es "running" ni un error, devolvemos el estado real del contenedor
    }
}

// cambiar algun dia pedir todo en una pero hasta el moemnto pra pedir  a la bd de la siguinte forma
//funcion pedir red
function obtenerNetwork($lastname) {
    $servername = "localhost";
    $username = "kafl";
    $password = "kafl";
    $dbname = "KAFL";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $sql = $conn->prepare("SELECT Network FROM docker_netw_user WHERE Lastname = ?");
    $sql->bind_param("s", $lastname);
    $sql->execute();
    $sql->bind_result($network);
    $sql->fetch();
    $sql->close();
    $conn->close();

    return $network;
}
//funcion pedir reto
function actualizarEstadoReto($lastname, $container, $estado) {
    if (strpos($container, "kali") === 0) {
    	$numreto = "kali";
    } elseif (strpos($container, "reto") === 0) {
	preg_match('/reto(\d+)_/', $container, $numreto_arr);
	$numreto = "$numreto_arr[1]";
    }
    $servername = "localhost";
    $username = "kafl";
    $password = "kafl";
    $dbname = "KAFL";

    // Crear coneta
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar conect
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // actulizar estadoooooo
    $campo = "";
    switch ($numreto) {
        case 'kali':
            $campo = "Kali";
            break;
        case '1':
            $campo = "Reto1";
            break;
        case '2':
            $campo = "Reto2";
            break;
        case '3':
            $campo = "Reto3";
            break;
        case '4':
            $campo = "Reto4";
            break;
        default:
            return "Aqui no deberia aparecer nunca esto";
    }

    // actulizar los retos
    $sql = $conn->prepare("UPDATE docker_cont_user SET $campo = ? WHERE Lastname = ?");
    if (!$sql) {
        die("Preparación de consulta fallida: " . $conn->error);
    }
    $sql->bind_param("is", $estado, $lastname);
    $sql->execute();

    // verificamos
    if ($sql->affected_rows > 0) {
        $resultado = "Actualización exitosa";
    } else {
        $resultado = "No se encontró el usuario o no hubo cambios";
    }

    // Cerrar la conexión
    $sql->close();
    $conn->close();

    return $resultado;
}


// funcion para el estado
function obtenerEstadoRetos($lastname) {
    $servername = "localhost";
    $username = "kafl";
    $password = "kafl";
    $dbname = "KAFL";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $sql = $conn->prepare("SELECT Kali, Kali_name, Reto1, Reto2, Reto3, Reto4 FROM docker_cont_user WHERE Lastname = ?");
    $sql->bind_param("s", $lastname);
    $sql->execute();
    $sql->bind_result($kali, $kali_name, $reto1, $reto2, $reto3, $reto4);
    $sql->fetch();

    // RETOSSpiedoendo si el 1 es lo de el kali por que esta determindo de una forma distinta en el codigo luego los resto es para que lo hago por booleano
    $retos = [
        'kali' => ['exists' => $kali, 'name' => $kali_name, 'state' => $kali == 1 ? obtenerEstadoContenedor($kali_name) : "Death"],
        'reto1' => ['exists' => $reto1, 'name' => "reto1_$lastname", 'state' => $reto1 == 1 ? obtenerEstadoContenedor("reto1_$lastname") : "Death"],
        'reto2' => ['exists' => $reto2, 'name' => "reto2_$lastname", 'state' => $reto2 == 1 ? obtenerEstadoContenedor("reto2_$lastname") : "Death"],
        'reto3' => ['exists' => $reto3, 'name' => "reto3_$lastname", 'state' => $reto3 == 1 ? obtenerEstadoContenedor("reto3_$lastname") : "Death"],
        'reto4' => ['exists' => $reto4, 'name' => "reto4_$lastname", 'state' => $reto4 == 1 ? obtenerEstadoContenedor("reto4_$lastname") : "Death"]
    ];
    $sql->close();
    $conn->close();

    return $retos;
}

// funcion puerto
function obtenerPuerto($lastname) {
    $servername = "localhost";
    $username = "kafl";
    $password = "kafl";
    $dbname = "KAFL";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $sql = $conn->prepare("SELECT Port FROM docker_netw_user WHERE Lastname = ?");
    $sql->bind_param("s", $lastname);
    $sql->execute();
    $sql->bind_result($puerto);
    $sql->fetch();
    $sql->close();
    $conn->close();

    return $puerto;
}

// vnc funcion para la key
function obtenerVNCPassword($lastname) {
    $servername = "localhost";
    $username = "kafl";
    $password = "kafl";
    $dbname = "KAFL";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $sql = $conn->prepare("SELECT Tighvnc FROM docker_netw_user WHERE Lastname = ?");
    $sql->bind_param("s", $lastname);
    $sql->execute();
    $sql->bind_result($vnckey);
    $sql->fetch();
    $sql->close();
    $conn->close();

    return $vnckey;
}


$network = obtenerNetwork($lastname);
$vnckey = obtenerVNCPassword($lastname);
$retos = obtenerEstadoRetos($lastname);
$puerto = obtenerPuerto($lastname);

// botones de los retos
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $container = $_POST['container'];
    preg_match('/reto(\d+)_/', $container, $reto_num);
    shell_exec("bash -c 'echo $action $reto_num[1] $container >> /tmp/x'");
    // Definir nombre del contenedor Kali
    $kali_name = "kali-attacker-" . $lastname;
    switch ($action) {
        case 'reboot':
            shell_exec("docker restart $container");
	    actualizarEstadoReto($lastname, $container, 1); //verde
            break;
        case 'kill':
            shell_exec("docker kill $container");
            shell_exec("docker rm $container");
	    actualizarEstadoReto($lastname, $container, 0); //verde bd 
            break;
        case 'recreate':
            preg_match('/reto(\d+)_/', $container, $reto_num);
            shell_exec("docker kill $container");
            shell_exec("docker rm $container");
           if (strpos($container, "kali") === 0) {
                shell_exec("docker run -d -e USER=$lastname -e VNCPASSWORD='$vnckey' --network=$network -p $puerto:3421 --name $kali_name kali-attacker");
            } elseif (strpos($container, "reto") === 0) {
		preg_match('/reto(\d+)_/', $container, $reto_num);
                switch ($reto_num[1]) {
                    case 1:
                        $imageName = "reto1:v2";
                        break;
                    case 2:
                        $imageName = "reto2:v1";
                        break;
                    case 3:
                        $imageName = "reto3:v1";
                        break;
                    case 4:
                        $imageName = "tleemcjr/metasploitable2";
                        break;
                }
                shell_exec("docker run -d --name=$container --network=$network $imageName");
            }
	    actualizarEstadoReto($lastname, $container, 1); //bd verde 
            break;
    }

    // Redireccionar después de completar las acciones
    header("Location: pageuser.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="P.css">
    <title>Estado de Contenedores</title>
</head>
<body>
    <div class="header-container">
        <header class="menu">
            <h1>KAFL</h1>
            <ul>
                <li><a class="link-menu" href="proyecto.html">Home</a></li>
                <li><a class="link-menu" href="registro.php">Registro</a></li>
            </ul>
        </header>
        <div class="user-info">
            <?php echo "Usuario: " . htmlspecialchars($lastname) . "<br>"; ?>
        </div>
    </div>

    <div class="contenedor-estado">
        <h1>Estado de los Contenedores</h1>

        <table>
            <tr>
                <th>Reto</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($retos as $key => $reto): ?>
            <tr style="background-color: <?php echo $reto['exists'] ? 'lightgreen' : 'lightcoral'; ?>;">
                <td><?php echo strtoupper($key); ?></td>
                <td><?php echo $reto['state']; ?></td>
                <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="container" value="<?php echo $reto['name']; ?>">
                            <input type="hidden" name="action" value="reboot">
                            <button type="submit">Reiniciar</button>
                        </form>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="container" value="<?php echo $reto['name']; ?>">
                            <input type="hidden" name="action" value="kill">
                            <button type="submit">Matar</button>
                        </form>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="container" value="<?php echo $reto['name']; ?>">
                            <input type="hidden" name="action" value="recreate">
                            <button type="submit">Recrear</button>
                        </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <div class="vnc-info">
            <h2>Información de VNC</h2>
            <p>Contraseña de VNC: <strong><?php echo htmlspecialchars($vnckey); ?></strong></p>
            <a href="/reto/<?php echo $puerto; ?>/vnc.html" target="_blank">Conectar al KALI</a>
        </div>
    </div>
</body>
</html>
