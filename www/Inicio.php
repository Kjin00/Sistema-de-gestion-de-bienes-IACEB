<?php
// Inicia la sesión y verifica que el usuario esté autenticado
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

// Maneja el cierre de sesión (logout)
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
        /* Estilos generales para el fondo y el cuerpo de la página */
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
        /* Contenedor principal del menú */
        .menu-container {
            width: 95%;
            max-width: 540px;
            margin: 60px auto 40px auto;
            background: rgba(255,255,255,0.97);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.18), 0 2px 10px rgba(0,0,0,0.10);
        }
        /* Título del menú */
        .menu-title {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        /* Lista de opciones del menú */
        .menu-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 90px 72px; /* Mucho más espacio entre filas, columnas igual */
        }
        .menu-item {
            background: none;
            border: none;
            box-shadow: none;
            padding: 0;
            margin: 0;
            transition: transform 0.2s;
            width: 150px; /* Más ancho para mayor separación */
            text-align: center;
        }
        /* Estilos para los enlaces del menú */
        .menu-item a {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: #34495e;
            font-size: 17px;
            font-family: 'Arial', sans-serif;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: color 0.3s, transform 0.2s;
        }
        .menu-item a .icon-circle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #eaf1fb;
            margin-bottom: 18px;
            box-shadow: 0 2px 8px rgba(44,62,80,0.10);
            transition: background 0.3s;
            overflow: hidden;
        }
        .menu-item a .icon-img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            display: block;
        }
        .menu-item a:hover .icon-circle {
            background: #2980b9;
        }
        .menu-item a span:last-child {
            font-size: 17px;
            font-weight: 600;
            color: #34495e;
            letter-spacing: 0.5px;
            margin-top: 0;
            margin-bottom: 0;
            line-height: 1.25;
            text-shadow: 0 1px 2px #fff, 0 0px 1px #eaf1fb;
            white-space: normal;
            word-break: break-word;
        }
        .menu-item a:hover {
            color: #2980b9;
            transform: translateY(-4px) scale(1.05);
        }
        .menu-item:not(:last-child)::after {
            display: none;
        }
        /* Estilos para el botón de cerrar sesión */
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
        /* Efecto hover para el botón de cerrar sesión */
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
                <a href="incorporaciones.php">
                    <span class="icon-circle">
                        <img class="icon-img" src="iconos/Copilot_20250630_220337.png" alt="Incorporaciones">
                    </span>
                    <span>Incorporaciones</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="desincorporaciones.php">
                    <span class="icon-circle">
                        <img class="icon-img" src="iconos/Copilot_20250630_220834.png" alt="Desincorporaciones">
                    </span>
                    <span>Desincorporaciones</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="transferencias.php">
                    <span class="icon-circle">
                        <img class="icon-img" src="iconos/Copilot_20250630_220846.png" alt="Transferencias">
                    </span>
                    <span>Transferencias</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="reportar_faltante.php">
                    <span class="icon-circle">
                        <img class="icon-img" src="iconos/Copilot_20250630_22033.png" alt="Reportar Faltante">
                    </span>
                    <span>Reportar Faltante</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="informes.php">
                    <span class="icon-circle">
                        <img class="icon-img" src="iconos/Copilot_20250630_22337.png" alt="Informes">
                    </span>
                    <span>Informes</span>
                </a>
            </li>
            <li class="menu-item">
                <a href="ayuda.php">
                    <span class="icon-circle">
                        <img class="icon-img" src="iconos/Copilot_0250630_220337.png" alt="Ayuda">
                    </span>
                    <span>Ayuda</span>
                </a>
            </li>
            <?php if(isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
            <li class="menu-item">
                <a href="usuarios.php">
                    <span class="icon-circle">
                        <img class="icon-img" src="iconos/opilot_20250630_220337.png" alt="Usuarios">
                    </span>
                    <span>Usuarios</span>
                </a>
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