<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'admin') {
    header('Location: login.php');
    exit;
}
include('conexion.php');
$con = conectar();

$error = '';
$success = '';

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

// Determinar pestaña activa
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'registrar';

// Si no es el admin principal, forzar historial como única pestaña accesible
if ($_SESSION['usuario_id'] != 1) {
    $tab = 'historial';
}

// Eliminar usuario (no permitir eliminar admin principal)
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    if ($id == 1) {
        $error = "No se puede eliminar el usuario administrador principal.";
    } else {
        mysqli_query($con, "DELETE FROM usuarios WHERE id = $id");
        $success = "Usuario eliminado correctamente.";
        registrar_actividad($con, "Eliminar Usuario", "ID usuario eliminado: $id");
    }
}

// Crear usuario (solo admin principal puede crear usuarios)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nuevo_usuario'])) {
    if ($_SESSION['usuario_id'] != 1) {
        $error = "Solo el administrador principal puede crear nuevos usuarios.";
    } else {
        $usuario = mysqli_real_escape_string($con, $_POST['usuario']);
        $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
        $cargo = mysqli_real_escape_string($con, $_POST['cargo']);
        $clave = hash('sha256', $_POST['clave']);
        $clave_accion = hash('sha256', $_POST['clave_accion']);
        $rol = $_POST['rol'] === 'admin' ? 'admin' : 'usuario';
        $sql = "INSERT INTO usuarios (usuario, clave, nombre, rol, clave_accion, cargo) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ssssss", $usuario, $clave, $nombre, $rol, $clave_accion, $cargo);
        if (mysqli_stmt_execute($stmt)) {
            $success = "Usuario creado correctamente";
            $detalle = "Usuario: $usuario, Nombre: $nombre, Rol: $rol, Cargo: $cargo";
            registrar_actividad($con, "Crear Usuario", $detalle);
        } else {
            $error = "Error: " . mysqli_error($con);
        }
    }
}

// Editar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_usuario'])) {
    $id = (int)$_POST['id'];
    $nombre = mysqli_real_escape_string($con, $_POST['nombre']);
    $rol = $_POST['rol'] === 'admin' ? 'admin' : 'usuario';
    $set = "nombre='$nombre', rol='$rol'";
    if ($id == 1 && $_SESSION['usuario_id'] != 1) {
        // Solo puede cambiar nombre y rol
    } else {
        if (!empty($_POST['clave'])) {
            $clave = hash('sha256', $_POST['clave']);
            $set .= ", clave='$clave'";
        }
        if (!empty($_POST['clave_accion'])) {
            $clave_accion = hash('sha256', $_POST['clave_accion']);
            $set .= ", clave_accion='$clave_accion'";
        }
    }
    mysqli_query($con, "UPDATE usuarios SET $set WHERE id=$id");
    $success = "Usuario actualizado correctamente.";
    registrar_actividad($con, "Editar Usuario", "ID usuario editado: $id, Nombre: $nombre, Rol: $rol");
}

// Obtener usuarios
$usuarios = mysqli_query($con, "SELECT id, usuario, nombre, rol FROM usuarios");

// Si se va a editar, obtener datos del usuario
$usuario_editar = null;
if (isset($_GET['editar']) && is_numeric($_GET['editar'])) {
    $id = (int)$_GET['editar'];
    $res = mysqli_query($con, "SELECT * FROM usuarios WHERE id = $id");
    $usuario_editar = mysqli_fetch_assoc($res);
}

