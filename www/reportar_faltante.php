<?php
include('conexion.php');
$con = conectar();

$error = '';
$success = '';
$resultados = [];
$accion = isset($_GET['accion']) ? $_GET['accion'] : 'consulta';

// Función para registrar actividad de usuario
function registrar_actividad($con, $accion, $detalle = '') {
    if (!isset($_SESSION)) session_start();
    if (!isset($_SESSION['usuario_id'])) return;
    $usuario_id = $_SESSION['usuario_id'];
    $sql = "INSERT INTO actividades (usuario_id, accion, detalle) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $usuario_id, $accion, $detalle);
    mysqli_stmt_execute($stmt);
}

// Procesar registro de faltante
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar'])) {
    // Validar clave de acción
    if (!isset($_POST['clave_accion']) || empty($_POST['clave_accion'])) {
        $error = "Debe ingresar la clave de acción para reportar faltante.";
    } else {
        session_start();
        $usuario_id = $_SESSION['usuario_id'];
        $clave_accion_ingresada = hash('sha256', $_POST['clave_accion']);
        $sql_clave = "SELECT clave_accion FROM usuarios WHERE id = ?";
        $stmt_clave = mysqli_prepare($con, $sql_clave);
        if (!$stmt_clave) {
            $error = "Error SQL (clave): " . mysqli_error($con);
        } else {
            mysqli_stmt_bind_param($stmt_clave, "i", $usuario_id);
            mysqli_stmt_execute($stmt_clave);
            $res_clave = mysqli_stmt_get_result($stmt_clave);
            $row_clave = mysqli_fetch_assoc($res_clave);
            if (!$row_clave || $row_clave['clave_accion'] !== $clave_accion_ingresada) {
                $error = "Clave de acción incorrecta.";
            } else {
                $bien_id = (int)$_POST['bien_id'];
                $causa_probable = mysqli_real_escape_string($con, $_POST['descripcion']);
                $investigacion = mysqli_real_escape_string($con, $_POST['responsable']);

                // Obtener la cantidad del bien
                $sql_cant = "SELECT cantidad, responsable_patrimonial FROM bienes_publicos WHERE id = $bien_id";
                $res_cant = mysqli_query($con, $sql_cant);
                $row_cant = mysqli_fetch_assoc($res_cant);
                $cantidad_faltante = $row_cant && isset($row_cant['cantidad']) ? (int)$row_cant['cantidad'] : 1;
                $responsable_patrimonial = $row_cant && isset($row_cant['responsable_patrimonial']) ? $row_cant['responsable_patrimonial'] : '';

                $sql = "INSERT INTO faltantes (bien_id, fecha_reporte, cantidad_faltante, causa_probable, investigacion) 
                        VALUES (?, CURDATE(), ?, ?, ?)";
                $stmt = mysqli_prepare($con, $sql);
                if (!$stmt) {
                    $error = "Error SQL (faltantes): " . mysqli_error($con);
                } else {
                    mysqli_stmt_bind_param($stmt, "iiss", $bien_id, $cantidad_faltante, $causa_probable, $investigacion);

                    if (mysqli_stmt_execute($stmt)) {
                        // Cambiar estado del bien a "En investigación"
                        $sql_estado = "UPDATE bienes_publicos SET estado = 'En investigación' WHERE id = ?";
                        $stmt_estado = mysqli_prepare($con, $sql_estado);
                        if ($stmt_estado) {
                            mysqli_stmt_bind_param($stmt_estado, "i", $bien_id);
                            mysqli_stmt_execute($stmt_estado);
                            mysqli_stmt_close($stmt_estado);
                        }
                        // Registrar movimiento tipo 'Reporte'
                        $sql_mov = "INSERT INTO movimientos (bien_id, tipo_movimiento, fecha, cantidad, responsable) VALUES (?, 'Reporte', CURDATE(), ?, ?)";
                        $stmt_mov = mysqli_prepare($con, $sql_mov);
                        if ($stmt_mov) {
                            mysqli_stmt_bind_param($stmt_mov, "iis", $bien_id, $cantidad_faltante, $responsable_patrimonial);
                            mysqli_stmt_execute($stmt_mov);
                            mysqli_stmt_close($stmt_mov);
                        }
                        $success = "Reporte de bien faltante registrado exitosamente.";
                        // Registrar actividad
                        registrar_actividad($con, "Reportar Faltante", "Bien ID: $bien_id, Responsable: $investigacion");
                    } else {
                        $error = "Error al registrar el reporte: " . mysqli_error($con);
                    }
                    $accion = 'consulta';
                }
            }
        }
    }
}

