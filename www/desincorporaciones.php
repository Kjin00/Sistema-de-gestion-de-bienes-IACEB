<?php
include('conexion.php');
$con = conectar();

// Variables iniciales
$error = '';
$success = '';
$resultados = [];
$bien_detalle = null;
$accion = isset($_GET['accion']) ? $_GET['accion'] : 'consulta';

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

// Procesar desincorporación
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirmar_desincorporar'])) {
    if (!isset($_POST['motivo']) || trim($_POST['motivo']) === '') {
        $error = "Debe ingresar el motivo de la desincorporación.";
    } elseif (!isset($_POST['explicacion']) || trim($_POST['explicacion']) === '') {
        $error = "Debe ingresar una explicación para la desincorporación.";
    } elseif (!isset($_POST['clave_accion']) || empty($_POST['clave_accion'])) {
        $error = "Debe ingresar la clave de acción para desincorporar.";
    } else {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['usuario_id'])) {
            $error = "Sesión expirada. Vuelva a iniciar sesión.";
        } else {
            $usuario_id = $_SESSION['usuario_id'];
            $clave_accion_ingresada = hash('sha256', $_POST['clave_accion']);
            $sql_clave = "SELECT clave_accion FROM usuarios WHERE id = ?";
            $stmt_clave = mysqli_prepare($con, $sql_clave);
            mysqli_stmt_bind_param($stmt_clave, "i", $usuario_id);
            mysqli_stmt_execute($stmt_clave);
            $res_clave = mysqli_stmt_get_result($stmt_clave);
            $row_clave = mysqli_fetch_assoc($res_clave);
            if (!$row_clave || $row_clave['clave_accion'] !== $clave_accion_ingresada) {
                $error = "Clave de acción incorrecta.";
            } else {
                $id = (int)$_POST['id'];
                $motivo = mysqli_real_escape_string($con, $_POST['motivo']);
                $explicacion = mysqli_real_escape_string($con, $_POST['explicacion']);
                // Obtener estado y responsable patrimonial del bien
                $sql_verificar = "SELECT estado, responsable_patrimonial FROM bienes_publicos WHERE id = ?";
                $stmt_verificar = mysqli_prepare($con, $sql_verificar);
                mysqli_stmt_bind_param($stmt_verificar, "i", $id);
                mysqli_stmt_execute($stmt_verificar);
                $resultado_verificar = mysqli_stmt_get_result($stmt_verificar);
                $bien = mysqli_fetch_assoc($resultado_verificar);
                if ($bien && $bien['estado'] === 'Incorporado') {
                    $responsable_patrimonial = $bien['responsable_patrimonial'];
                    $sql = "UPDATE bienes_publicos SET estado = 'Desincorporado' WHERE id = ?";
                    $stmt = mysqli_prepare($con, $sql);
                    mysqli_stmt_bind_param($stmt, "i", $id);
                    if (mysqli_stmt_execute($stmt)) {
                        $detalle = "Motivo: $motivo. Explicación: $explicacion";
                        // Elimina 'detalle' de la consulta y del bind
                        $sql_movimiento = "INSERT INTO movimientos (bien_id, tipo_movimiento, fecha, cantidad, responsable) 
                                           VALUES (?, 'Desincorporación', CURDATE(), 1, ?)";
                        $stmt_movimiento = mysqli_prepare($con, $sql_movimiento);
                        mysqli_stmt_bind_param($stmt_movimiento, "is", $id, $responsable_patrimonial);
                        if (!mysqli_stmt_execute($stmt_movimiento)) {
                            $error = "Error al registrar el movimiento de desincorporación: " . mysqli_error($con);
                        } else {
                            registrar_actividad($con, "Desincorporar Bien", "ID: $id. $detalle");
                            $success = "Bien desincorporado exitosamente.";
                        }
                    } else {
                        $error = "Error al desincorporar: " . mysqli_error($con);
                    }
                } else {
                    $error = "El bien no está incorporado y no puede ser desincorporado.";
                }
            }
        }
    }
}

