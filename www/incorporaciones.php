<?php
include('conexion.php');
$con = conectar();

// Variables iniciales
$error = '';
$success = '';
$resultados = [];
$bien_detalle = null;
$bien_editar = null;
$accion = isset($_GET['accion']) ? $_GET['accion'] : 'consulta';

// Función para registrar actividad de usuario (unificada)
function registrar_actividad($con, $accion, $detalle = '') {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (!isset($_SESSION['usuario_id'])) return;
    $usuario_id = $_SESSION['usuario_id'];
    $sql = "INSERT INTO actividades (usuario_id, accion, detalle) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $usuario_id, $accion, $detalle);
    mysqli_stmt_execute($stmt);
}

// Procesar formulario de incorporación (todo en un solo paso)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar_bien_final'])) {
    try {
        // Validar clave de acción
        if (!isset($_POST['clave_accion']) || empty($_POST['clave_accion'])) {
            throw new Exception("Debe ingresar la clave de acción para registrar un bien.");
        }
        // Validar fecha de adquisición (espera formato dd-mm-aaaa)
        if (!isset($_POST['fecha_adquisicion']) || trim($_POST['fecha_adquisicion']) === '') {
            throw new Exception("Debe ingresar la fecha de adquisición del bien.");
        }
        $fecha_input = trim($_POST['fecha_adquisicion']);
        // Validar formato dd-mm-aaaa
        if (!preg_match('/^\d{2}-\d{2}-\d{4}$/', $fecha_input)) {
            throw new Exception("La fecha de adquisición debe tener el formato DD-MM-AAAA.");
        }
        // Convertir a formato aaaa-mm-dd para MySQL
        $partes_fecha = explode('-', $fecha_input);
        $fecha_mysql = $partes_fecha[2] . '-' . $partes_fecha[1] . '-' . $partes_fecha[0];
        // Validar que sea una fecha válida
        if (!checkdate((int)$partes_fecha[1], (int)$partes_fecha[0], (int)$partes_fecha[2])) {
            throw new Exception("La fecha de adquisición no es válida.");
        }
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $usuario_id = $_SESSION['usuario_id'];
        $clave_accion_ingresada = hash('sha256', $_POST['clave_accion']);
        $sql_clave = "SELECT clave_accion FROM usuarios WHERE id = ?";
        $stmt_clave = mysqli_prepare($con, $sql_clave);
        mysqli_stmt_bind_param($stmt_clave, "i", $usuario_id);
        mysqli_stmt_execute($stmt_clave);
        $res_clave = mysqli_stmt_get_result($stmt_clave);
        $row_clave = mysqli_fetch_assoc($res_clave);
        if (!$row_clave || $row_clave['clave_accion'] !== $clave_accion_ingresada) {
            throw new Exception("Clave de acción incorrecta.");
        }

        // Recuperar y sanitizar datos del bien
        $codigo_unico = mysqli_real_escape_string($con, $_POST['codigo_unico']);
        $tipo_bien = mysqli_real_escape_string($con, $_POST['tipo_bien']);
        $subcategoria = mysqli_real_escape_string($con, $_POST['subcategoria']);
        $descripcion = trim(str_replace(["\r", "\n"], ' ', $_POST['descripcion']));
        $fecha_adquisicion = mysqli_real_escape_string($con, $fecha_mysql);
        $estado_conservacion = mysqli_real_escape_string($con, $_POST['estado_conservacion']);
        $responsable_patrimonial = mysqli_real_escape_string($con, $_POST['responsable_patrimonial']);
        $precio_adquisicion = (float)$_POST['precio_adquisicion'];
        $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;
        $ubicacion = mysqli_real_escape_string($con, $_POST['ubicacion']);
        $notas = mysqli_real_escape_string($con, $_POST['notas']);
        $documento_soporte = '';

        // Validar código único
        $sql_verificar = "SELECT id FROM bienes_publicos WHERE codigo_unico = ?";
        $stmt = mysqli_prepare($con, $sql_verificar);
        mysqli_stmt_bind_param($stmt, "s", $codigo_unico);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            throw new Exception("El código único ya está registrado");
        }

        // Procesar documento
        if (isset($_FILES['documento_soporte']) && $_FILES['documento_soporte']['size'] > 0) {
            $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
            $file_ext = strtolower(pathinfo($_FILES['documento_soporte']['name'], PATHINFO_EXTENSION));
            if (!in_array($file_ext, $allowed)) {
                throw new Exception("Solo se permiten archivos PDF, JPG y PNG");
            }
            if ($_FILES['documento_soporte']['size'] > 5242880) {
                throw new Exception("El archivo excede el tamaño máximo de 5MB");
            }
            $target_dir = "documentos/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            $nombre_archivo = $codigo_unico . '_' . time() . '.' . $file_ext;
            $target_file = $target_dir . $nombre_archivo;
            if (!move_uploaded_file($_FILES['documento_soporte']['tmp_name'], $target_file)) {
                throw new Exception("Error al subir el documento");
            }
            $documento_soporte = $target_file;
        }

        // Insertar en base de datos (sentencia preparada)
        $sql = "INSERT INTO bienes_publicos (
            codigo_unico, tipo_bien, subcategoria, descripcion, 
            fecha_adquisicion, estado_conservacion, 
            responsable_patrimonial, 
            documento_soporte, precio_adquisicion, cantidad, ubicacion, notas
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssssdiss", 
            $codigo_unico,
            $tipo_bien,
            $subcategoria,
            $descripcion,
            $fecha_adquisicion,
            $estado_conservacion,
            $responsable_patrimonial,
            $documento_soporte,
            $precio_adquisicion,
            $cantidad,
            $ubicacion,
            $notas
        );

        if (!mysqli_stmt_execute($stmt)) {
            if ($documento_soporte && file_exists($documento_soporte)) {
                unlink($documento_soporte); // Eliminar archivo subido
            }
            throw new Exception("Error al registrar: " . mysqli_error($con));
        }

        // Registrar movimiento de incorporación
        $bien_id = mysqli_insert_id($con);
        $sql_movimiento = "INSERT INTO movimientos (bien_id, tipo_movimiento, fecha, cantidad, documento_soporte, responsable) 
                           VALUES (?, 'Incorporación', CURDATE(), ?, ?, ?)";

        $stmt_movimiento = mysqli_prepare($con, $sql_movimiento);
        mysqli_stmt_bind_param($stmt_movimiento, "iiss", $bien_id, $cantidad, $documento_soporte, $responsable_patrimonial);
        mysqli_stmt_execute($stmt_movimiento);

        // Registrar actividad de usuario
        registrar_actividad($con, "Registrar Bien", "Código: $codigo_unico, Descripción: $descripcion");

        $success = "Bien registrado exitosamente";
        $accion = 'consulta'; // Redirigir a consulta después de registro

    } catch (Exception $e) {
        $error = $e->getMessage();
        $accion = 'registro';
    }
}

