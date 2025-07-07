<?php
// Inicia la sesión para el usuario
session_start();
// Incluye el archivo de conexión a la base de datos
include('conexion.php');
$con = conectar();

// Variable para almacenar mensajes de error
$error = '';

// Función para registrar actividad de usuario en la base de datos
function registrar_actividad($con, $accion, $detalle = '') {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (!isset($_SESSION['usuario_id'])) return;
    $usuario_id = $_SESSION['usuario_id'];
    $sql = "INSERT INTO actividades (usuario_id, accion, detalle) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $usuario_id, $accion, $detalle);
    mysqli_stmt_execute($stmt);
}

// Procesa el formulario de inicio de sesión cuando se envía por POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Escapa el usuario y encripta la clave
    $usuario = mysqli_real_escape_string($con, $_POST['usuario']);
    $clave = hash('sha256', $_POST['clave']);
    // Consulta para verificar usuario y clave
    $sql = "SELECT * FROM usuarios WHERE usuario = ? AND clave = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $usuario, $clave);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($res);
    if ($user) {
        // Si el usuario existe, guarda datos en la sesión
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['rol'] = $user['rol'];
        // Registrar actividad de inicio de sesión
        registrar_actividad($con, "Login", "Usuario: {$user['usuario']}");
        // Redirige al inicio
        header('Location: Inicio.php');
        exit;
    } else {
        // Si no existe, muestra error
        $error = "Usuario o clave incorrectos";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Sistema de Bienes</title>
    <style>
        /* Estilos generales para el fondo y el cuerpo de la página */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            background-image: url('fondo_barinas.png');
            background-repeat: no-repeat; /* Imagen de fondo sin repetición */
            background-position: center center; /* Centra la imagen de fondo */
            background-size: cover; /* Ajusta la imagen al tamaño de la pantalla */
            margin: 0;
            padding: 0;
        }
        /* Contenedor principal del formulario de login */
        .login-container {
            max-width: 400px;
            width: 94%;
            margin: 180px auto 0 auto; /* Centra vertical y horizontalmente */
            background: rgba(255,255,255,0.97); /* Fondo blanco semitransparente */
            border-radius: 8px; /* Bordes redondeados */
            box-shadow: 0 8px 32px rgba(44,62,80,0.18), 0 2px 10px #ccc; /* Sombra para profundidad */
            padding: 30px 24px;
        }
        /* Estilo para el título del formulario */
        .login-box h2 { text-align: center; }
        /* Estilos para los campos de entrada */
        .login-box input { width: 100%; padding: 10px; margin: 10px 0; border-radius: 4px; border: 1px solid #ccc; }
        /* Estilo para el botón de enviar */
        .login-box button { width: 100%; padding: 10px; background: #007bff; color: #fff; border: none; border-radius: 4px; }
        /* Mensaje de error centrado y en rojo */
        .login-box .error { color: red; text-align: center; }
    </style>
</head>
<body>
<!-- Contenedor principal del login -->
<div class="login-container">
    <div class="login-box">
        <h2>Iniciar Sesión</h2>
        <!-- Muestra mensaje de error si existe -->
        <?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <!-- Formulario de inicio de sesión -->
        <form method="POST">
            <input type="text" name="usuario" placeholder="Usuario" required>
            <input type="password" name="clave" placeholder="Clave" required>
            <button type="submit">Ingresar</button>
        </form>
    </div>
</div>
</body>
</html>
