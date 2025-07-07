<?php
// Incluye la conexión a la base de datos y la librería TCPDF para generar PDFs
include('conexion.php');
require_once(__DIR__ . '/../tcpdf/tcpdf.php');

// Conexión a la base de datos
$con = conectar();
// Variables para mensajes y datos
$error = '';
$success = '';
$unidades = [
    'Gerencia de Patrimonio Cultural.', 'Gerencia de Promoción y Difusión Cultural', 'Presidencia', 'Gerencia de Administración', 'Recursos Humanos',
    'Deposito', 'Escuela de Música', 'Escuela de Artes Escénicas', 'Escuela de Artes Plásticos',
    'Auditorio', 'Banda del estado barinas', 'Ateneo',
    'No Ubicados'
];

// Función para registrar actividad de usuario en la base de datos
function registrar_actividad($con, $accion, $detalle = '') {
    if (!isset($_SESSION)) session_start();
    if (!isset($_SESSION['usuario_id'])) return;
    $usuario_id = $_SESSION['usuario_id'];
    $sql = "INSERT INTO actividades (usuario_id, accion, detalle) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $usuario_id, $accion, $detalle);
    mysqli_stmt_execute($stmt);
}

// Obtiene la lista de bienes públicos para mostrar en el formulario de transferencia
$bienes = [];
$sql_bienes = "SELECT id, codigo_unico, descripcion, ubicacion, responsable_patrimonial FROM bienes_publicos ORDER BY codigo_unico ASC";
$res_bienes = mysqli_query($con, $sql_bienes);
while ($b = mysqli_fetch_assoc($res_bienes)) {
    $bienes[] = $b;
}