// Procesar reincorporación
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reincorporar'])) {
    // Validar clave de acción
    if (!isset($_POST['clave_accion']) || empty($_POST['clave_accion'])) {
        $error = "Debe ingresar la clave de acción para reincorporar.";
    } else {
        session_start();
        $usuario_id = $_SESSION['usuario_id'];
        $clave_accion_ingresada = hash('sha256', $_POST['clave_accion']);
        $sql_clave = "SELECT clave_accion FROM usuarios WHERE id = ?";
        $stmt_clave = mysqli_prepare($con, $sql_clave);
        mysqli_stmt_bind_param($stmt_clave, "i", $usuario_id);
        mysqli_stmt_execute($stmt_clave);
        $res_clave = mysqli_stmt_get_result($stmt_clave);
        $row_clave = mysqli_fetch_assoc($res_clave);
        if (!$row_clave || $row_clave['clave_accion'] !== $clave_accion_ingresada) {
            $error = "Clave de acción incorrecta.";
        } else {
            $id = (int)$_POST['id'];

            // Verificar que el bien esté desincorporado
            $sql_verificar = "SELECT estado FROM bienes_publicos WHERE id = ?";
            $stmt_verificar = mysqli_prepare($con, $sql_verificar);
            mysqli_stmt_bind_param($stmt_verificar, "i", $id);
            mysqli_stmt_execute($stmt_verificar);
            $resultado_verificar = mysqli_stmt_get_result($stmt_verificar);
            $bien = mysqli_fetch_assoc($resultado_verificar);

            if ($bien && $bien['estado'] === 'Desincorporado') {
                $sql = "UPDATE bienes_publicos SET estado = 'Incorporado' WHERE id = ?";
                $stmt = mysqli_prepare($con, $sql);
                mysqli_stmt_bind_param($stmt, "i", $id);

                if (mysqli_stmt_execute($stmt)) {
                    // Registrar movimiento de reincorporación
                    $sql_movimiento = "INSERT INTO movimientos (bien_id, tipo_movimiento, fecha, cantidad, responsable) 
                                       VALUES (?, 'Reincorporación', CURDATE(), 1, ?)";
                    $stmt_movimiento = mysqli_prepare($con, $sql_movimiento);
                    mysqli_stmt_bind_param($stmt_movimiento, "is", $id, $responsable_patrimonial);
                    mysqli_stmt_execute($stmt_movimiento);

                    // Registrar actividad
                    registrar_actividad($con, "Reincorporar Bien", "ID: $id");

                    $success = "Bien reincorporado exitosamente.";
                } else {
                    $error = "Error al reincorporar: " . mysqli_error($con);
                }
            } else {
                $error = "El bien no está desincorporado y no puede ser reincorporado.";
            }
        }
    }
}

// Procesar búsqueda o mostrar todos los bienes desincorporados
if ($accion == 'consulta') {
    $busqueda = '';
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['busqueda']) && trim($_GET['busqueda']) !== '') {
        $busqueda = trim(mysqli_real_escape_string($con, $_GET['busqueda']));
        $sql = "SELECT id, codigo_unico, tipo_bien, descripcion, 
                fecha_adquisicion, estado_conservacion 
                FROM bienes_publicos 
                WHERE estado = 'Desincorporado'
                AND (codigo_unico LIKE '%$busqueda%' OR descripcion LIKE '%$busqueda%')
                ORDER BY fecha_adquisicion DESC";
    } else {
        $sql = "SELECT id, codigo_unico, tipo_bien, descripcion, 
                fecha_adquisicion, estado_conservacion 
                FROM bienes_publicos 
                WHERE estado = 'Desincorporado'
                ORDER BY fecha_adquisicion DESC";
    }
    $resultados = mysqli_query($con, $sql);
}

// AGREGADO: Mostrar bienes incorporados para la acción "desincorporar"
if ($accion == 'desincorporar') {
    $busqueda = '';
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['busqueda']) && trim($_GET['busqueda']) !== '') {
        $busqueda = trim(mysqli_real_escape_string($con, $_GET['busqueda']));
        $sql = "SELECT id, codigo_unico, tipo_bien, descripcion, 
                fecha_adquisicion, estado_conservacion 
                FROM bienes_publicos 
                WHERE estado = 'Incorporado'
                AND (codigo_unico LIKE '%$busqueda%' OR descripcion LIKE '%$busqueda%')
                ORDER BY fecha_adquisicion DESC";
    } else {
        $sql = "SELECT id, codigo_unico, tipo_bien, descripcion, 
                fecha_adquisicion, estado_conservacion 
                FROM bienes_publicos 
                WHERE estado = 'Incorporado'
                ORDER BY fecha_adquisicion DESC";
    }
    $resultados = mysqli_query($con, $sql);
}