// Procesar búsqueda o mostrar todos los bienes incorporados
if ($accion == 'consulta') {
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

// Mostrar mensajes de éxito/error tras eliminación
if (isset($_GET['success']) && $_GET['success'] == 'eliminado') {
    $success = "Bien eliminado correctamente.";
}
if (isset($_GET['error']) && $_GET['error'] != '') {
    $error = htmlspecialchars($_GET['error']);
}

if (isset($_GET['ver'])) {
    $id = (int)$_GET['ver'];
    $sql = "SELECT * FROM bienes_publicos WHERE id = $id";
    $bien_detalle = mysqli_fetch_assoc(mysqli_query($con, $sql));
}

if (isset($_GET['editar'])) {
    $id = (int)$_GET['editar'];
    $sql = "SELECT * FROM bienes_publicos WHERE id = $id";
    $bien_editar = mysqli_fetch_assoc(mysqli_query($con, $sql));
    $accion = 'editar_bien';
}

// Eliminar bien (clave de acción como confirmación, usando el modal visual)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && isset($_POST['clave_accion']) && !isset($_POST['editar_bien']) && !isset($_POST['guardar_edicion']) && !isset($_POST['registrar_bien_final'])) {
    try {
        if (!isset($_POST['clave_accion']) || empty($_POST['clave_accion'])) {
            throw new Exception("Debe ingresar la clave de acción para eliminar el bien.");
        }
        if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
            throw new Exception("ID de bien inválido.");
        }
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (!isset($_SESSION['usuario_id'])) {
            throw new Exception("Sesión expirada. Vuelva a iniciar sesión.");
        }
        $usuario_id = $_SESSION['usuario_id'];
        $clave_accion_ingresada = hash('sha256', $_POST['clave_accion']);
        $sql_clave = "SELECT clave_accion FROM usuarios WHERE id = ?";
        $stmt_clave = mysqli_prepare($con, $sql_clave);
        mysqli_stmt_bind_param($stmt_clave, "i", $usuario_id);
        mysqli_stmt_execute($stmt_clave);
        $res_clave = mysqli_stmt_get_result($stmt_clave);
        $row_clave = mysqli_fetch_assoc($res_clave);
        if (!$row_clave || $row_clave['clave_accion'] !== $clave_accion_ingresada) {
            throw new Exception("Clave de acción incorrecta.");
        }
        $id = (int)$_POST['id'];

        // Verifica que el bien exista antes de eliminar
        $sql_check = "SELECT id FROM bienes_publicos WHERE id = ?";
        $stmt_check = mysqli_prepare($con, $sql_check);
        mysqli_stmt_bind_param($stmt_check, "i", $id);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        if (mysqli_stmt_num_rows($stmt_check) === 0) {
            throw new Exception("El bien no existe o ya fue eliminado.");
        }

        // Eliminar bien
        $sql = "DELETE FROM bienes_publicos WHERE id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            registrar_actividad($con, "Eliminar Bien", "ID: $id");
            $success = "Bien eliminado correctamente.";
        } else {
            $error = "Error al eliminar: " . mysqli_error($con);
        }
        $accion = 'consulta';
    } catch (Exception $e) {
        $error = $e->getMessage();
        $accion = 'consulta';
    }
}