// Procesar búsqueda o mostrar todos los bienes reportados como faltantes
if ($accion == 'consulta') {
    $busqueda = '';
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['busqueda']) && trim($_GET['busqueda']) !== '') {
        $busqueda = trim(mysqli_real_escape_string($con, $_GET['busqueda']));
        $sql = "SELECT f.id, b.codigo_unico, b.descripcion AS desc_bien, b.ubicacion, f.causa_probable AS desc_faltante, f.fecha_reporte, f.investigacion AS responsable
                FROM faltantes f
                JOIN bienes_publicos b ON f.bien_id = b.id
                WHERE (b.codigo_unico LIKE '%$busqueda%' OR b.descripcion LIKE '%$busqueda%' OR f.investigacion LIKE '%$busqueda%')
                ORDER BY f.fecha_reporte DESC";
    } else {
        $sql = "SELECT f.id, b.codigo_unico, b.descripcion AS desc_bien, b.ubicacion, f.causa_probable AS desc_faltante, f.fecha_reporte, f.investigacion AS responsable
                FROM faltantes f
                JOIN bienes_publicos b ON f.bien_id = b.id
                ORDER BY f.fecha_reporte DESC";
    }
    $resultados = mysqli_query($con, $sql);
    // Obtener detalle si se solicita
    $faltante_detalle = null;
    if (isset($_GET['ver']) && is_numeric($_GET['ver'])) {
        $id = (int)$_GET['ver'];
        $sql_det = "SELECT f.*, b.codigo_unico, b.descripcion AS desc_bien, b.ubicacion FROM faltantes f JOIN bienes_publicos b ON f.bien_id = b.id WHERE f.id = $id";
        $faltante_detalle = mysqli_fetch_assoc(mysqli_query($con, $sql_det));
    }
}