if (isset($_GET['ver'])) {
    $id = (int)$_GET['ver'];
    $sql = "SELECT * FROM bienes_publicos WHERE id = $id";
    $bien_detalle = mysqli_fetch_assoc(mysqli_query($con, $sql));

    // Obtener movimientos de desincorporación para este bien
    $sql_mov = "SELECT * FROM movimientos WHERE bien_id = $id AND tipo_movimiento = 'Desincorporación' ORDER BY fecha DESC LIMIT 1";
    $movimiento = mysqli_fetch_assoc(mysqli_query($con, $sql_mov));
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Desincorporaciones de Bienes</title>
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
            max-width: 1050px; 
            width: 96%;
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
        .detail-view { background: #f9f9f9; padding: 20px; margin-top: 20px; }
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
        .action-btn, button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.2s;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        .action-btn:hover, button:hover {
            background-color: #0056b3;
        }
        .action-btn.red {
            background-color: #e74c3c;
        }
        .action-btn.red:hover {
            background-color: #c0392b;
        }
        .action-btn.green {
            background-color: #28a745;
        }
        .action-btn.green:hover {
            background-color: #1e7e34;
        }
        .motivo-area, .explicacion-area {
            width: 98%;
            min-height: 40px;
            resize: none;
            font-size: 15px;
            padding: 6px;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin-bottom: 6px;
            box-sizing: border-box;
            transition: min-height 0.2s;
            overflow-x: hidden;
        }
        .clave-area {
            width: 98%;
            font-size: 15px;
            padding: 6px;
            border-radius: 4px;
            border: 1px solid #ccc;
            margin-bottom: 6px;
            box-sizing: border-box;
            overflow-x: hidden;
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
        .modal-content .action-btn-lista {
            background-color: #6c757d;
            color: #fff;
            margin-right: 10px;
        }
        .modal-content .action-btn-lista:hover {
            background-color: #495057;
        }
        @media (max-width: 600px) {
            .modal-content { padding: 10px; min-width: 0; }
        }
    </style>
    <script>
    // Autoajuste solo en altura para textarea
    function autoResizeField(el) {
        if (el.tagName === 'TEXTAREA') {
            el.style.height = 'auto';
            el.style.height = (el.scrollHeight) + 'px';
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('textarea').forEach(function(el) {
            autoResizeField(el);
            el.addEventListener('input', function() { autoResizeField(el); });
        });
    });

    function mostrarMotivoExp(id) {
        // Oculta cualquier otro modal abierto
        document.querySelectorAll('tr[id^="desinc-form-row-"]').forEach(function(row) {
            row.style.display = 'none';
        });
        document.getElementById('desinc-form-row-' + id).style.display = '';
        // Reset forms
        document.getElementById('motivoExpForm-' + id).style.display = '';
        document.getElementById('claveAccionForm-' + id).style.display = 'none';
        document.getElementById('motivo-' + id).value = '';
        document.getElementById('explicacion-' + id).value = '';
    }
    function mostrarClaveAccion(id) {
        // Copia los valores a los campos ocultos
        document.getElementById('hidden-motivo-' + id).value = document.getElementById('motivo-' + id).value;
        document.getElementById('hidden-explicacion-' + id).value = document.getElementById('explicacion-' + id).value;
        document.getElementById('motivoExpForm-' + id).style.display = 'none';
        document.getElementById('claveAccionForm-' + id).style.display = '';
    }
    function cerrarDesincModal(id) {
        document.getElementById('desinc-form-row-' + id).style.display = 'none';
    }
    function abrirModalDetalle(id) {
        window.location.href = '?accion=<?= $accion ?>&ver=' + id;
    }
    function cerrarModalDetalle() {
        window.location.href = '?accion=<?= $accion ?>';
    }
    function volverLista() {
        window.location.href = '?accion=<?= $accion ?>';
    }
    // Modal Clave de Acción
    function abrirModalClaveAccion(tipo, id) {
        document.getElementById('modalClaveAccion').style.display = 'flex';
        document.getElementById('clave_accion_tipo').value = tipo;
        document.getElementById('clave_accion_id').value = id || '';
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
        <a href="?accion=consulta" class="<?= $accion == 'consulta' ? 'activo' : '' ?>">Consultar Bienes Desincorporados</a>
        <a href="?accion=desincorporar" class="<?= $accion == 'desincorporar' ? 'activo' : '' ?>">Desincorporar Bienes</a>
    </div>

    <div class="seccion-contenido">
        <?php if ($accion == 'desincorporar'): ?>
            <!-- Sección de Desincorporación -->
            <?php if ($error): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="success"><?= $success ?></div>
            <?php endif; ?>

            <form method="GET" class="search-box">
                <input type="hidden" name="accion" value="desincorporar">
                <input type="text" name="busqueda" placeholder="Buscar bienes incorporados" value="<?= isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : '' ?>">
                <button type="submit">Buscar</button>
            </form>

            <?php if (!empty($resultados)): ?>
            <table class="results-table">
                <tr>
                    <th>Código</th>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
                <?php while ($bien = mysqli_fetch_assoc($resultados)): ?>
                <tr>
                    <td><?= htmlspecialchars($bien['codigo_unico']) ?></td>
                    <td><?= htmlspecialchars($bien['tipo_bien']) ?></td>
                    <td><?= htmlspecialchars(substr($bien['descripcion'], 0, 50)) ?>...</td>
                    <td><?= date('d/m/Y', strtotime($bien['fecha_adquisicion'])) ?></td>
                    <td><?= htmlspecialchars($bien['estado_conservacion']) ?></td>
                    <td>
                        <button type="button" class="action-btn red" onclick="mostrarMotivoExp(<?= $bien['id'] ?>)">Desincorporar</button>
                        <button type="button" class="action-btn" style="margin-left:5px;" onclick="abrirModalDetalle(<?= $bien['id'] ?>)">Ver Detalles</button>
                    </td>
                </tr>
                <!-- Motivo y Explicación (primera tabla) -->
                <tr id="desinc-form-row-<?= $bien['id'] ?>" style="display:none;">
                    <td colspan="6">
                        <div class="modal-bg" style="position:fixed;">
                            <div class="modal-content">
                                <h3>Desincorporar Bien</h3>
                                <form id="motivoExpForm-<?= $bien['id'] ?>" onsubmit="event.preventDefault(); abrirModalClaveAccion('desincorporar', <?= $bien['id'] ?>);">
                                    <table class="form-table">
                                        <tr>
                                            <td><strong>Motivo:</strong></td>
                                            <td>
                                                <textarea name="motivo" id="motivo-<?= $bien['id'] ?>" class="motivo-area" required oninput="this.style.height='auto';this.style.height=(this.scrollHeight)+'px';"></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Explicación:</strong></td>
                                            <td>
                                                <textarea name="explicacion" id="explicacion-<?= $bien['id'] ?>" class="explicacion-area" required oninput="this.style.height='auto';this.style.height=(this.scrollHeight)+'px';"></textarea>
                                            </td>
                                        </tr>
                                    </table>
                                    <div style="margin-top: 10px; text-align:right;">
                                        <button type="submit" class="action-btn red">Continuar</button>
                                        <button type="button" class="action-btn action-btn-lista" onclick="cerrarDesincModal(<?= $bien['id'] ?>)" id="cancelar-desinc-btn-<?= $bien['id'] ?>">Cancelar</button>
                                    </div>
                                </form>
                                <!-- Segunda tabla: Clave de acción, aparece después de continuar -->
                                <form method="POST" class="desinc-form" id="claveAccionForm-<?= $bien['id'] ?>" style="display:none; margin-top:15px;">
                                    <input type="hidden" name="id" value="<?= $bien['id'] ?>">
                                    <input type="hidden" name="motivo" id="hidden-motivo-<?= $bien['id'] ?>">
                                    <input type="hidden" name="explicacion" id="hidden-explicacion-<?= $bien['id'] ?>">
                                    <table class="form-table">
                                        <tr>
                                            <td><strong>Clave de acción:</strong></td>
                                            <td>
                                                <input type="password" name="clave_accion" class="clave-area" required>
                                            </td>
                                        </tr>
                                    </table>
                                    <div style="margin-top: 10px; text-align:right;">
                                        <button type="submit" name="confirmar_desincorporar" class="action-btn red">Confirmar Desincorporación</button>
                                        <button type="button" class="action-btn action-btn-lista" onclick="cerrarDesincModal(<?= $bien['id'] ?>)" id="cancelar-desinc-btn-<?= $bien['id'] ?>">Cancelar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php else: ?>
            <p>No se encontraron bienes incorporados.</p>
            <?php endif; ?>
        <?php else: ?>
            <!-- Sección de Consulta -->
            <form method="GET" class="search-box">
                <input type="hidden" name="accion" value="consulta">
                <input type="text" name="busqueda" placeholder="Buscar bienes desincorporados" value="<?= isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : '' ?>">
                <button type="submit" class="action-btn">Buscar</button>
            </form>
            <?php if (!empty($resultados)): ?>
            <table class="results-table">
                <tr>
                    <th>Código</th>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
                <?php while ($bien = mysqli_fetch_assoc($resultados)): ?>
                <tr>
                    <td><?= htmlspecialchars($bien['codigo_unico']) ?></td>
                    <td><?= htmlspecialchars($bien['tipo_bien']) ?></td>
                    <td><?= htmlspecialchars(substr($bien['descripcion'], 0, 50)) ?>...</td>
                    <td><?= date('d/m/Y', strtotime($bien['fecha_adquisicion'])) ?></td>
                    <td><?= htmlspecialchars($bien['estado_conservacion']) ?></td>
                    <td>
                        <button type="button" class="action-btn green" onclick="abrirModalClaveAccion('reincorporar', <?= $bien['id'] ?>)">Reincorporar</button>
                        <button type="button" class="action-btn" style="margin-left:5px;" onclick="abrirModalDetalle(<?= $bien['id'] ?>)">Ver Detalles</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php else: ?>
            <p>No se encontraron bienes desincorporados.</p>
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($bien_detalle): ?>
        <div class="modal-bg" id="modalDetalle">
            <div class="modal-content">
                <h3>Ficha Técnica Completa</h3>
                <table class="form-table">
                    <?php foreach($bien_detalle as $campo => $valor): ?>
                        <?php if ($valor !== null && $valor !== ''): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars(ucwords(str_replace('_',' ',$campo))) ?></strong></td>
                            <td>
                                <?php if ($campo === 'documento_soporte' && $valor): ?>
                                    <a href="<?= htmlspecialchars($valor) ?>" target="_blank" class="action-btn" style="background-color:#28a745;">Ver documento adjunto</a>
                                <?php elseif ($campo === 'precio_adquisicion'): ?>
                                    <?= number_format($valor, 2) ?> Bs
                                <?php elseif ($campo === 'fecha_adquisicion'): ?>
                                    <?= date('d/m/Y', strtotime($valor)) ?>
                                <?php else: ?>
                                    <?= nl2br(htmlspecialchars($valor)) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if (isset($movimiento) && !empty($movimiento['detalle'])): ?>
                        <tr>
                            <td><strong>Motivo de Desincorporación</strong></td>
                            <td><?= htmlspecialchars($movimiento['detalle']) ?></td>
                        </tr>
                    <?php endif; ?>
                </table>
                <div style="margin-top: 20px; text-align:right;">
                    <button class="action-btn action-btn-lista" onclick="volverLista()">Volver a la lista</button>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <a href="Inicio.php" class="back-button action-btn">← Regresar a Inicio</a>
</div>

<!-- Modal Clave de Acción Global -->
<div class="modal-bg" id="modalClaveAccion" style="display:none;">
    <div class="modal-content">
        <h3>Clave de Acción</h3>
        <form method="POST" id="form-modal-clave-accion">
            <input type="hidden" name="clave_accion_tipo" id="clave_accion_tipo">
            <input type="hidden" name="clave_accion_id" id="clave_accion_id">
            <table class="form-table">
                <tr>
                    <td><label>Clave de Acción:</label></td>
                    <td><input type="password" name="clave_accion" id="clave_accion_input" required></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <button type="submit">Confirmar</button>
                        <button type="button" onclick="cerrarModalClaveAccion()" class="action-btn action-btn-lista">Cancelar</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<script>
    document.getElementById('form-modal-clave-accion').onsubmit = function(e) {
        e.preventDefault();
        var tipo = document.getElementById('clave_accion_tipo').value;
        var id = document.getElementById('clave_accion_id').value;
        var clave = document.getElementById('clave_accion_input').value;
        if (tipo === 'desincorporar') {
            var f = document.createElement('form');
            f.method = 'POST';
            f.innerHTML = '<input type="hidden" name="confirmar_desincorporar" value="1">' +
                          '<input type="hidden" name="clave_accion" value="'+clave+'">' +
                          '<input type="hidden" name="id" value="'+id+'">' +
                          // Motivo y explicación deben ser recuperados del DOM si es necesario
                          '<input type="hidden" name="motivo" value="'+(document.getElementById('motivo-'+id).value)+'">' +
                          '<input type="hidden" name="explicacion" value="'+(document.getElementById('explicacion-'+id).value)+'">';
            document.body.appendChild(f);
            f.submit();
        } else if (tipo === 'reincorporar') {
            var f = document.createElement('form');
            f.method = 'POST';
            f.innerHTML = '<input type="hidden" name="reincorporar" value="1">' +
                          '<input type="hidden" name="clave_accion" value="'+clave+'">' +
                          '<input type="hidden" name="id" value="'+id+'">';
            document.body.appendChild(f);
            f.submit();
        }
    };
</script>
<?php
mysqli_close($con);
?>