// Edición de bien (dos pasos)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editar_bien'])) {
    // Mostrar formulario de edición con los datos actuales
    $id = $_POST['id'];
    $sql = "SELECT * FROM bienes_publicos WHERE id = $id";
    $bien_editar = mysqli_fetch_assoc(mysqli_query($con, $sql));
    $accion = 'editar_bien';
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar_edicion'])) {
    // Guardar datos editados en sesión y mostrar formulario de clave de acción
    session_start();
    $_SESSION['editar_bien_id'] = $_POST['id'];
    $_SESSION['editar_bien_datos'] = $_POST;
    $accion = 'clave_accion_editar';
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirmar_editar'])) {
    try {
        if (!isset($_POST['clave_accion']) || empty($_POST['clave_accion'])) {
            throw new Exception("Debe ingresar la clave de acción para editar el bien.");
        }
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
            throw new Exception("Clave de acción incorrecta.");
        }
        $id = $_SESSION['editar_bien_id'];
        $datos = $_SESSION['editar_bien_datos'];
        unset($_SESSION['editar_bien_id'], $_SESSION['editar_bien_datos']);

        // Validar y convertir fecha_adquisicion si viene en formato dd-mm-aaaa
        $fecha_input = trim($datos['fecha_adquisicion']);
        if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $fecha_input)) {
            $partes_fecha = explode('-', $fecha_input);
            $fecha_mysql = $partes_fecha[2] . '-' . $partes_fecha[1] . '-' . $partes_fecha[0];
            if (!checkdate((int)$partes_fecha[1], (int)$partes_fecha[0], (int)$partes_fecha[2])) {
                throw new Exception("La fecha de adquisición no es válida.");
            }
        } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_input)) {
            $fecha_mysql = $fecha_input;
        } else {
            throw new Exception("La fecha de adquisición debe tener el formato DD-MM-AAAA o AAAA-MM-DD.");
        }

        // Sanitizar y preparar datos
        $tipo_bien = mysqli_real_escape_string($con, $datos['tipo_bien']);
        $subcategoria = mysqli_real_escape_string($con, $datos['subcategoria']);
        $descripcion = trim(str_replace(["\r", "\n"], ' ', $datos['descripcion']));
        $fecha_adquisicion = mysqli_real_escape_string($con, $fecha_mysql);
        $estado_conservacion = mysqli_real_escape_string($con, $datos['estado_conservacion']);
        $responsable_patrimonial = mysqli_real_escape_string($con, $datos['responsable_patrimonial']);
        $precio_adquisicion = isset($datos['precio_adquisicion']) ? (float)$datos['precio_adquisicion'] : 0;
        $cantidad = isset($datos['cantidad']) ? (int)$datos['cantidad'] : 1;
        $ubicacion = mysqli_real_escape_string($con, $datos['ubicacion']);
        $notas = mysqli_real_escape_string($con, $datos['notas']);

        $sql = "UPDATE bienes_publicos SET
            tipo_bien = ?, subcategoria = ?, descripcion = ?, fecha_adquisicion = ?, estado_conservacion = ?,
            responsable_patrimonial = ?, precio_adquisicion = ?, cantidad = ?, ubicacion = ?, notas = ?
            WHERE id = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssdisi",
            $tipo_bien,
            $subcategoria,
            $descripcion,
            $fecha_adquisicion,
            $estado_conservacion,
            $responsable_patrimonial,
            $precio_adquisicion,
            $cantidad,
            $ubicacion,
            $notas,
            $id
        );
        if (mysqli_stmt_execute($stmt)) {
            registrar_actividad($con, "Editar Bien", "ID: $id");
            $success = "Bien actualizado correctamente.";
        } else {
            $error = "Error al actualizar: " . mysqli_error($con);
        }
        $accion = 'consulta';
    } catch (Exception $e) {
        $error = $e->getMessage();
        $accion = 'consulta';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sistema de Incorporación de Bienes</title>
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
        .action-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .action-btn:hover {
            background-color: #0056b3;
            color: #fff;
        }
        .edit-table { width: 100%; border-collapse: collapse; margin: 0 auto 20px auto; }
        .edit-table td { padding: 10px; border: 1px solid #ddd; }
        .edit-table label { font-weight: bold; }
        textarea {
            width: 100%;
            min-height: 38px;
            resize: vertical;
            overflow-y: hidden;
            box-sizing: border-box;
        }
        input[type="text"] {
            width: 100%;
            min-height: 38px;
            box-sizing: border-box;
        }
        @media (max-width: 600px) {
            .modal-content { padding: 10px; min-width: 0; }
        }
    </style>
    <script>
        function actualizarSubcategorias() {
            const tipo = document.getElementById('tipo_bien').value;
            const subcatSelect = document.getElementById('subcategoria');
            subcatSelect.innerHTML = '<option value="">Cargando...</option>';

            const subcategorias = {
                Inmueble: [
                    '1-01: Edificios para oficinas',
                    '1-02: Establecimientos culturales',
                    '1-03: Fines asistenciales y de protección social',
                    '1-04: Obras públicas',
                    '1-05: Fines agropecuarios',
                    '1-06: Fines industriales',
                    '1-07: Cárceles y reformatorios',
                    '1-08: Acueductos y obras hidráulicas',
                    '1-09: Plantas eléctricas',
                    '1-10: Telecomunicaciones',
                    '1-11: Otros servicios públicos',
                    '1-12: Instalaciones portuarias',
                    '1-13: Aeródromos y aeropuertos',
                    '1-14: Ferrocarriles',
                    '1-15: Alojamiento y hoteles',
                    '1-16: Uso policial',
                    '1-17: Otras construcciones',
                    '1-18: Construcciones en proceso',
                    '1-19: Predios urbanos',
                    '1-20: Terrenos rurales',
                    '1-21: Minas'
                ],
                Mueble: [
                    '2-01: Máquinas y equipos de oficina',
                    '2-02: Mobiliario de alojamiento',
                    '2-03: Equipos de construcción y taller',
                    '2-04: Equipos de transporte',
                    '2-05: Telecomunicaciones',
                    '2-06: Equipos médico-quirúrgicos',
                    '2-07: Equipos científicos y educativos',
                    '2-08: Colecciones culturales',
                    '2-09: Armamento y defensa',
                    '2-10: Instalaciones provisionales',
                    '2-11: Semovientes',
                    '2-12: Otros elementos'
                ]
            };

            subcatSelect.innerHTML = subcategorias[tipo] 
                ? subcategorias[tipo].map(opt => `<option value="${opt}">${opt}</option>`).join('')
                : '<option value="">Seleccione tipo primero</option>';
        }

        function abrirModalDetalle(id) {
            window.location.href = '?accion=consulta&ver=' + id;
        }

        function mostrarClaveAccionRegistro() {
            document.getElementById('form-datos-bien').style.display = 'none';
            document.getElementById('form-clave-accion').style.display = '';
        }

        function volverDatosRegistro() {
            document.getElementById('form-clave-accion').style.display = 'none';
            document.getElementById('form-datos-bien').style.display = '';
        }

        function mostrarClaveAccionEliminar(id) {
            document.getElementById('form-eliminar-' + id).style.display = 'none';
            document.getElementById('form-clave-accion-eliminar-' + id).style.display = '';
        }

        function volverEliminar(id) {
            document.getElementById('form-clave-accion-eliminar-' + id).style.display = 'none';
            document.getElementById('form-eliminar-' + id).style.display = '';
        }

        function mostrarClaveAccionEditar(id) {
            document.getElementById('form-editar-' + id).style.display = 'none';
            document.getElementById('form-clave-accion-editar-' + id).style.display = '';
        }

        function volverEditar(id) {
            document.getElementById('form-clave-accion-editar-' + id).style.display = 'none';
            document.getElementById('form-editar-' + id).style.display = '';
        }

        // Modal Clave de Acción
        function abrirModalClaveAccion(tipo, id = null) {
            document.getElementById('modalClaveAccion').style.display = 'flex';
            document.getElementById('clave_accion_tipo').value = tipo;
            document.getElementById('clave_accion_id').value = id || '';
            document.getElementById('clave_accion_input').value = '';
            document.getElementById('clave_accion_input').focus();
        }
        function cerrarModalClaveAccion() {
            document.getElementById('modalClaveAccion').style.display = 'none';
        }
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('form-modal-clave-accion').addEventListener('submit', function(e) {
                e.preventDefault();
                var tipo = document.getElementById('clave_accion_tipo').value;
                var id = document.getElementById('clave_accion_id').value;
                var clave = document.getElementById('clave_accion_input').value;

                if (tipo === 'eliminar') {
                    var formEliminar = document.getElementById('form-eliminar-' + id);
                    if (formEliminar) {
                        var prev = formEliminar.querySelector('input[name="clave_accion"]');
                        if (prev) formEliminar.removeChild(prev);
                        var inputClave = document.createElement('input');
                        inputClave.type = 'hidden';
                        inputClave.name = 'clave_accion';
                        inputClave.value = clave;
                        formEliminar.appendChild(inputClave);
                        formEliminar.submit();
                    } else {
                        var form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '';
                        form.innerHTML = '<input type="hidden" name="id" value="' + id + '">' +
                                         '<input type="hidden" name="clave_accion" value="' + clave + '">';
                        document.body.appendChild(form);
                        form.submit();
                    }
                } else if (tipo === 'registro') {
                    var f = document.createElement('form');
                    f.method = 'POST';
                    f.innerHTML = '<input type="hidden" name="confirmar_registro" value="1">' +
                                  '<input type="hidden" name="clave_accion" value="'+clave+'">';
                    document.body.appendChild(f);
                    f.submit();
                } else if (tipo === 'editar') {
                    var formEditar = document.getElementById('form-editar-bien');
                    if (formEditar) {
                        var inputClave = document.createElement('input');
                        inputClave.type = 'hidden';
                        inputClave.name = 'clave_accion';
                        inputClave.value = clave;
                        formEditar.appendChild(inputClave);

                        var inputConfirm = document.createElement('input');
                        inputConfirm.type = 'hidden';
                        inputConfirm.name = 'confirmar_editar';
                        inputConfirm.value = '1';
                        formEditar.appendChild(inputConfirm);

                        formEditar.submit();
                    } else {
                        var f = document.createElement('form');
                        f.method = 'POST';
                        f.innerHTML = '<input type="hidden" name="confirmar_editar" value="1">' +
                                    '<input type="hidden" name="clave_accion" value="'+clave+'">';
                        if (id) {
                            f.innerHTML += '<input type="hidden" name="id" value="'+id+'">';
                        }
                        document.body.appendChild(f);
                        f.submit();
                    }
                }
                cerrarModalClaveAccion();
            });
        });
    </script>