// Obtener lista de bienes para el select
$bienes = [];
$sql_bienes = "SELECT id, codigo_unico, descripcion FROM bienes_publicos ORDER BY codigo_unico ASC";
$res_bienes = mysqli_query($con, $sql_bienes);
while ($b = mysqli_fetch_assoc($res_bienes)) {
    $bienes[] = $b;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bienes Faltantes</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
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
        .seccion-contenido { border: 1px solid #ddd; padding: 20px; border-radius: 5px; }
        .form-table { width: 100%; border-collapse: collapse; }
        .form-table td { padding: 10px; border: 1px solid #ddd; }
        .search-box { margin-bottom: 20px; }
        .results-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .results-table th, .results-table td { padding: 10px; border: 1px solid #ddd; }
        .error { color: red; margin: 10px 0; }
        .success { color: green; margin: 10px 0; }
        .back-button {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }
        .back-button:hover {
            background-color: #0056b3;
        }
        button, .results-table button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 15px;
            transition: background 0.2s;
        }
        button:hover, .results-table button:hover {
            background-color: #0056b3;
        }
        input[type="text"], textarea, select {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        .modal-bg {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.45);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: #fff;
            border-radius: 8px;
            padding: 30px 30px 20px 30px;
            min-width: 320px;
            max-width: 98vw;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 2px 20px #3338;
            position: relative;
        }
    </style>
    <script>
        // Modal Clave de Acción
        function abrirModalClaveAccion() {
            document.getElementById('modalClaveAccion').style.display = 'flex';
            document.getElementById('clave_accion_input').value = '';
            document.getElementById('clave_accion_input').focus();
        }
        function cerrarModalClaveAccion() {
            document.getElementById('modalClaveAccion').style.display = 'none';
        }
        function submitClaveAccion() {
            document.getElementById('form-modal-clave-accion').submit();
        }
    </script>
</head>
<body>
<div class="container">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <img src="logo_iaceb.png" alt="Logo IACEB" style="height:60px;">
        <img src="logo_barinas.png" alt="Logo Barinas" style="height:60px;">
    </div>
    <div class="menu-interno">
        <a href="?accion=consulta" class="<?= $accion == 'consulta' ? 'activo' : '' ?>">Consultar Bienes Faltantes</a>
        <a href="?accion=reportar" class="<?= $accion == 'reportar' ? 'activo' : '' ?>">Reportar un Bien</a>
    </div>

    <div class="seccion-contenido">
        <?php if ($accion == 'reportar'): ?>
            <?php if ($error): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="success"><?= $success ?></div>
            <?php endif; ?>
            <form id="form-reportar-faltante">
                <table class="form-table">
                    <tr>
                        <td><label for="bien_id">Bien registrado:</label></td>
                        <td>
                            <select name="bien_id" id="bien_id" required>
                                <option value="">Seleccione un bien</option>
                                <?php foreach ($bienes as $b): ?>
                                    <option value="<?= $b['id'] ?>">
                                        <?= htmlspecialchars($b['codigo_unico']) ?> - <?= htmlspecialchars(substr($b['descripcion'],0,60)) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="descripcion">Descripción del Faltante:</label></td>
                        <td><textarea name="descripcion" id="descripcion" required></textarea></td>
                    </tr>
                    <tr>
                        <td><label for="responsable">Responsable:</label></td>
                        <td><input type="text" name="responsable" id="responsable" required></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:center;">
                            <button type="button" onclick="abrirModalClaveAccion()">Reportar</button>
                        </td>
                    </tr>
                </table>
            </form>
        <?php else: ?>
            <form method="GET" class="search-box">
                <input type="hidden" name="accion" value="consulta">
                <input type="text" name="busqueda" placeholder="Buscar por código, descripción o responsable" value="<?= isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : '' ?>">
                <button type="submit" name="buscar">Buscar</button>
            </form>
            <?php if ($error): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="success"><?= $success ?></div>
            <?php endif; ?>
            <?php if (!empty($resultados) && mysqli_num_rows($resultados) > 0): ?>
            <table class="results-table">
                <tr>
                    <th>Código Bien</th>
                    <th>Descripción Bien</th>
                    <th>Ubicación</th>
                    <th>Descripción Faltante</th>
                    <th>Fecha Reporte</th>
                    <th>Responsable</th>
                    <th>Acciones</th>
                </tr>
                <?php while($faltante = mysqli_fetch_assoc($resultados)): ?>
                <tr>
                    <td><?= htmlspecialchars($faltante['codigo_unico']) ?></td>
                    <td><?= htmlspecialchars($faltante['desc_bien']) ?></td>
                    <td><?= htmlspecialchars($faltante['ubicacion']) ?></td>
                    <td><?= htmlspecialchars($faltante['desc_faltante']) ?></td>
                    <td><?= date('d/m/Y', strtotime($faltante['fecha_reporte'])) ?></td>
                    <td><?= htmlspecialchars($faltante['responsable']) ?></td>
                    <td>
                        <button type="button" class="btn" onclick="window.location.href='?accion=consulta&ver=<?= $faltante['id'] ?>'">Ver Detalles</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php else: ?>
            <p>No se encontraron bienes faltantes.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <a href="Inicio.php" class="back-button">← Regresar a Inicio</a>
</div>

<!-- Modal Clave de Acción Global -->
<div class="modal-bg" id="modalClaveAccion" style="display:none;">
    <div class="modal-content">
        <h3>Clave de Acción</h3>
        <form method="POST" id="form-modal-clave-accion">
            <input type="hidden" name="registrar" value="1">
            <input type="hidden" name="bien_id" id="modal_bien_id">
            <input type="hidden" name="descripcion" id="modal_descripcion">
            <input type="hidden" name="responsable" id="modal_responsable">
            <table class="form-table">
                <tr>
                    <td><label>Clave de Acción:</label></td>
                    <td><input type="password" name="clave_accion" id="clave_accion_input" required></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <button type="button" onclick="submitClaveAccion()">Confirmar</button>
                        <button type="button" onclick="cerrarModalClaveAccion()" class="action-btn action-btn-lista">Cancelar</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<?php if (isset($faltante_detalle) && $faltante_detalle): ?>
<div class="modal-bg" id="modalDetalle" style="display:flex;">
    <div class="modal-content">
        <h3>Detalle del Bien Faltante</h3>
        <table class="form-table">
            <tr><td><strong>Código Bien</strong></td><td><?= htmlspecialchars($faltante_detalle['codigo_unico']) ?></td></tr>
            <tr><td><strong>Descripción Bien</strong></td><td><?= htmlspecialchars($faltante_detalle['desc_bien']) ?></td></tr>
            <tr><td><strong>Ubicación</strong></td><td><?= htmlspecialchars($faltante_detalle['ubicacion']) ?></td></tr>
            <tr><td><strong>Estado del Bien</strong></td><td>
                <?php
                // Obtener datos completos del bien
                $bien_id = $faltante_detalle['bien_id'];
                $sql_bien = "SELECT * FROM bienes_publicos WHERE id = $bien_id";
                $bien = mysqli_fetch_assoc(mysqli_query($con, $sql_bien));
                if ($bien) {
                    echo htmlspecialchars($bien['estado']);
                } else {
                    echo "N/D";
                }
                ?>
            </td></tr>
            <tr><td><strong>Responsable Patrimonial</strong></td><td><?= isset($bien['responsable_patrimonial']) ? htmlspecialchars($bien['responsable_patrimonial']) : 'N/D' ?></td></tr>
            <tr><td><strong>Fecha de Adquisición</strong></td><td><?= isset($bien['fecha_adquisicion']) ? date('d/m/Y', strtotime($bien['fecha_adquisicion'])) : 'N/D' ?></td></tr>
            <tr><td><strong>Precio de Adquisición</strong></td><td><?= isset($bien['precio_adquisicion']) ? number_format($bien['precio_adquisicion'],2,',','.') : 'N/D' ?></td></tr>
            <tr><td><strong>Notas</strong></td><td><?= isset($bien['notas']) ? nl2br(htmlspecialchars($bien['notas'])) : 'N/D' ?></td></tr>
            <tr><td><strong>Descripción Faltante</strong></td><td><?= htmlspecialchars($faltante_detalle['causa_probable']) ?></td></tr>
            <tr><td><strong>Fecha Reporte</strong></td><td><?= date('d/m/Y', strtotime($faltante_detalle['fecha_reporte'])) ?></td></tr>
            <tr><td><strong>Responsable (Investigación)</strong></td><td><?= htmlspecialchars($faltante_detalle['investigacion']) ?></td></tr>
            <?php if (isset($faltante_detalle['cantidad_faltante'])): ?>
            <tr><td><strong>Cantidad Faltante</strong></td><td><?= htmlspecialchars($faltante_detalle['cantidad_faltante']) ?></td></tr>
            <?php endif; ?>
        </table>
        <div style="margin-top: 20px; text-align:right;">
            <button class="btn" onclick="window.location.href='?accion=consulta'">Cerrar</button>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
    // Interceptar el submit para usar el modal
    document.getElementById('form-modal-clave-accion').onsubmit = function(e) {
        e.preventDefault();
        // El formulario ya tiene los campos ocultos
        this.submit();
    };
    // Copiar datos del formulario principal al modal antes de abrirlo
    document.getElementById('form-reportar-faltante').onsubmit = function(e) {
        e.preventDefault();
        abrirModalClaveAccion();
    };
    // Al abrir el modal, copiar los datos del formulario principal
    function abrirModalClaveAccion() {
        document.getElementById('modal_bien_id').value = document.getElementById('bien_id').value;
        document.getElementById('modal_descripcion').value = document.getElementById('descripcion').value;
        document.getElementById('modal_responsable').value = document.getElementById('responsable').value;
        document.getElementById('modalClaveAccion').style.display = 'flex';
        document.getElementById('clave_accion_input').value = '';
        document.getElementById('clave_accion_input').focus();
    }
</script>
<?php
mysqli_close($con);
?>
</body>
</html>
