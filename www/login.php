<?php
session_start();
include('conexion.php');
$con = conectar();

$error = '';

// Función para registrar actividad de usuario
function registrar_actividad($con, $accion, $detalle = '') {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (!isset($_SESSION['usuario_id'])) return;
    $usuario_id = $_SESSION['usuario_id'];
    $sql = "INSERT INTO actividades (usuario_id, accion, detalle) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $usuario_id, $accion, $detalle);
    mysqli_stmt_execute($stmt);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = mysqli_real_escape_string($con, $_POST['usuario']);
    $clave = hash('sha256', $_POST['clave']);
    $sql = "SELECT * FROM usuarios WHERE usuario = ? AND clave = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $usuario, $clave);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($res);
    if ($user) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['rol'] = $user['rol'];
        // Registrar actividad Login
        registrar_actividad($con, "Login", "Usuario: {$user['usuario']}");
        header('Location: Inicio.php');
        exit;
    } else {
        $error = "Usuario o clave incorrectos";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Sistema de Bienes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            background-image: url('fondo_barinas.png');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
            margin: 0;
            padding: 0;
        }
        .login-container {
            max-width: 400px;
            width: 94%;
            margin: 180px auto 0 auto;
            background: rgba(255,255,255,0.97);
            border-radius: 8px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.18), 0 2px 10px #ccc;
            padding: 30px 24px;
        }
        .login-box h2 { text-align: center; }
        .login-box input { width: 100%; padding: 10px; margin: 10px 0; border-radius: 4px; border: 1px solid #ccc; }
        .login-box button { width: 100%; padding: 10px; background: #007bff; color: #fff; border: none; border-radius: 4px; }
        .login-box .error { color: red; text-align: center; }
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-box">
        <h2>Iniciar Sesión</h2>
        <?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <input type="text" name="usuario" placeholder="Usuario" required>
            <input type="password" name="clave" placeholder="Clave" required>
            <button type="submit">Ingresar</button>
        </form>
    </div>
</div>
</body>
</html>