</head>
<body>

<div class="container">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <img src="logo_iaceb.png" alt="Logo IACEB" style="height:60px;">
        <img src="logo_barinas.png" alt="Logo Barinas" style="height:60px;">
    </div>
    <!-- Menú interno -->
    <div class="menu-interno">
        <a href="?accion=consulta" class="<?= $accion == 'consulta' ? 'activo' : '' ?>">Consultar Bienes</a>
        <a href="?accion=registro" class="<?= $accion == 'registro' ? 'activo' : '' ?>">Incorporar Nuevo Bien</a>
    </div>

    <!-- Contenido dinámico -->
    <div class="seccion-contenido">
        <?php if($accion == 'registro'): ?>
            <!-- Sección de Registro -->
            <?php if($error): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="success"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" id="form-datos-bien">
                <table class="form-table">
                    <tr>
                        <td width="30%"><label>Código Único:</label></td>
                        <td><input type="text" name="codigo_unico" required></td>
                    </tr>
                    <tr>
                        <td><label>Tipo de Bien:</label></td>
                        <td>
                            <select name="tipo_bien" id="tipo_bien" onchange="actualizarSubcategorias()" required>
                                <option value="">Seleccionar...</option>
                                <option value="Mueble">Mueble</option>
                                <option value="Inmueble">Inmueble</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Subcategoría:</label></td>
                        <td>
                            <select name="subcategoria" id="subcategoria" required>
                                <option value="">Seleccione tipo primero</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Descripción:</label></td>
                        <td><textarea name="descripcion" rows="3" required oninput="autoResizeField(this)"></textarea></td>
                    </tr>
                    <tr>
                        <td><label>Fecha de Adquisición:</label></td>
                        <td><input type="date" name="fecha_adquisicion" required></td>
                    </tr>
                    <tr>
                        <td><label>Estado de Conservación:</label></td>
                        <td>
                            <select name="estado_conservacion" required>
                                <option value="Optimo">Óptimo</option>
                                <option value="Regular">Regular</option>
                                <option value="Deteriorado">Deteriorado</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Responsable Patrimonial:</label></td>
                        <td><input type="text" name="responsable_patrimonial" required></td>
                    </tr>
                    <tr>
                        <td><label>Ubicación:</label></td>
                        <td>
                            <select name="ubicacion" required>
                                <option value="Patrimonio">Patrimonio</option>
                                <option value="Prensa">Prensa</option>
                                <option value="Presidencia">Presidencia</option>
                                <option value="Administración">Administración</option>
                                <option value="Recursos Humanos">Recursos Humanos</option>
                                <option value="Almacén">Almacén</option>
                                <option value="Escuela de Música">Escuela de Música</option>
                                <option value="Escuela de Artes Escénicas">Escuela de Artes Escénicas</option>
                                <option value="Escuela de Artes Plásticos">Escuela de Artes Plásticos</option>
                                <option value="Auditorio">Auditorio</option>
                                <option value="Banda del estado barinas">Banda del estado barinas</option>
                                <option value="Ateneo">Ateneo</option>
                                <option value="No Ubicados">No Ubicados</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><label>Precio de Adquisición:</label></td>
                        <td><input type="number" step="0.01" name="precio_adquisicion" required></td>
                    </tr>
                    <tr>
                        <td><label>Cantidad:</label></td>
                        <td><input type="number" name="cantidad" min="1" value="1" required></td>
                    </tr>
                    <tr>
                        <td><label>Notas:</label></td>
                        <td><textarea name="notas" rows="2" oninput="autoResizeField(this)"></textarea></td>
                    </tr>
                    <tr>
                        <td><label>Documento Soporte:</label></td>
                        <td><input type="file" name="documento_soporte" accept=".pdf,.jpg,.png"></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:center;">
                            <button type="button" onclick="abrirModalClaveAccionRegistro()">Registrar Bien</button>
                        </td>
                    </tr>
                </table>
            </form>
            <!-- Modal flotante para clave de acción -->
            <div class="modal-bg" id="modalClaveAccionRegistro" style="display:none;">
                <div class="modal-content">
                    <h3>Clave de Acción</h3>
                    <form method="POST" enctype="multipart/form-data" id="form-modal-clave-accion-registro">
                        <!-- Aquí se insertarán los campos del formulario principal por JS -->
                        <input type="password" name="clave_accion" id="clave_accion_input_registro" placeholder="Clave de acción" required style="width:100%;margin-bottom:10px;">
                        <div style="text-align:center;">
                            <button type="submit" name="registrar_bien_final">Confirmar Registro</button>
                            <button type="button" onclick="cerrarModalClaveAccionRegistro()" class="action-btn action-btn-lista">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php elseif($accion == 'clave_accion'): ?>
            <!-- Mostrar solo el formulario de clave de acción si se viene del paso anterior -->
            <form method="POST" enctype="multipart/form-data" id="form-clave-accion">
                <table class="form-table">
                    <tr>
                        <td><label>Clave de Acción:</label></td>
                        <td><input type="password" name="clave_accion" required></td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align:center;">
                            <button type="submit" name="confirmar_registro">Registrar Bien</button>
                            <button type="button" onclick="window.location.href='?accion=registro'" class="action-btn action-btn-lista">Volver</button>
                        </td>
                    </tr>
                </table>
            </form>
        <?php elseif($accion == 'editar_bien' && isset($bien_editar)): ?>
            <div class="modal-bg" id="modalEditar">
                <div class="modal-content">
                    <h3>Editar Bien</h3>
                    <form method="POST" id="form-editar-bien" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $bien_editar['id'] ?>">
                        <table class="edit-table">
                            <?php foreach($bien_editar as $campo => $valor): ?>
                                <?php
                                if ($campo == 'id' || $valor === null || $valor === '') continue;
                                // No permitir edición del campo 'estado'
                                if ($campo === 'estado') {
                                    ?>
                                    <tr>
                                        <td width="35%"><label><?= htmlspecialchars(ucwords(str_replace('_',' ',$campo))) ?>:</label></td>
                                        <td>
                                            <input type="text" name="estado" value="<?= htmlspecialchars($valor) ?>" readonly style="background:#eee;">
                                        </td>
                                    </tr>
                                    <?php
                                    continue;
                                }
                                // Mostrar la fecha registrada (no editable) antes del campo editable de fecha_adquisicion
                                if ($campo === 'fecha_adquisicion') {
                                    ?>
                                    <tr>
                                        <td width="35%"><label>Fecha registrada:</label></td>
                                        <td>
                                            <input type="text" value="<?= date('d/m/Y', strtotime($valor)) ?>" readonly style="background:#eee;">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="35%"><label><?= htmlspecialchars(ucwords(str_replace('_',' ',$campo))) ?>:</label></td>
                                        <td>
                                            <input type="date" name="<?= $campo ?>" value="<?= htmlspecialchars($valor) ?>" required>
                                        </td>
                                    </tr>
                                    <?php
                                    continue;
                                }
                                ?>
                                <tr>
                                    <td width="35%"><label><?= htmlspecialchars(ucwords(str_replace('_',' ',$campo))) ?>:</label></td>
                                    <td>
                                        <?php if ($campo === 'tipo_bien'): ?>
                                            <select name="tipo_bien" id="tipo_bien_edit" onchange="actualizarSubcategoriasEditar()" required>
                                                <option value="">Seleccionar...</option>
                                                <option value="Mueble" <?= $valor=='Mueble'?'selected':'' ?>>Mueble</option>
                                                <option value="Inmueble" <?= $valor=='Inmueble'?'selected':'' ?>>Inmueble</option>
                                            </select>
                                        <?php elseif ($campo === 'subcategoria'): ?>
                                            <select name="subcategoria" id="subcategoria_edit" required>
                                                <!-- Las opciones se llenan por JS según tipo_bien -->
                                            </select>
                                            <script>
                                                // Llenar subcategoría al abrir modal de edición
                                                function actualizarSubcategoriasEditar() {
                                                    var tipo = document.getElementById('tipo_bien_edit').value;
                                                    var subcatSelect = document.getElementById('subcategoria_edit');
                                                    var subcategorias = {
                                                        Inmueble: [
                                                            '1-01: Edificios para oficinas',
                                                            '1-02: Establecimientos culturales',
                                                            '1-03: Fines asistenciales y de protección social',
                                                            '1-04: Obras públicas',
                                                            '1-05: Fines agropecuarios',
                                                            '1-06: Fines industriales',
                                                            '1-07: Cárceles y reformatorios',
                                                            '1-08: Acueductos y obras hidráulicas',
                                                            '1-09: Plantas eléctricas',
                                                            '1-10: Telecomunicaciones',
                                                            '1-11: Otros servicios públicos',
                                                            '1-12: Instalaciones portuarias',
                                                            '1-13: Aeródromos y aeropuertos',
                                                            '1-14: Ferrocarriles',
                                                            '1-15: Alojamiento y hoteles',
                                                            '1-16: Uso policial',
                                                            '1-17: Otras construcciones',
                                                            '1-18: Construcciones en proceso',
                                                            '1-19: Predios urbanos',
                                                            '1-20: Terrenos rurales',
                                                            '1-21: Minas'
                                                        ],
                                                        Mueble: [
                                                            '2-01: Máquinas y equipos de oficina',
                                                            '2-02: Mobiliario de alojamiento',
                                                            '2-03: Equipos de construcción y taller',
                                                            '2-04: Equipos de transporte',
                                                            '2-05: Telecomunicaciones',
                                                            '2-06: Equipos médico-quirúrgicos',
                                                            '2-07: Equipos científicos y educativos',
                                                            '2-08: Colecciones culturales',
                                                            '2-09: Armamento y defensa',
                                                            '2-10: Instalaciones provisionales',
                                                            '2-11: Semovientes',
                                                            '2-12: Otros elementos'
                                                        ]
                                                    };
                                                    subcatSelect.innerHTML = '';
                                                    if (subcategorias[tipo]) {
                                                        subcategorias[tipo].forEach(function(opt) {
                                                            var selected = (opt === <?= json_encode($bien_editar['subcategoria']) ?>) ? 'selected' : '';
                                                            subcatSelect.innerHTML += '<option value="'+opt+'" '+selected+'>'+opt+'</option>';
                                                        });
                                                    } else {
                                                        subcatSelect.innerHTML = '<option value="">Seleccione tipo primero</option>';
                                                    }
                                                }
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    actualizarSubcategoriasEditar();
                                                });
                                            </script>
                                        <?php elseif ($campo === 'descripcion' || $campo === 'notas'): ?>
                                            <textarea name="<?= $campo ?>" rows="2" required oninput="autoResizeField(this)"><?= htmlspecialchars($valor) ?></textarea>
                                        <?php elseif ($campo === 'estado_conservacion'): ?>
                                            <select name="estado_conservacion" required>
                                                <option value="Optimo" <?= $valor=='Optimo'?'selected':'' ?>>Óptimo</option>
                                                <option value="Regular" <?= $valor=='Regular'?'selected':'' ?>>Regular</option>
                                                <option value="Deteriorado" <?= $valor=='Deteriorado'?'selected':'' ?>>Deteriorado</option>
                                            </select>
                                        <?php elseif ($campo === 'ubicacion'): ?>
                                            <select name="ubicacion" required>
                                                <?php
                                                $ubicaciones = [
                                                    'Patrimonio', 'Prensa', 'Presidencia', 'Administración',
                                                    'Recursos Humanos', 'Almacén', 'Escuela de Música',
                                                    'Escuela de Artes Escénicas', 'Escuela de Artes Plásticos',
                                                    'Auditorio', 'Banda del estado barinas', 'Ateneo',
                                                    'No Ubicados'
                                                ];
                                                foreach ($ubicaciones as $u):
                                                ?>
                                                    <option value="<?= $u ?>" <?= $valor==$u?'selected':'' ?>><?= $u ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php elseif ($campo === 'fecha_actualizacion'): ?>
                                            <input type="date" name="<?= $campo ?>" value="<?= htmlspecialchars($valor) ?>" required>
                                        <?php elseif ($campo === 'precio_adquisicion' || $campo === 'valor_mercado'): ?>
                                            <input type="number" step="0.01" name="<?= $campo ?>" value="<?= htmlspecialchars($valor) ?>" required> Bs
                                        <?php elseif ($campo === 'cantidad'): ?>
                                            <input type="number" name="cantidad" min="1" value="<?= htmlspecialchars($valor) ?>" required>
                                        <?php elseif ($campo === 'documento_soporte'): ?>
                                            <?php if ($valor): ?>
                                                <a href="<?= htmlspecialchars($valor) ?>" target="_blank" class="action-btn" style="background-color:#28a745;">Ver documento adjunto actual</a><br>
                                            <?php endif; ?>
                                            <input type="file" name="documento_soporte" accept=".pdf,.jpg,.jpeg,.png">
                                            <small>Puedes subir un nuevo archivo para reemplazar el actual.</small>
                                        <?php else: ?>
                                            <input type="text" name="<?= $campo ?>" value="<?= htmlspecialchars($valor) ?>" required oninput="autoResizeField(this)">
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="2" style="text-align:center;">
                                    <button type="submit" name="guardar_edicion" class="action-btn">Guardar Cambios</button>
                                    <button type="button" onclick="window.location.href='?accion=consulta'" class="action-btn action-btn-lista">Cancelar</button>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        <?php elseif($accion == 'clave_accion_editar'): ?>
            <div class="modal-bg" id="modalClaveEditar">
                <div class="modal-content">
                    <h3>Confirmar Edición</h3>
                    <form method="POST" id="form-clave-accion-editar">
                        <table class="form-table">
                            <tr>
                                <td><label>Clave de Acción:</label></td>
                                <td><input type="password" name="clave_accion" required></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align:center;">
                                    <button type="submit" name="confirmar_editar">Guardar Cambios</button>
                                    <button type="button" onclick="window.location.href='?accion=consulta'" class="action-btn action-btn-lista">Volver</button>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <!-- Sección de Consulta -->
            <form method="GET" class="search-box">
                <input type="hidden" name="accion" value="consulta">
                <input type="text" name="busqueda" placeholder="Buscar por código o descripción" value="<?= isset($_GET['busqueda']) ? htmlspecialchars($_GET['busqueda']) : '' ?>">
                <button type="submit">Buscar</button>
            </form>

            <?php if(!empty($resultados)): ?>
            <table class="results-table">
                <tr>
                    <th>Código</th>
                    <th>Tipo</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
                <?php while($bien = mysqli_fetch_assoc($resultados)): ?>
                <tr>
                    <td><?= htmlspecialchars($bien['codigo_unico']) ?></td>
                    <td><?= htmlspecialchars($bien['tipo_bien']) ?></td>
                    <td><?= htmlspecialchars(substr($bien['descripcion'], 0, 50)) ?>...</td>
                    <td><?= date('d/m/Y', strtotime($bien['fecha_adquisicion'])) ?></td>
                    <td><?= htmlspecialchars($bien['estado_conservacion']) ?></td>
                    <td>
                        <!-- Editar (dos pasos) -->
                        <form method="POST" id="form-editar-<?= $bien['id'] ?>" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $bien['id'] ?>">
                            <button type="submit" name="editar_bien" class="action-btn">Editar</button>
                        </form>
                        <!-- Eliminar (abrir modal de clave) -->
                        <button type="button" class="action-btn" style="background-color:#e74c3c;" 
        onclick="abrirModalClaveAccion('eliminar', <?= $bien['id'] ?>)">Eliminar</button>
                        <button type="button" class="action-btn" onclick="abrirModalDetalle(<?= $bien['id'] ?>)">Ver Detalles</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php else: ?>
            <p>No se encontraron bienes que coincidan con la búsqueda.</p>
            <?php endif; ?>

            <?php if($bien_detalle): ?>
            <div class="modal-bg" id="modalDetalle">
                <div class="modal-content">
                    <h3>Ficha Técnica Completa</h3>
                    <table class="form-table">
                        <?php foreach($bien_detalle as $campo => $valor): ?>
                            <?php if ($valor !== null && $valor !== ''): ?>
                            <tr>
                                <td width="30%"><strong><?= htmlspecialchars(ucwords(str_replace('_',' ',$campo))) ?></strong></td>
                                <td>
                                    <?php if ($campo === 'documento_soporte' && $valor): ?>
                                        <a href="<?= htmlspecialchars($valor) ?>" target="_blank" class="action-btn" style="background-color:#28a745;">Ver documento adjunto</a>
                                    <?php elseif ($campo === 'precio_adquisicion' || $campo === 'valor_mercado'): ?>
                                        <?= number_format($valor, 2) ?> Bs
                                    <?php elseif ($campo === 'fecha_adquisicion' || $campo === 'fecha_actualizacion'): ?>
                                        <?= date('d/m/Y', strtotime($valor)) ?>
                                    <?php else: ?>
                                        <?= nl2br(htmlspecialchars($valor)) ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </table>
                    <div style="margin-top: 20px; text-align:right;">
                        <button class="action-btn action-btn-lista" onclick="window.location.href='?accion=consulta'">Volver a la lista</button>
                    </div>
                </div>
            </div>
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
    // Interceptar el submit de los formularios para usar el modal
    document.getElementById('form-modal-clave-accion').onsubmit = function(e) {
        e.preventDefault();
        var tipo = document.getElementById('clave_accion_tipo').value;
        var id = document.getElementById('clave_accion_id').value;
        var clave = document.getElementById('clave_accion_input').value;

        if (tipo === 'registro') {
            var f = document.createElement('form');
            f.method = 'POST';
            f.innerHTML = '<input type="hidden" name="confirmar_registro" value="1">' +
                          '<input type="hidden" name="clave_accion" value="'+clave+'">';
            document.body.appendChild(f);
            f.submit();
        } else if (tipo === 'editar') {
            var formEditar = document.getElementById('form-editar-bien');
            if (formEditar) {
                var inputClave = document.createElement('input');
                inputClave.type = 'hidden';
                inputClave.name = 'clave_accion';
                inputClave.value = clave;
                formEditar.appendChild(inputClave);

                var inputConfirm = document.createElement('input');
                inputConfirm.type = 'hidden';
                inputConfirm.name = 'confirmar_editar';
                inputConfirm.value = '1';
                formEditar.appendChild(inputConfirm);

                formEditar.submit();
            } else {
                var f = document.createElement('form');
                f.method = 'POST';
                f.innerHTML = '<input type="hidden" name="confirmar_editar" value="1">' +
                            '<input type="hidden" name="clave_accion" value="'+clave+'">';
                if (id) {
                    f.innerHTML += '<input type="hidden" name="id" value="'+id+'">';
                }
                document.body.appendChild(f);
                f.submit();
            }
        } else if (tipo === 'eliminar') {
            // Enviar el formulario al formulario oculto de la fila correspondiente
            var formEliminar = document.getElementById('form-eliminar-' + id);
            if (formEliminar) {
                // Elimina cualquier input previo de clave_accion para evitar duplicados
                var prev = formEliminar.querySelector('input[name="clave_accion"]');
                if (prev) formEliminar.removeChild(prev);
                var inputClave = document.createElement('input');
                inputClave.type = 'hidden';
                inputClave.name = 'clave_accion';
                inputClave.value = clave;
                formEliminar.appendChild(inputClave);
                formEliminar.submit();
            }
        }
    };
</script>
</body>
</html>