// Filtro para historial de actividades
$filtro_usuario = isset($_GET['filtro_usuario']) ? trim($_GET['filtro_usuario']) : '';
$filtro_accion = isset($_GET['filtro_accion']) ? trim($_GET['filtro_accion']) : '';
$where = [];
if ($filtro_usuario !== '') {
    $where[] = "u.usuario LIKE '%" . mysqli_real_escape_string($con, $filtro_usuario) . "%'";
}
if ($filtro_accion !== '') {
    $where[] = "a.accion LIKE '%" . mysqli_real_escape_string($con, $filtro_accion) . "%'";
}
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$actividades = mysqli_query($con, "
    SELECT a.*, u.usuario 
    FROM actividades a 
    JOIN usuarios u ON a.usuario_id = u.id 
    $where_sql
    ORDER BY a.fecha DESC 
    LIMIT 100
");

// Obtener lista de usuarios para el filtro
$usuarios_lista = [];
$res_usuarios = mysqli_query($con, "SELECT usuario FROM usuarios ORDER BY usuario ASC");
while ($row = mysqli_fetch_assoc($res_usuarios)) {
    $usuarios_lista[] = $row['usuario'];
}

// Acciones principales para el filtro
$acciones_lista = [
    'Registrar Bien',
    'Editar Bien',
    'Eliminar Bien',
    'Transferir Bien',
    'Desincorporar Bien',
    'Reincorporar Bien',
    'Reportar Faltante',
    'Generar Informe',
    'Login',
    'Logout',
    'Crear Usuario',
    'Editar Usuario',
    'Eliminar Usuario'
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
            background-image: url('fondo_barinas.png');
            background-repeat: no-repeat;
            background-position: center center;
            background-size: cover;
        }
        .container { 
            max-width: 900px; 
            width: 95%;
            margin: 40px auto 30px auto; 
            padding: 20px; 
            background: rgba(255,255,255,0.97);
            border-radius: 8px; 
            box-shadow: 0 8px 32px rgba(44,62,80,0.18), 0 2px 10px #ccc; 
        }
        .menu-interno { margin-bottom: 20px; border-bottom: 2px solid #ddd; }
        .menu-interno a {
            display: inline-block;
            padding: 10px 20px;
            text-decoration: none;
            color: #333;
            border: 1px solid #ddd;
            margin-right: 5px;
            border-radius: 5px 5px 0 0;
            background: #f5f5f5;
        }
        .menu-interno a.activo {
            background: #fff;
            border-bottom: 2px solid #fff;
            color: #0066cc;
            font-weight: bold;
        }
        .seccion-contenido { border: 1px solid #ddd; padding: 20px; border-radius: 5px; background: #fafbfc; }
        .form-table, .results-table { width: 100%; border-collapse: collapse; }
        .form-table td, .results-table th, .results-table td { border: 1px solid #ddd; padding: 8px; }
        .results-table th { background: #f5f5f5; }
        .success { color: green; margin: 10px 0; }
        .error { color: red; margin: 10px 0; }
        .btn, .btn-edit, .btn-delete, .btn-save, .filtro-form button, .back-button {
            padding: 8px 16px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 15px;
            transition: background 0.2s, color 0.2s;
            margin: 2px 0;
            display: inline-block;
        }
        .btn { background: #6c757d; color: #fff; }
        .btn-edit { background: #ffc107; color: #222; }
        .btn-delete { background: #dc3545; color: #fff; }
        .btn-save, .filtro-form button { background: #007bff; color: #fff; }
        .btn:hover, .btn-edit:hover, .btn-delete:hover, .btn-save:hover, .filtro-form button:hover, .back-button:hover {
            opacity: 0.85;
        }
        .filtro-form input, .filtro-form select {
            padding: 6px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .back-button {
            margin-top: 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            border: none;
        }
        h2, h3 { margin-top: 0; }
    </style>
</head>
<body>
<div class="container">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <img src="logo_iaceb.png" alt="Logo IACEB" style="height:60px;">
        <img src="logo_barinas.png" alt="Logo Barinas" style="height:60px;">
    </div>
    <div class="menu-interno">
        <?php if($_SESSION['usuario_id'] == 1): ?>
        <a href="usuarios.php?tab=registrar" class="<?= $tab=='registrar'?'activo':'' ?>">Registrar Usuario</a>
        <?php endif; ?>
        <a href="usuarios.php?tab=historial" class="<?= $tab=='historial'?'activo':'' ?>">Historial de Actividades</a>
    </div>
    <div class="seccion-contenido">
        <?php if($success): ?><div class="success"><?= $success ?></div><?php endif; ?>
        <?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>

        <?php if($tab=='registrar' && $_SESSION['usuario_id'] == 1): ?>
            <?php if($usuario_editar): ?>
            <h3>Editar Usuario: <?= htmlspecialchars($usuario_editar['usuario']) ?></h3>
            <form method="POST">
                <input type="hidden" name="id" value="<?= $usuario_editar['id'] ?>">
                <table class="form-table">
                    <tr>
                        <td>Nombre:</td>
                        <td><input type="text" name="nombre" value="<?= htmlspecialchars($usuario_editar['nombre']) ?>" required></td>
                        <td>Rol:</td>
                        <td>
                            <select name="rol">
                                <option value="usuario" <?= $usuario_editar['rol']=='usuario'?'selected':'' ?>>Usuario</option>
                                <option value="admin" <?= $usuario_editar['rol']=='admin'?'selected':'' ?>>Administrador</option>
                            </select>
                        </td>
                    </tr>
                    <?php if($usuario_editar['id'] == 1 && $_SESSION['usuario_id'] != 1): ?>
                    <tr>
                        <td colspan="4" style="color:#888;">Solo el administrador principal puede cambiar su clave o clave de acción.</td>
                    </tr>
                    <?php else: ?>
                    <tr>
                        <td>Nueva Clave (opcional):</td>
                        <td><input type="password" name="clave"></td>
                        <td>Nueva Clave de Acción (opcional):</td>
                        <td><input type="password" name="clave_accion"></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td colspan="4" style="text-align:right;">
                            <button type="submit" name="editar_usuario" class="btn btn-save">Guardar Cambios</button>
                            <a href="usuarios.php?tab=registrar" class="btn">Cancelar</a>
                        </td>
                    </tr>
                </table>
            </form>
            <?php elseif($_SESSION['usuario_id'] == 1): ?>
            <form method="POST">
                <table class="form-table">
                    <tr>
                        <td>Usuario:</td>
                        <td><input type="text" name="usuario" required></td>
                        <td>Nombre:</td>
                        <td><input type="text" name="nombre" required></td>
                    </tr>
                    <tr>
                        <td>Cargo:</td>
                        <td><input type="text" name="cargo" required></td>
                        <td>Clave:</td>
                        <td><input type="password" name="clave" required></td>
                    </tr>
                    <tr>
                        <td>Clave de Acción:</td>
                        <td><input type="password" name="clave_accion" required></td>
                        <td>Rol:</td>
                        <td>
                            <select name="rol">
                                <option value="usuario">Usuario</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </td>
                        <td colspan="2"><button type="submit" name="nuevo_usuario" class="btn btn-save">Crear Usuario</button></td>
                    </tr>
                </table>
            </form>
            <?php endif; ?>

            <h3>Usuarios Registrados</h3>
            <table class="results-table">
                <tr><th>ID</th><th>Usuario</th><th>Nombre</th><th>Rol</th><th></th>
                <?php while($u = mysqli_fetch_assoc($usuarios)): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['usuario']) ?></td>
                    <td><?= htmlspecialchars($u['nombre']) ?></td>
                    <td><?= $u['rol'] ?></td>
                    <td>
                        <?php if($u['id'] != 1): ?>
                        <a href="usuarios.php?tab=registrar&editar=<?= $u['id'] ?>" class="btn btn-edit">Editar</a>
                        <a href="usuarios.php?tab=registrar&eliminar=<?= $u['id'] ?>" class="btn btn-delete" onclick="return confirm('¿Eliminar este usuario?')">Eliminar</a>
                        <?php elseif($_SESSION['usuario_id'] == 1): ?>
                        <a href="usuarios.php?tab=registrar&editar=1" class="btn btn-edit">Editar</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        <?php elseif($tab=='registrar' && $_SESSION['usuario_id'] != 1): ?>
            <div class="error">Acceso denegado. Solo el administrador principal puede gestionar usuarios.</div>
        <?php endif; ?>

        <?php if($tab=='historial'): ?>
            <h3>Historial de Actividades</h3>
            <form method="GET" class="filtro-form" style="margin-bottom:15px;">
                <input type="hidden" name="tab" value="historial">
                <select name="filtro_usuario">
                    <option value="">Todos los usuarios</option>
                    <?php foreach($usuarios_lista as $u): ?>
                        <option value="<?= htmlspecialchars($u) ?>" <?= $filtro_usuario == $u ? 'selected' : '' ?>><?= htmlspecialchars($u) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="filtro_accion">
                    <option value="">Todas las acciones</option>
                    <?php foreach($acciones_lista as $accion): ?>
                        <option value="<?= htmlspecialchars($accion) ?>" <?= $filtro_accion == $accion ? 'selected' : '' ?>><?= htmlspecialchars($accion) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit">Filtrar</button>
                <a href="usuarios.php?tab=historial" class="btn" style="background:#6c757d;color:#fff;">Limpiar</a>
            </form>
            <table class="results-table">
                <tr><th>Fecha</th><th>Usuario</th><th>Acción</th><th>Detalle</th></tr>
                <?php while($a = mysqli_fetch_assoc($actividades)): ?>
                <tr>
                    <td><?= $a['fecha'] ?></td>
                    <td><?= htmlspecialchars($a['usuario']) ?></td>
                    <td><?= htmlspecialchars($a['accion']) ?></td>
                    <td><?= htmlspecialchars($a['detalle']) ?></td>
                </tr>
                <?php endwhile; ?>
            </table>
        <?php endif; ?>
        <a href="Inicio.php" class="back-button">← Volver al Menú</a>
    </div>
</div>
</body>
</html>