// Procesa el formulario de transferencia de bien
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['transferir'])) {
    try {
        // Valida la clave de acción del usuario
        if (!isset($_POST['clave_accion']) || empty($_POST['clave_accion'])) {
            throw new Exception("Debe ingresar la clave de acción para transferir.");
        }
        session_start();
        $usuario_id = $_SESSION['usuario_id'];
        $clave_accion_ingresada = hash('sha256', $_POST['clave_accion']);
        $sql_clave = "SELECT clave_accion FROM usuarios WHERE id = ?";
        $stmt_clave = mysqli_prepare($con, $sql_clave);
        if (!$stmt_clave) {
            throw new Exception("Error SQL (clave): " . mysqli_error($con));
        }
        mysqli_stmt_bind_param($stmt_clave, "i", $usuario_id);
        mysqli_stmt_execute($stmt_clave);
        $res_clave = mysqli_stmt_get_result($stmt_clave);
        if (!$res_clave) {
            throw new Exception("Error SQL (clave result): " . mysqli_error($con));
        }
        $row_clave = mysqli_fetch_assoc($res_clave);
        if (!$row_clave || $row_clave['clave_accion'] !== $clave_accion_ingresada) {
            throw new Exception("Clave de acción incorrecta.");
        }

        // Obtiene y valida los datos del formulario usando el código único del bien
        $codigo_unico = mysqli_real_escape_string($con, $_POST['codigo_unico']);
        $unidad_destino = mysqli_real_escape_string($con, $_POST['unidad_destino']);
        $responsable_destino = mysqli_real_escape_string($con, $_POST['responsable_destino']);

        // Obtiene los datos actuales del bien por código único
        $sql_bien = "SELECT id, ubicacion, responsable_patrimonial, codigo_unico, descripcion FROM bienes_publicos WHERE codigo_unico = ?";
        $stmt = mysqli_prepare($con, $sql_bien);
        if (!$stmt) {
            throw new Exception("Error SQL (bien): " . mysqli_error($con));
        }
        mysqli_stmt_bind_param($stmt, "s", $codigo_unico);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
        if (!$resultado) {
            throw new Exception("Error SQL (bien result): " . mysqli_error($con));
        }
        $bien = mysqli_fetch_assoc($resultado);

        if (!$bien) throw new Exception("Bien no encontrado");

        $bien_id = $bien['id'];
        $unidad_origen = $bien['ubicacion'];
        $responsable_origen = $bien['responsable_patrimonial'];
        $descripcion = $bien['descripcion'];

        // Verificar que la unidad de origen y destino no sean la misma
        if (trim($unidad_origen) === trim($unidad_destino)) {
            throw new Exception("La unidad de origen y la unidad de destino no pueden ser la misma.");
        }

        // Obtener datos adicionales para el acta (sin cedula)
        $sql_usuario = "SELECT nombre, cargo, cedula FROM usuarios WHERE id = ?";
        $stmt_usuario = mysqli_prepare($con, $sql_usuario);
        if (!$stmt_usuario) {
            throw new Exception("Error SQL (usuario): " . mysqli_error($con));
        }
        mysqli_stmt_bind_param($stmt_usuario, "i", $usuario_id);
        mysqli_stmt_execute($stmt_usuario);
        $res_usuario = mysqli_stmt_get_result($stmt_usuario);
        if (!$res_usuario) {
            throw new Exception("Error SQL (usuario result): " . mysqli_error($con));
        }
        $usuario_data = mysqli_fetch_assoc($res_usuario);

        $nombre_usuario = $usuario_data['nombre'] ?? '';
        $cargo_usuario = $usuario_data['cargo'] ?? '';
        $cedula_usuario = $usuario_data['cedula'] ?? '';
        $cedula_destino = isset($_POST['cedula_destino']) ? mysqli_real_escape_string($con, $_POST['cedula_destino']) : '';

        // Generar acta de transferencia (PDF) con formato personalizado
        $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false); // 'P' para vertical
        $pdf->SetCreator('Sistema de Bienes');
        $pdf->SetAuthor('Sistema de Bienes');
        $pdf->SetTitle('Acta de Transferencia');
        $pdf->AddPage();

        // Encabezado con logos
        $header_html = '
        <table width="100%" style="border:none;">
            <tr>
                <td width="20%" align="left"><img src="logo_iaceb.png" height="50"></td>
                <td width="60%" align="center" style="font-size:13px;">
                    <b>REPUBLICA BOLIVARIANA DE VENEZUELA<br>
                    INSTITUTO AUTONOMO DE CULTURA DEL ESTADO BARINAS<br>
                    BARINAS EDO. BARINAS</b>
                </td>
                <td width="20%" align="right"><img src="logo_barinas.png" height="50"></td>
            </tr>
        </table>
        <div style="text-align:right;font-size:12px;">Barinas; '.date('d/m/Y').'</div>
        <h2 style="text-align:center;text-decoration:underline;font-size:16px;">Acta de transferencia de bienes</h2>
        ';

        // Cuerpo del acta
        $acta_html = '
        <div style="font-size:13px;text-align:justify;">
        Yo; <b>'.htmlspecialchars($nombre_usuario).'</b> de Nacionalidad Venezolana, Titular de la Cédula de Identidad <b>'.htmlspecialchars($cedula_usuario).'</b>, en mi condición de <b>'.htmlspecialchars($cargo_usuario).'</b> DEL INSTITUCIÓN AUTÓNOMO DE CULTURA DEL ESTADO BARINAS (I.A.C.E.B), hago constar por medio de la presente acta, se realiza transferencia de <b>'.htmlspecialchars($descripcion).'</b> al ciudadano <b>'.htmlspecialchars($responsable_destino).'</b> de Nacionalidad Venezolana, Titular de la Cédula de Identidad <b>'.htmlspecialchars($cedula_destino).'</b>,  la presente asignación estará bajo la responsabilidad del ciudadano.<br><br>
        <br>
        ';

        // Cuadro de datos del bien (ya generado antes)
        $cuadro_html = '
        <table border="1" cellpadding="5" style="font-size:12px;">
            <tr><th>ID del Bien</th><td>'.$bien_id.'</td></tr>
            <tr><th>Código Único</th><td>'.$codigo_unico.'</td></tr>
            <tr><th>Descripción</th><td>'.$descripcion.'</td></tr>
            <tr><th>Unidad Origen</th><td>'.$unidad_origen.'</td></tr>
            <tr><th>Unidad Destino</th><td>'.$unidad_destino.'</td></tr>
            <tr><th>Responsable Origen</th><td>'.$responsable_origen.'</td></tr>
            <tr><th>Responsable Destino</th><td>'.$responsable_destino.'</td></tr>
            <tr><th>Fecha</th><td>'.date('d/m/Y').'</td></tr>
        </table>
        <br><br>
        ';

        // Firmas
        $firmas_html = '
        <div style="height:60px;"></div>
        <table width="100%" style="margin-top:40px;">
            <tr>
                <td width="33%" align="center" style="padding-top:40px;">_________________________<br>JEFE DE BIENES DEL I.A.C.E.B. (E)</td>
                <td width="33%"></td>
                <td width="33%" align="center" style="padding-top:40px;">_________________________</td>
            </tr>
        </table>
        ';

        $pdf->writeHTML($header_html . $acta_html . $cuadro_html . $firmas_html);

        // Cambia el nombre del archivo PDF
        $fecha_actual = date('Ymd');
        $nombre_pdf = 'acta_de_transferencia_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $codigo_unico) . '_' . $fecha_actual . '.pdf';
        $pdf_content = $pdf->Output($nombre_pdf, 'S');

        // Registrar movimientos
        // Salida/origen
        $sql_mov_out = "INSERT INTO movimientos (bien_id, tipo_movimiento, fecha, cantidad, responsable, documento_soporte) 
                        VALUES (?, 'Transferencia', CURDATE(), 1, ?, NULL)";
        $stmt_out = mysqli_prepare($con, $sql_mov_out);
        if (!$stmt_out) {
            throw new Exception("Error SQL (Salida): " . mysqli_error($con));
        }
        mysqli_stmt_bind_param($stmt_out, "is", $bien_id, $responsable_origen);
        if (!mysqli_stmt_execute($stmt_out)) {
            throw new Exception("Error al ejecutar movimiento de salida: " . mysqli_error($con));
        }

        // Entrada/destino
        $sql_mov_in = "INSERT INTO movimientos (bien_id, tipo_movimiento, fecha, cantidad, responsable, documento_soporte) 
                       VALUES (?, 'Transferencia', CURDATE(), 1, ?, NULL)";
        $stmt_in = mysqli_prepare($con, $sql_mov_in);
        if (!$stmt_in) {
            throw new Exception("Error SQL (Entrada): " . mysqli_error($con));
        }
        mysqli_stmt_bind_param($stmt_in, "is", $bien_id, $responsable_destino);
        if (!mysqli_stmt_execute($stmt_in)) {
            throw new Exception("Error al ejecutar movimiento de entrada: " . mysqli_error($con));
        }

        // Verificar inserción
        if (mysqli_stmt_affected_rows($stmt_out) <= 0 || mysqli_stmt_affected_rows($stmt_in) <= 0) {
            throw new Exception("Error al registrar la transferencia en la tabla de movimientos.");
        }

        // Actualizar bien
        $sql_update = "UPDATE bienes_publicos SET 
                        ubicacion = ?, 
                        responsable_patrimonial = ? 
                        WHERE id = ?";
        $stmt_upd = mysqli_prepare($con, $sql_update);
        if (!$stmt_upd) {
            throw new Exception("Error SQL (Update): " . mysqli_error($con));
        }
        mysqli_stmt_bind_param($stmt_upd, "ssi", $unidad_destino, $responsable_destino, $bien_id);
        if (!mysqli_stmt_execute($stmt_upd)) {
            throw new Exception("Error al actualizar bien: " . mysqli_error($con));
        }

        // Registrar actividad
        registrar_actividad($con, "Transferir Bien", "Código: $codigo_unico, De: $unidad_origen ($responsable_origen) a $unidad_destino ($responsable_destino)");

        // Descargar PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $nombre_pdf . '"');
        echo $pdf_content;

        // Cierra la ventana modal y recarga la página después de la descarga del PDF
        echo '<script>
            if(window.parent && window.parent.document.getElementById("modalClaveAccion")){
                window.parent.document.getElementById("modalClaveAccion").style.display = "none";
            }
            setTimeout(function(){ window.location.href = window.location.pathname; }, 500);
        </script>';
        exit;

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Transferencia de Bienes</title>
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
        .results-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .results-table th, .results-table td { padding: 10px; border: 1px solid #ddd; text-align: left; }
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
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.2s;
        }
        button:hover {
            background-color: #0056b3;
        }
        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        .info-bien {
            background: #f5f5f5;
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        /* Modal styles */
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
    </style>
    <script>
        // Datos de bienes para JS
        const bienes = <?= json_encode($bienes) ?>;
        function mostrarInfoBien() {
            const select = document.getElementById('codigo_unico');
            const infoDiv = document.getElementById('info-bien');
            const selected = select.value;
            if (!selected) {
                infoDiv.innerHTML = '';
                return;
            }
            const bien = bienes.find(b => b.codigo_unico === selected);
            if (bien) {
                infoDiv.innerHTML = `
                    <strong>Descripción:</strong> ${bien.descripcion}<br>
                    <strong>Ubicación actual:</strong> ${bien.ubicacion}<br>
                    <strong>Responsable actual:</strong> ${bien.responsable_patrimonial}
                `;
            } else {
                infoDiv.innerHTML = '';
            }
        }
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
        <a href="transferencias.php" class="activo">Nueva Transferencia</a>
    </div>
    <div class="seccion-contenido">
        <h2 style="margin-top:0;">Transferencia de Bienes</h2>
        <?php if($error): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="success"><?= $success ?></div>
        <?php endif; ?>

        <form id="form-transferir-bien">
            <table class="form-table">
                <tr>
                    <td><label>Código Único del Bien:</label></td>
                    <td>
                        <select name="codigo_unico" id="codigo_unico" onchange="mostrarInfoBien()" required>
                            <option value="">Seleccione un bien</option>
                            <?php foreach($bienes as $b): ?>
                                <option value="<?= htmlspecialchars($b['codigo_unico']) ?>">
                                    <?= htmlspecialchars($b['codigo_unico']) ?> - <?= htmlspecialchars(substr($b['descripcion'],0,60)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <div id="info-bien" class="info-bien"></div>
                    </td>
                </tr>
                <tr>
                    <td><label>Unidad Destino:</label></td>
                    <td>
                        <select name="unidad_destino" required>
                            <?php foreach($unidades as $u): ?>
                                <option value="<?= $u ?>"><?= $u ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label>Responsable Destino:</label></td>
                    <td><input type="text" name="responsable_destino" required></td>
                </tr>
                <tr>
                    <td><label>Cédula Responsable Destino:</label></td>
                    <td><input type="text" name="cedula_destino" placeholder="Ingrese cédula responsable destino"></td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <button type="button" onclick="abrirModalClaveAccion()">Transferir</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <a href="Inicio.php" class="back-button">← Regresar a Inicio</a>
</div>

<!-- Modal Clave de Acción Global -->
<div class="modal-bg" id="modalClaveAccion" style="display:none;">
    <div class="modal-content">
        <h3>Clave de Acción</h3>
        <form method="POST" id="form-modal-clave-accion">
            <input type="hidden" name="transferir" value="1">
            <input type="hidden" name="codigo_unico" id="modal_codigo_unico">
            <input type="hidden" name="unidad_destino" id="modal_unidad_destino">
            <input type="hidden" name="responsable_destino" id="modal_responsable_destino">
            <input type="hidden" name="cedula_destino" id="modal_cedula_destino">
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

<script>
    // Interceptar el submit para usar el modal
    document.getElementById('form-modal-clave-accion').onsubmit = function(e) {
        e.preventDefault();
        this.submit();
    };
    // Copiar datos del formulario principal al modal antes de abrirlo
    document.getElementById('form-transferir-bien').onsubmit = function(e) {
        e.preventDefault();
        abrirModalClaveAccion();
    };
    function abrirModalClaveAccion() {
        document.getElementById('modal_codigo_unico').value = document.getElementById('codigo_unico').value;
        document.getElementById('modal_unidad_destino').value = document.querySelector('select[name="unidad_destino"]').value;
        document.getElementById('modal_responsable_destino').value = document.querySelector('input[name="responsable_destino"]').value;
        document.getElementById('modal_cedula_destino').value = document.querySelector('input[name="cedula_destino"]').value;
        document.getElementById('modalClaveAccion').style.display = 'flex';
        document.getElementById('clave_accion_input').value = '';
        document.getElementById('clave_accion_input').focus();
    }
</script>
</body>
</html>
<?php
// Cerrar conexión
mysqli_close($con);
?>
