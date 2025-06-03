<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Función para registrar actividad de usuario (unificada)
function registrar_actividad($con, $accion, $detalle = '') {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (!isset($_SESSION['usuario_id'])) return;
    include_once('conexion.php');
    $con = conectar();
    $usuario_id = $_SESSION['usuario_id'];
    $sql = "INSERT INTO actividades (usuario_id, accion, detalle) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $usuario_id, $accion, $detalle);
    mysqli_stmt_execute($stmt);
    mysqli_close($con);
}

// Manejar logout
if (isset($_GET['logout'])) {
    registrar_actividad($con, "Logout", "Usuario: " . (isset($_SESSION['usuario']) ? $_SESSION['usuario'] : ''));
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Gestión de Bienes IACEB</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            background-color: #f5f5f5;
            background-image: url('fondo_barinas.png');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
        }

        .menu-container {
            width: 95%;
            max-width: 540px;
            margin: 60px auto 40px auto;
            background: rgba(255,255,255,0.97);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.18), 0 2px 10px rgba(0,0,0,0.10);
        }

        .menu-title {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .menu-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .menu-item {
            padding: 15px 20px;
            margin: 10px 0;
            position: relative;
            transition: all 0.3s ease;
        }

        .menu-item:not(:last-child)::after {
            content: '';
            display: block;
            width: 100%;
            height: 1px;
            background: #e0e0e0;
            position: absolute;
            bottom: -10px;
            left: 0;
        }

        .menu-item a {
            text-decoration: none;
            color: #34495e;
            font-size: 18px;
            display: block;
            transition: color 0.3s ease;
        }

        .menu-item a:hover {
            color: #2980b9;
            transform: translateX(10px);
        }

        .menu-item:hover::after {
            background: #2980b9;
        }

        .logout-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 30px;
            transition: background 0.3s ease;
        }

        .logout-btn:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
    <div class="menu-container">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <img src="logo_iaceb.png" alt="Logo IACEB" style="height:70px;">
            <img src="logo_barinas.png" alt="Logo Barinas" style="height:70px;">
        </div>
        <h1 class="menu-title">Sistema de Gestión de Bienes IACEB</h1>
        
        <ul class="menu-list">
            <li class="menu-item">
                <a href="incorporaciones.php">1. Incorporaciones de Bienes</a>
            </li>
            <li class="menu-item">
                <a href="desincorporaciones.php">2. Desincorporaciones de Bienes</a>
            </li>
            <li class="menu-item">
                <a href="transferencias.php">3. Transferencias de Bienes</a>
            </li>
            <li class="menu-item">
                <a href="reportar_faltante.php">4. Reportar Faltante</a>
            </li>
            <li class="menu-item">
                <a href="informes.php">5. Informes</a>
            </li>
            <li class="menu-item">
                <a href="ayuda.php">6. Ayuda / Manual de Usuario</a>
            </li>
            <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
            <li class="menu-item">
                <a href="usuarios.php">7. Gestión de Usuarios</a>
            </li>
            <?php endif; ?>
        </ul>
        <div style="text-align: center; margin-top: 30px;">
            <button class="logout-btn" onclick="confirmLogout()">Salir / Cerrar Sesión</button>
        </div>
    </div>
    <script>
        function confirmLogout() {
            if(confirm('¿Está seguro que desea cerrar la sesión?')) {
                window.location.href = 'login.php?logout=1';
            }
        }
    </script>
</body>
</html>