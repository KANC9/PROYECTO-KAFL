<?php
session_start(); 

// user verification
if(!isset($_SESSION['lastname'])) {
    echo "Usuario no conectado";
    exit();
}

$lastname = $_SESSION['lastname'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <meta http-equiv="X-UA-Compatible" content="ie=edge">
 <link rel="stylesheet" href="F.css">
 <title>form_usuario</title>
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
        <?php
        echo "Usuario: " . htmlspecialchars($lastname) . "<br>";
        ?>
    </div>
  </div>

  <div class="formulario-container">
    <h1 class="registro">Contenedores vulnerables</h1>

    <div class="formulario_retos">
        <form action="" class="form" method="post">
            <fieldset>
                <legend>Retos</legend>
                <h2 style="color: red;">¡Importante!</h2>
                <div>
                    <input type="checkbox" id="kali" name="kali" />
                    <label for="kali">KALI</label>
                    <p style="color: red;">Debe activar esta opcion para utilizar los retos sin este requisito no podras iniciar</p>
                </div>
                <h1 style="color: green;">Retos a escoger</h1>
                <div>
                    <input type="checkbox" id="reto1" name="reto1" />
                    <label for="reto1">Reto 1</label>
                    <p>Este reto consiste en un entorno de WordPress vulnerable diseñado para practicar pruebas de penetración y evaluación de seguridad en sitios web WordPress.</p>
                <div>
                    <input type="checkbox" id="reto2" name="reto2" />
                    <label for="reto2">Reto 2</label>
                    <p>Este desafío ofrece un entorno Docker que contiene una serie de laboratorios prácticos para aprender y practicar inyección SQL.</p>
                </div>
                <div>
                    <input type="checkbox" id="reto3" name="reto3" />
                    <label for="reto3">Reto 3</label>
                    <p>Este reto se centra en vsftpd, un servidor FTP (File Transfer Protocol) vulnerable</p>
                </div>
                <div>
                    <input type="checkbox" id="reto4" name="reto4" />
                    <label for="reto4">Reto 4 meta (ㆆ_ㆆ)</label>
                    <p>Metasploitable 2 esta diseñada para ser deliberadamente vulnerable, creada con fines educativos. Ofrece una amplia gama de vulnerabilidades deliberadas y configuraciones inseguras, lo que la convierte en un entorno ideal para aprender sobre pruebas de penetración, explotación y seguridad de sistemas.</p>
                </div>
                <input type="submit" name="crear_contenedores" class="submitBTN" value="Crear Contenedores">
                <input type="submit" name="ver_estado" class="submitBTN" value="Ver Estado">
            </fieldset><br><br>
<fieldset>
    <legend style="color: blue;">Leer</legend>
    <p style="color: blue;"><strong>Pasos:</strong></p>
    <ol style="color: blue;">
        <li>Si desea volver a realizar otros retos o se ha equivocado en su elección, siga estos pasos.</li>
        <li>Acceda a la sección "Ver estado" desde el formulario realizado.</li>
        <li>Tendrá que detener los retos y la máquina KALI que se haya creado.</li>
        <li>Presione el botón "Borrar" general.</li>
        <li>Acceda de nuevo al formulario y elija los retos que desea, además de activar KALI.</li>
    </ol>
</fieldset>

        </form>
    </div>
  </div>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['crear_contenedores'])) {
        // Obtener los valores del formulario
        $reto1 = isset($_POST["reto1"]) ? 1 : 0;
        $reto2 = isset($_POST["reto2"]) ? 1 : 0;
        $reto3 = isset($_POST["reto3"]) ? 1 : 0;
        $reto4 = isset($_POST["reto4"]) ? 1 : 0;
        $kali = isset($_POST["kali"]) ? 1 : 0;
        $kali_name = "kali-attacker-" . $lastname;
        $reto1_name = "reto1_" . $lastname;
        $reto2_name = "reto2_" . $lastname;
        $reto3_name = "reto3_" . $lastname;
        $reto4_name = "reto4_" . $lastname;
        $network_name = "network_". $lastname;

        // dockernetwork con nombre de usario
        if ($kali) {
            $output = shell_exec("docker network create $network_name");
            if (!empty($output)) {
                echo "Se ha creado la red Docker con el nombre: $network_name<br>";
            } else {
                echo "Error al crear la red Docker<br>";
            }
        }

        //bd conect
        $servername = "localhost";
        $username = "kafl";
        $password = "kafl";
        $dbname = "KAFL";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        }

        // Verificar y obtener los valores existentes para el usuario
        $sql = $conn->prepare("SELECT Kali, Reto1, Reto2, Reto3, Reto4 FROM docker_cont_user WHERE Lastname = ?");
        $sql->bind_param("s", $lastname);
        $sql->execute();
        $sql->bind_result($existing_kali, $existing_reto1, $existing_reto2, $existing_reto3, $existing_reto4);
        $sql->fetch();
        $sql->close();

        // para que no haya mas de uno 
        if ($existing_kali !== null) {
            $kali = $existing_kali || $kali;
            $reto1 = $existing_reto1 || $reto1;
            $reto2 = $existing_reto2 || $reto2;
            $reto3 = $existing_reto3 || $reto3;
            $reto4 = $existing_reto4 || $reto4;
        }

        // insertar o actulizar
        if ($existing_kali !== null) {
            // Actualizar los valores existentes
            $sql = $conn->prepare("UPDATE docker_cont_user SET Kali = ?, Kali_name = ?, Reto1 = ?, Reto2 = ?, Reto3 = ?, Reto4 = ? WHERE Lastname = ?");
            $sql->bind_param("isiiiii", $kali, $kali_name, $reto1, $reto2, $reto3, $reto4, $lastname);
        } else {
            // Insertar nuevos valores
            $sql = $conn->prepare("INSERT INTO docker_cont_user (Lastname, Kali, Kali_name, Reto1, Reto2, Reto3, Reto4) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $sql->bind_param("sissiii", $lastname, $kali, $kali_name, $reto1, $reto2, $reto3, $reto4);
        }

        if ($sql->execute() === TRUE) {
            echo "Datos guardados correctamente en la tabla docker_cont_user<br>";
        } else {
            echo "Error al guardar los datos: " . $sql->error;
        }

        // contra
        function generarPassword($longitud = 10) {
            $caracteresPermitidos = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()-_=+';
            $longitudCaracteres = strlen($caracteresPermitidos);
            $password = '';
            for ($i = 0; $i < $longitud; $i++) {
                $caracterAleatorio = $caracteresPermitidos[rand(0, $longitudCaracteres - 1)];
                $password .= $caracterAleatorio;
            }
            return $password;
        }
        $vnckey = generarPassword();
        $puerto_aleatorio = rand(1023, 65535);

        // Verifica que el usuario ya tiene un registro
        $sql = $conn->prepare("SELECT COUNT(*) FROM docker_netw_user WHERE Lastname = ?");
        $sql->bind_param("s", $lastname);
        $sql->execute();
        $sql->bind_result($count);
        $sql->fetch();
        $sql->close();

if ($count > 0) {
    // Actualizar los valores existentes
    $sql = $conn->prepare("UPDATE docker_netw_user SET Port = ?, Network = ?, Tighvnc = ? WHERE Lastname = ?");
    $sql->bind_param("isss", $puerto_aleatorio, $network_name, $vnckey, $lastname);
} else {
    // Insertar nuevos valores
    $sql_insert_netw = $conn->prepare("INSERT INTO docker_netw_user (Lastname, Port, Network, Tighvnc) VALUES (?, ?, ?, ?)");
    $sql_insert_netw->bind_param("siss", $lastname, $puerto_aleatorio, $network_name, $vnckey); // Corrección en la asignación de parámetros
    if ($sql_insert_netw->execute() === TRUE) { // Ejecutar la consulta de inserción
        echo "Datos guardados correctamente en la tabla docker_netw_user<br>";
    } else {
        echo "Error al guardar los datos en la tabla docker_netw_user: " . $sql_insert_netw->error;
    }
}


        // kali user
        if ($kali) {
            $command = "docker run -d -e USER='$lastname' -e VNCPASSWORD='$vnckey' --network=$network_name -p $puerto_aleatorio:3421 --name $kali_name kali-attacker";
            $output = shell_exec($command);
            if (!empty($output)) {
                echo "Se ha iniciado el contenedor Kali<br>";
            } else {
                echo "Error al iniciar el contenedor Kali<br>";
            }
        }

	// Retosssssssssssssssssss
	if ($reto1) {
            $reto1_command = "docker run -d --name=$reto1_name --network=$network_name reto1:v1";
            $output = shell_exec($reto1_command);
            if (!empty($output)) {
                echo "Se ha iniciado el contenedor del Reto1<br>";
            } else {
                echo "Error al iniciar el contenedor del Reto1<br>";
    	    }
	}

        if ($reto2) {
            $reto2_command = "docker run -d --network=$network_name --name=$reto2_name reto2:v1";
            $output = shell_exec($reto2_command);
            if (!empty($output)) {
                echo "Se ha iniciado el contenedor del Reto2<br>";
            } else {
                echo "Error al iniciar el contenedor del Reto2<br>";
            }
        }

        if ($reto3) {
            $reto3_command = "docker run -d --network=$network_name --name=$reto3_name reto3:v1";
            $output = shell_exec($reto3_command);
            if (!empty($output)) {
                echo "Se ha iniciado el contenedor del Reto3<br>";
            } else {
                echo "Error al iniciar el contenedor del Reto3<br>";
            }
        }

        if ($reto4) {
            $reto4_command = "docker run -d --network=$network_name --name $reto4_name tleemcjr/metasploitable2";
            $output = shell_exec($reto4_command);
            if (!empty($output)) {
                echo "Se ha iniciado el contenedor del Reto4<br>";
            } else {
                echo "Error al iniciar el contenedor del Reto4<br>";
            }
        }

        $conn->close();
    }

    if (isset($_POST['ver_estado'])) {
        // manda al segudno bootn pra ver el estado de las maquinas
        header("Location: pageuser.php");
        exit();
    }
}
?>
</body>
</html>
