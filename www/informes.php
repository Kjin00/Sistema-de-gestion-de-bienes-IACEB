<?php
// Incluye la conexión a la base de datos
include('conexion.php');
$con = conectar();

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

// Obtiene la lista de unidades para los filtros de informes
$unidades = [
    'Gerencia de Patrimonio Cultural.', 'Gerencia de Promoción y Difusión Cultural', 'Presidencia', 'Gerencia de Administración', 'Recursos Humanos',
    'Deposito', 'Escuela de Música', 'Escuela de Artes Escénicas', 'Escuela de Artes Plásticos',
    'Auditorio', 'Banda del estado barinas', 'Ateneo',
    'No Ubicados'
];

// Obtiene las subcategorías únicas para los filtros
$subcategorias_disponibles = [];
$sql_subcat = "SELECT DISTINCT subcategoria FROM bienes_publicos ORDER BY subcategoria ASC";
$res_subcat = mysqli_query($con, $sql_subcat);
while ($row = mysqli_fetch_assoc($res_subcat)) {
    if ($row['subcategoria']) $subcategorias_disponibles[] = $row['subcategoria'];
}

// Obtiene los valores seleccionados en los filtros
$tipo_sel = isset($_GET['tipo']) ? $_GET['tipo'] : '';
$unidad_sel = isset($_GET['unidad_trabajo']) ? $_GET['unidad_trabajo'] : '';
$subcat_sel = isset($_GET['subcategoria']) ? $_GET['subcategoria'] : '';

// Obtiene las fechas disponibles según los filtros seleccionados
$fechas_disponibles = [];
$años_disponibles = [];
if ($tipo_sel && $unidad_sel) {
    $tabla = 'bienes_publicos';
    $campo_fecha = 'fecha_adquisicion';
    $campo_estado = '';
    // Define el estado según el tipo de informe
    if ($tipo_sel == 'BM-1') {
        $campo_estado = "AND estado = 'Incorporado'";
    } elseif ($tipo_sel == 'BM-2') {
        $campo_estado = "AND estado IN ('Incorporado','Desincorporado')";
    } elseif ($tipo_sel == 'BM-3') {
        $campo_estado = "AND estado = 'En investigación'";
    } elseif ($tipo_sel == 'BM-4') {
        $campo_estado = "";
    }
    // Fechas por mes
    $sql_fechas = "SELECT DISTINCT DATE_FORMAT($campo_fecha, '%Y-%m') as mes FROM $tabla WHERE ubicacion = ? $campo_estado ORDER BY mes DESC";
    $stmt_fechas = mysqli_prepare($con, $sql_fechas);
    mysqli_stmt_bind_param($stmt_fechas, "s", $unidad_sel);
    mysqli_stmt_execute($stmt_fechas);
    $res_fechas = mysqli_stmt_get_result($stmt_fechas);
    while ($row = mysqli_fetch_assoc($res_fechas)) {
        $fechas_disponibles[] = $row['mes'];
    }
    // Fechas por año
    $sql_años = "SELECT DISTINCT YEAR($campo_fecha) as año FROM $tabla WHERE ubicacion = ? $campo_estado ORDER BY año DESC";
    $stmt_años = mysqli_prepare($con, $sql_años);
    mysqli_stmt_bind_param($stmt_años, "s", $unidad_sel);
    mysqli_stmt_execute($stmt_años);
    $res_años = mysqli_stmt_get_result($stmt_años);
    while ($row = mysqli_fetch_assoc($res_años)) {
        $años_disponibles[] = $row['año'];
    }
} else {
    // Si no hay selección, muestra todos los meses y años posibles
    $tabla = 'bienes_publicos';
    $campo_fecha = 'fecha_adquisicion';
    // Meses
    $sql_fechas = "SELECT DISTINCT DATE_FORMAT($campo_fecha, '%Y-%m') as mes FROM $tabla ORDER BY mes DESC";
    $res_fechas = mysqli_query($con, $sql_fechas);
    while ($row = mysqli_fetch_assoc($res_fechas)) {
        $fechas_disponibles[] = $row['mes'];
    }
    // Años
    $sql_años = "SELECT DISTINCT YEAR($campo_fecha) as año FROM $tabla ORDER BY año DESC";
    $res_años = mysqli_query($con, $sql_años);
    while ($row = mysqli_fetch_assoc($res_años)) {
        $años_disponibles[] = $row['año'];
    }
}

// Exportar a PDF si se solicita
if (isset($_GET['exportar']) && $_GET['exportar'] === 'pdf' && isset($_GET['tipo'], $_GET['unidad_trabajo'])) {
    $fecha_param = isset($_GET['fecha']) ? $_GET['fecha'] : '';
    $año_param = isset($_GET['año']) ? $_GET['año'] : '';
    $nombre_pdf = $_GET['tipo'];
    if ($fecha_param) {
        $nombre_pdf .= '-' . $fecha_param;
    } elseif ($año_param) {
        $nombre_pdf .= '-' . $año_param;
    }
    $nombre_pdf .= '.pdf';

    registrar_actividad($con, "Generar Informe", "Tipo: {$_GET['tipo']}, Unidad: {$_GET['unidad_trabajo']}, Fecha: $fecha_param, Año: $año_param, PDF");
    ob_start();
    $html = mostrarFormato($_GET['tipo'], $con, $_GET['unidad_trabajo'], $fecha_param, true, $año_param); // true: modo PDF
    $html = ob_get_clean() . $html;

    require_once(__DIR__ . '/../tcpdf/tcpdf.php');
    $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->AddPage();
    $pdf->writeHTML($html);
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="'.$nombre_pdf.'"');
    $pdf->Output($nombre_pdf, 'D');
    exit;
}

// Registrar actividad al generar informe en pantalla
if (isset($_GET['tipo'], $_GET['unidad_trabajo']) && $_GET['tipo'] && $_GET['unidad_trabajo'] && (isset($_GET['fecha']) || isset($_GET['año']))) {
    $fecha_param = isset($_GET['fecha']) ? $_GET['fecha'] : '';
    $año_param = isset($_GET['año']) ? $_GET['año'] : '';
    registrar_actividad($con, "Generar Informe", "Tipo: {$_GET['tipo']}, Unidad: {$_GET['unidad_trabajo']}, Fecha: $fecha_param, Año: $año_param, Vista Web");
}

function generarEncabezado($modelo) {
    // Encabezado con logos y título
    return '
    <table style="width:100%;border-collapse:collapse;margin-bottom:0;">
        <tr>
            <td style="width:15%;text-align:left;">
                <img src="logo_iaceb.png" alt="Logo IACEB" style="height:50px;">
            </td>
            <td style="width:70%;text-align:center;font-weight:bold;font-size:16px;">
                INSTITUTO AUTÓNOMO DE CULTURA DEL ESTADO BARINAS<br>
                CONTROL DE BIENES
            </td>
            <td style="width:15%;text-align:right;">
                <img src="logo_barinas.png" alt="Logo Barinas" style="height:50px;">
            </td>
        </tr>
        <tr>
            <td colspan="3" style="text-align:right;font-size:13px;">MODELO: '.$modelo.'</td>
        </tr>
    </table>
    <table border="1" cellpadding="5" style="width: 100%; border-collapse: collapse; margin-top:0;">
    ';
}

function generarBM1($con, $unidad_trabajo, $fecha = '', $modo_pdf = false, $año = '', $subcat = '') {
    $fecha_informe = date('Y-m-d');
    $html = '
    <table border="1" cellpadding="4" style="width:100%;border-collapse:collapse;font-size:13px;">
        <tr>
            <td colspan="9" style="text-align:center;font-weight:bold;font-size:16px;">
                <img src="logo_iaceb.png" alt="Logo IACEB" style="height:40px;vertical-align:middle;float:left;">
                INVENTARIO DE BIENES MUEBLES (MB-01)
                <img src="logo_barinas.png" alt="Logo Barinas" style="height:40px;vertical-align:middle;float:right;">
            </td>
        </tr>
        <tr>
            <td colspan="9">Entidad Propietaria: <b>GOBERNACIÓN DEL ESTADO BARINAS</b></td>
        </tr>
        <tr>
            <td colspan="6">Sector Presupuestario: <b>DIRECCION Y COORDINACION EJECUTIVA</b></td>
            <td colspan="3">Servicio:<b>'.htmlspecialchars($unidad_trabajo).'</b></td>
        </tr>
        <tr>
            <td colspan="6">Unidad de trabajo: <b>INSTITUTO AUTONOMO DE CULTURA DEL ESTADO BARINAS</b></td>
            <td colspan="3">Código: <b>01.07.01.01</b></td>
        </tr>
        <tr>
            <td colspan="3">Estado: <b>BARINAS</b></td>
            <td colspan="3">Municipio: <b>BARINAS</b></td>
            <td colspan="3">Parroquia: <b>BARINAS</b></td>
        </tr>
        <tr>
            <td colspan="9">Fecha: <b>'.htmlspecialchars($fecha_informe).'</b></td>
        </tr>
    </table>
    <table border="1" cellpadding="4" style="width:100%;border-collapse:collapse;font-size:13px;">
        <tr>
            <th style="text-align:center;">Grup.</th>
            <th style="text-align:center;">Sub-Gru.</th>
            <th style="text-align:center;">Secc.</th>
            <th style="text-align:center;">Nº de Ident.</th>
            <th style="text-align:center;">Cant.</th>
            <th style="text-align:center;">NOMBRE Y DESCRIPCIÓN DE LOS ELEMENTOS</th>
            <th style="text-align:center;">Costo de<br>Adquisición Bs.</th>
            <th style="text-align:center;">Valor<br>Estimado Bs.</th>
        </tr>
    ';
    // Modifica la consulta para filtrar por año y subcategoría si están presentes
    $where = "WHERE estado = 'Incorporado' AND ubicacion = ?";
    $params = [$unidad_trabajo];
    $types = "s";
    if ($año) {
        $where .= " AND YEAR(fecha_adquisicion) = ?";
        $params[] = $año;
        $types .= "i";
    } elseif ($fecha) {
        $where .= " AND DATE_FORMAT(fecha_adquisicion, '%Y-%m') = ?";
        $params[] = $fecha;
        $types .= "s";
    }
    if ($subcat) {
        $where .= " AND subcategoria = ?";
        $params[] = $subcat;
        $types .= "s";
    }
    $sql = "SELECT subcategoria, codigo_unico, descripcion, cantidad, precio_adquisicion, estado_conservacion 
            FROM bienes_publicos 
            $where";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $total = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $total += $row['precio_adquisicion'];
        $grupo = $subgrupo = '';
        $seccion = '0';
        if (preg_match('/^(\d+)-(\d+)(?:-(\d+))?:/', $row['subcategoria'], $matches)) {
            $grupo = $matches[1];
            $subgrupo = $matches[2];
         
        }
        $html .= '
        <tr>
            <td style="text-align:center;">'.htmlspecialchars($grupo).'</td>
            <td style="text-align:center;">'.htmlspecialchars($subgrupo).'</td>
            <td style="text-align:center;">0</td>
            <td style="text-align:center;">'.htmlspecialchars($row['codigo_unico']).'</td>
            <td style="text-align:center;">'.htmlspecialchars($row['cantidad'] ?? 1).'</td>
            <td>'.htmlspecialchars($row['descripcion']).'</td>
            <td style="text-align:right;">'.number_format($row['precio_adquisicion'], 2, ',', '').'</td>
            <td style="text-align:right;">'.number_format($row['precio_adquisicion'], 2, ',', '').'</td>
        </tr>';
    }
    $html .= '
        <tr>
            <td colspan="6" style="text-align:right;">Sub-Total</td>
            <td colspan="2" style="text-align:right;">'.number_format($total, 2, ',', '').'</td>
        </tr>
    </table>
    <br>
    <table style="width:100%;border-collapse:collapse;font-size:13px;margin-top:30px;">
        <tr>
            <td style="width:50%;text-align:center;">
                <br><br>_____________________________<br>
                OFICINA DE BIENES Y SERVICIOS
            </td>
            <td style="width:50%;text-align:center;">
                <br><br>_____________________________<br>
                JEFE DE LA UNIDAD DE TRABAJO O DEPENDENCIA
            </td>
        </tr>
    </table>
    ';
    return $html;
}

// --- BM-2 ---
function generarBM2($con, $unidad_trabajo, $fecha = '', $modo_pdf = false, $año = '', $subcat = '') {
    $fecha_informe = date('Y-m-d');
    $html = generarEncabezado('BM-2');
    $html .= '
        <tr>
            <td colspan="11" style="text-align:center;font-weight:bold;font-size:16px;">RELACIÓN DEL MOVIMIENTO DE BIENES MUEBLES (MB-02)</td>
        </tr>
        <tr>
            <td colspan="11">Entidad Propietaria: <b>GOBERNACIÓN DEL ESTADO BARINAS</b></td>
        </tr>
        <tr>
            <td colspan="8">Sector Presupuestario: <b>DIRECCION Y COORDINACION EJECUTIVA</b></td>
            <td colspan="3">Servicio:<b>'.htmlspecialchars($unidad_trabajo).'</b></td>
        </tr>
        <tr>
            <td colspan="8">Unidad de trabajo: <b>INSTITUTO AUTONOMO DE CULTURA DEL ESTADO BARINAS</b></td>
            <td colspan="3">Código: <b>01.07.01.01</b></td>
        </tr>
        <tr>
            <td colspan="3">Estado: <b>BARINAS</b></td>
            <td colspan="3">Municipio: <b>BARINAS</b></td>
            <td colspan="3">Parroquia: <b>BARINAS</b></td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td colspan="11">Fecha: <b>'.htmlspecialchars($fecha_informe).'</b></td>
        </tr>
    </table>
    <table border="1" cellpadding="4" style="width:100%;border-collapse:collapse;font-size:13px;">
        <tr>
            <th style="text-align:center;">Grup.</th>
            <th style="text-align:center;">Sub-Gru.</th>
            <th style="text-align:center;">Secc.</th>
            <th style="text-align:center;">Nº de Ident.</th>
            <th style="text-align:center;">Cant.</th>
            <th style="text-align:center;">Concepto del Movimiento</th>
            <th style="text-align:center;">Nombre y Descripción de los Elementos</th>
            <th style="text-align:center;">Incorporaciones<br>(Bs.)</th>
            <th style="text-align:center;">Desincorporaciones<br>(Bs.)</th>
        </tr>
    ';
    // Agregar 'estado' al SELECT
    if ($año) {
        $sql = "SELECT subcategoria, codigo_unico, descripcion, cantidad, precio_adquisicion, estado 
                FROM bienes_publicos 
                WHERE estado IN ('Incorporado', 'Desincorporado') 
                AND ubicacion = ? 
                AND YEAR(fecha_adquisicion) = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "si", $unidad_trabajo, $año);
    } else {
        $sql = "SELECT subcategoria, codigo_unico, descripcion, cantidad, precio_adquisicion, estado 
                FROM bienes_publicos 
                WHERE estado IN ('Incorporado', 'Desincorporado') 
                AND ubicacion = ? 
                AND DATE_FORMAT(fecha_adquisicion, '%Y-%m') = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $unidad_trabajo, $fecha);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $total_incorp = 0;
    $total_desinc = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $concepto = (isset($row['estado']) && $row['estado'] === 'Incorporado') ? 'Incorporación' : 'Desincorporación';
        $valor = $row['precio_adquisicion'] * (isset($row['cantidad']) ? $row['cantidad'] : 1);
        $grupo = $subgrupo = '';
        $seccion = '0';
        if (preg_match('/^(\d+)-(\d+)(?:-(\d+))?:/', $row['subcategoria'], $matches)) {
            $grupo = $matches[1];
            $subgrupo = $matches[2];
            // Secc. siempre 0
        }
        $incorp_val = $concepto === 'Incorporación' ? number_format($valor, 2, ',', '.') . ' Bs.' : '';
        $desinc_val = $concepto === 'Desincorporación' ? number_format($valor, 2, ',', '.') . ' Bs.' : '';
        $html .= '
        <tr>
            <td style="text-align:center;">'.htmlspecialchars($grupo).'</td>
            <td style="text-align:center;">'.htmlspecialchars($subgrupo).'</td>
            <td style="text-align:center;">0</td>
            <td style="text-align:center;">'.htmlspecialchars($row['codigo_unico']).'</td>
            <td style="text-align:center;">'.htmlspecialchars($row['cantidad'] ?? 1).'</td>
            <td style="text-align:center;">'.$concepto.'</td>
            <td>'.htmlspecialchars($row['descripcion']).'</td>
            <td style="text-align:right;">'.$incorp_val.'</td>
            <td style="text-align:right;">'.$desinc_val.'</td>
        </tr>';
        if ($concepto === 'Incorporación') $total_incorp += $valor;
        if ($concepto === 'Desincorporación') $total_desinc += $valor;
    }
    $html .= '
        <tr>
            <td colspan="7" style="text-align:right;">Sub-Total</td>
            <td style="text-align:right;">'.number_format($total_incorp, 2, ',', '.').'</td>
            <td style="text-align:right;">'.number_format($total_desinc, 2, ',', '.').'</td>
        </tr>
    </table>
    <br>
    <table style="width:100%;border-collapse:collapse;font-size:13px;margin-top:30px;">
        <tr>
            <td style="width:50%;text-align:center;">
                <br><br>_____________________________<br>
                OFICINA DE BIENES Y SERVICIOS
            </td>
            <td style="width:50%;text-align:center;">
                <br><br>_____________________________<br>
                JEFE DE LA UNIDAD DE TRABAJO O DEPENDENCIA
            </td>
        </tr>
    </table>
    ';
    return $html;
}

function generarBM3($con, $unidad_trabajo, $fecha = '', $modo_pdf = false, $año = '') {
    $fecha_informe = date('Y-m-d');
    $html = '
    <table style="width:100%;border-collapse:collapse;margin-bottom:0;">
        <tr>
            <td style="width:15%;text-align:left;">
                <img src="logo_iaceb.png" alt="Logo IACEB" style="height:50px;">
            </td>
            <td style="width:70%;text-align:center;font-weight:bold;font-size:16px;">
                INSTITUTO AUTÓNOMO DE CULTURA DEL ESTADO BARINAS<br>
                CONTROL DE BIENES
            </td>
            <td style="width:15%;text-align:right;">
                <img src="logo_barinas.png" alt="Logo Barinas" style="height:50px;">
            </td>
        </tr>
        <tr>
            <td colspan="3" style="text-align:right;font-size:13px;">MODELO: BM-3</td>
        </tr>
    </table>
    <table border="1" cellpadding="4" style="width:100%;border-collapse:collapse;font-size:13px;">
        <tr>
            <td colspan="9" style="font-weight:bold;text-align:center;">RELACIÓN DE BIENES MUEBLES FALTANTES (MB-03)</td>
        </tr>
        <tr>
            <td colspan="9">Entidad Propietaria: <b>GOBERNACIÓN DEL ESTADO BARINAS</b></td>
        </tr>
        <tr>
            <td colspan="6">Sector Presupuestario: <b>DIRECCION Y COORDINACION EJECUTIVA</b></td>
            <td colspan="3">Servicio:<b>'.htmlspecialchars($unidad_trabajo).'</b></td>
        </tr>
        <tr>
            <td colspan="6">Unidad de trabajo: <b>INSTITUTO AUTONOMO DE CULTURA DEL ESTADO BARINAS</b></td>
            <td colspan="3">Código: <b>01.07.01.01</b></td>
        </tr>
        <tr>
            <td colspan="3">Estado: <b>Barinas</b></td>
            <td colspan="3">Municipio: <b>Barinas</b></td>
            <td colspan="3">Parroquia: <b>Barinas</b></td>
        </tr>
        <tr>
            <td colspan="9">Fecha: <b>'.htmlspecialchars($fecha_informe).'</b></td>
        </tr>
        <tr>
            <td colspan="9">NUMERO DE COMPROBANTE</td>
        </tr>
        <tr>
            <td colspan="9" style="font-weight:bold;text-align:center;">RELACIÓN DE BIENES FALTANTES</td>
        </tr>
    </table>
    <table border="1" cellpadding="4" style="width:100%;border-collapse:collapse;font-size:13px;">
        <tr>
            <th style="text-align:center;">Gru</th>
            <th style="text-align:center;">Su-G</th>
            <th style="text-align:center;">Sec.</th>
            <th style="text-align:center;">Nº de Ident.</th>
            <th style="text-align:center;">NOMBRE Y DESCRIPCIÓN</th>
            <th style="text-align:center;">CANTIDAD</th>
            <th style="text-align:center;">VALOR UNITARIO</th>
            <th style="text-align:center;">DIFERENCIA</th>
            <th style="text-align:center;">VALOR TOTAL</th>
        </tr>
    ';
    // Cambia mostrarFormato para pasar el año si está presente
    if ($año) {
        $sql = "SELECT subcategoria, codigo_unico, descripcion, precio_adquisicion, estado_conservacion 
                FROM bienes_publicos 
                WHERE estado = 'En investigación' 
                AND ubicacion = ? 
                AND YEAR(fecha_adquisicion) = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "si", $unidad_trabajo, $año);
    } else {
        $sql = "SELECT subcategoria, codigo_unico, descripcion, precio_adquisicion, estado_conservacion 
                FROM bienes_publicos 
                WHERE estado = 'En investigación' 
                AND ubicacion = ? 
                AND DATE_FORMAT(fecha_adquisicion, '%Y-%m') = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $unidad_trabajo, $fecha);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $grupo = $subgrupo = '';
        $seccion = '0';
        if (preg_match('/^(\d+)-(\d+)(?:-(\d+))?:/', $row['subcategoria'], $matches)) {
            $grupo = $matches[1];
            $subgrupo = $matches[2];
            // Secc. siempre 0
        }
        $html .= '
        <tr>
            <td style="text-align:center;">'.htmlspecialchars($grupo).'</td>
            <td style="text-align:center;">'.htmlspecialchars($subgrupo).'</td>
            <td style="text-align:center;">0</td>
            <td style="text-align:center;">'.htmlspecialchars($row['codigo_unico']).'</td>
            <td>'.htmlspecialchars($row['descripcion']).'</td>
            <td style="text-align:center;">1</td>
            <td style="text-align:right;">'.number_format($row['precio_adquisicion'], 2, ',', '').'</td>
            <td style="text-align:right;">'.number_format($row['precio_adquisicion'], 2, ',', '').'</td>
            <td style="text-align:right;">'.number_format($row['precio_adquisicion'], 2, ',', '').'</td>
        </tr>';
    }
    $html .= '
        <tr>
            <td colspan="2" style="font-weight:bold;">OBSERVACIONES:</td>
            <td colspan="7">FALTANTES DETERMINADOS POR:</td>
        </tr>
    </table>
    <br>
    <table style="width:100%;border-collapse:collapse;font-size:13px;margin-top:30px;">
        <tr>
            <td style="width:50%;text-align:center;">
                <br><br>_____________________________<br>
                OFICINA DE BIENES Y SERVICIOS
            </td>
            <td style="width:50%;text-align:center;">
                <br><br>_____________________________<br>
                RESPONSABLE DE LA UNIDAD DE TRABAJO
            </td>
        </tr>
    </table>
    ';
    return $html;
}

function generarBM4($con, $unidad_trabajo, $fecha, $modo_pdf = false, $año = '') {
    $fecha_informe = date('Y-m-d');
    $html = '
    <table style="width:100%;border-collapse:collapse;margin-bottom:0;">
        <tr>
            <td style="width:15%;text-align:left;">
                <img src="logo_iaceb.png" alt="Logo IACEB" style="height:50px;">
            </td>
            <td style="width:70%;text-align:center;font-weight:bold;font-size:16px;">
                INSTITUTO AUTÓNOMO DE CULTURA DEL ESTADO BARINAS<br>
                CONTROL DE BIENES
            </td>
            <td style="width:15%;text-align:right;">
                <img src="logo_barinas.png" alt="Logo Barinas" style="height:50px;">
            </td>
        </tr>
        <tr>
            <td colspan="3" style="text-align:right;font-size:13px;">MODELO: BM-4</td>
        </tr>
    </table>
    <table border="1" cellpadding="4" style="width:100%;border-collapse:collapse;font-size:13px;">
        <tr>
            <td colspan="6" style="font-weight:bold;text-align:center;">RESUMEN DE LA CUENTA DE BIENES MUEBLES (MB-04)</td>
        </tr>
        <tr>
            <td colspan="6">Entidad Propietaria: <b>GOBERNACIÓN DEL ESTADO BARINAS</b></td>
        </tr>
        <tr>
            <td colspan="4">Sector Presupuestario: <b>DIRECCION Y COORDINACION EJECUTIVA</b></td>
            <td colspan="2">Servicio:<b>'.htmlspecialchars($unidad_trabajo).'</b></td>
        </tr>
        <tr>
            <td colspan="4">Unidad de trabajo: <b>INSTITUTO AUTONOMO DE CULTURA DEL ESTADO BARINAS</b></td>
            <td colspan="2">Código: <b>01.07.01.01</b></td>
        </tr>
        <tr>
            <td>Estado: <b>BARINAS</b></td>
            <td>Municipio: <b>BARINAS</b></td>
            <td>Parroquia: <b>BARINAS</b></td>
            <td colspan="3"></td>
        </tr>
        <tr>
            <td colspan="6">Fecha: <b>'.htmlspecialchars($fecha_informe).'</b></td>
        </tr>
    </table>
    <table border="1" cellpadding="4" style="width:100%;border-collapse:collapse;font-size:13px;">
        <tr>
            <th style="text-align:center;">CONCEPTO</th>
            <th style="text-align:center;">MAS (Bs.)</th>
            <th style="text-align:center;">MENOS (Bs.)</th>
        </tr>
    ';
    // ...calculos...
    // Filtros de fecha
    $where_fecha = '';
    if ($año) {
        $where_fecha = "AND YEAR(fecha_adquisicion) = '".intval($año)."'";
    } elseif ($fecha) {
        $where_fecha = "AND DATE_FORMAT(fecha_adquisicion, '%Y-%m') = '".mysqli_real_escape_string($con, $fecha)."'";
    }
    // Existencia anterior
    $existencia_anterior = 0;
    if ($fecha || $año) {
        $fecha_limite = $año ? $año.'-01-01' : ($fecha.'-01');
        $sql_prev = "SELECT SUM(precio_adquisicion * cantidad) AS total FROM bienes_publicos WHERE ubicacion = ? AND fecha_adquisicion < ?";
        $stmt_prev = mysqli_prepare($con, $sql_prev);
        mysqli_stmt_bind_param($stmt_prev, "ss", $unidad_trabajo, $fecha_limite);
        mysqli_stmt_execute($stmt_prev);
        $res_prev = mysqli_stmt_get_result($stmt_prev);
        $existencia_anterior = floatval(mysqli_fetch_assoc($res_prev)['total']);
    }
    // Incorporaciones
    $sql_incorp = "SELECT SUM(precio_adquisicion * cantidad) AS total FROM bienes_publicos WHERE estado = 'Incorporado' AND ubicacion = ? $where_fecha";
    $stmt_incorp = mysqli_prepare($con, $sql_incorp);
    mysqli_stmt_bind_param($stmt_incorp, "s", $unidad_trabajo);
    mysqli_stmt_execute($stmt_incorp);
    $res_incorp = mysqli_stmt_get_result($stmt_incorp);
    $incorporados = floatval(mysqli_fetch_assoc($res_incorp)['total']);
    // Desincorporaciones
    $sql_desinc = "SELECT SUM(precio_adquisicion * cantidad) AS total FROM bienes_publicos WHERE estado = 'Desincorporado' AND ubicacion = ? $where_fecha";
    $stmt_desinc = mysqli_prepare($con, $sql_desinc);
    mysqli_stmt_bind_param($stmt_desinc, "s", $unidad_trabajo);
    mysqli_stmt_execute($stmt_desinc);
    $res_desinc = mysqli_stmt_get_result($stmt_desinc);
    $desincorporados = floatval(mysqli_fetch_assoc($res_desinc)['total']);
    // Faltantes por investigar
    $sql_falt = "SELECT SUM(precio_adquisicion * cantidad) AS total FROM bienes_publicos WHERE estado = 'En investigación' AND ubicacion = ? $where_fecha";
    $stmt_falt = mysqli_prepare($con, $sql_falt);
    mysqli_stmt_bind_param($stmt_falt, "s", $unidad_trabajo);
    mysqli_stmt_execute($stmt_falt);
    $res_falt = mysqli_stmt_get_result($stmt_falt);
    $faltantes = floatval(mysqli_fetch_assoc($res_falt)['total']);
    // Existencia final
    $existencia_final = $existencia_anterior + $incorporados - $desincorporados - $faltantes;

    $html .= '
        <tr>
            <td>EXISTENCIA ANTERIOR</td>
            <td style="text-align:right;">'.number_format($existencia_anterior,2,'.','').' Bs.</td>
            <td></td>
        </tr>
        <tr>
            <td>INCORPORACION EN EL MES DE LA CUENTA</td>
            <td style="text-align:right;">'.number_format($incorporados,2,'.','').' Bs.</td>
            <td></td>
        </tr>
        <tr>
            <td>DESINCORPORACIONES EN EL MES DE LA CUENTA POR TODOS LOS CONCEPTOS, CON EXCEPCION DEL 60. "FALTANTES DE BIENES POR INVESTIGAR"</td>
            <td></td>
            <td style="text-align:right;">'.number_format($desincorporados,2,'.','').' Bs.</td>
        </tr>
        <tr>
            <td>DESINCORPORACIONES EN EL MES DE LA CUENTA POR EL CONCEPTO 60 "FALTANTES POR INVESTIGAR"</td>
            <td></td>
            <td style="text-align:right;">'.number_format($faltantes,2,'.','').' Bs.</td>
        </tr>
        <tr>
            <td>EXISTENCIA FINAL</td>
            <td style="text-align:right;">'.number_format($existencia_final,2,'.','').' Bs.</td>
            <td></td>
        </tr>
        <tr>
            <td>TOTAL BS.</td>
            <td></td>
            <td></td>
        </tr>
    </table>
    <br>
    <table style="width:100%;border-collapse:collapse;font-size:13px;margin-top:30px;">
        <tr>
            <td style="width:50%;text-align:center;">
                <br><br>_____________________________<br>
                OFICINA DE BIENES Y SERVICIOS
            </td>
            <td style="width:50%;text-align:center;">
                <br><br>_____________________________<br>
                JEFE DE LA UNIDAD DE TRABAJO O DEPENDENCIA
            </td>
        </tr>
    </table>
    ';
    return $html;
}

function mostrarFormato($tipo_informe, $con, $unidad_trabajo, $fecha = '', $modo_pdf = false, $año = '', $subcat = '') {
    switch ($tipo_informe) {
        case 'BM-1':
            return generarBM1($con, $unidad_trabajo, $fecha, $modo_pdf, $año, $subcat);
        case 'BM-2':
            return generarBM2($con, $unidad_trabajo, $fecha, $modo_pdf, $año, $subcat);
        case 'BM-3':
            return generarBM3($con, $unidad_trabajo, $fecha, $modo_pdf, $año, $subcat);
        case 'BM-4':
            return generarBM4($con, $unidad_trabajo, $fecha, $modo_pdf, $año, $subcat);
        default:
            return '<p>Seleccione un tipo de informe válido.</p>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Formatos BM</title>
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
        .filtros { margin: 20px 0; padding: 15px; background: #f5f5f5; border-radius: 5px; }
        .form-table { width: 100%; border-collapse: collapse; }
        .form-table td { padding: 10px; border: 1px solid #ddd; }
        .results-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .results-table th, .results-table td { padding: 10px; border: 1px solid #ddd; text-align: left; }
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
            font-size: 15px;
            transition: background 0.2s;
        }
        button:hover {
            background-color: #0056b3;
        }
        select, input[type="month"] {
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            margin-bottom: 10px;
        }
        a.exportar-pdf {
            background: #28a745;
            color: #fff !important;
            padding: 8px 18px;
            border-radius: 4px;
            text-decoration: none;
            margin-left: 10px;
            font-size: 15px;
            transition: background 0.2s;
        }
        a.exportar-pdf:hover {
            background: #218838;
        }
        table { margin: 20px 0; page-break-inside: avoid; }
        td { min-width: 50px; }
    </style>
</head>
<body>
<div class="container">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <img src="logo_iaceb.png" alt="Logo IACEB" style="height:60px;">
        <img src="logo_barinas.png" alt="Logo Barinas" style="height:60px;">
    </div>
    <div class="menu-interno">
        <a href="informes.php" class="activo">Formatos BM</a>
    </div>
    <div class="seccion-contenido">
        <h2 style="margin-top:0;">Formatos BM</h2>
        <div class="filtros">
            <form method="GET">
                <label for="tipo">Seleccionar informe:</label>
                <select name="tipo" id="tipo" required>
                    <option value="">Seleccione un informe</option>
                    <option value="BM-1" <?= $tipo_sel == 'BM-1' ? 'selected' : '' ?>>BM-1: Inventario de Bienes Muebles</option>
                    <option value="BM-2" <?= $tipo_sel == 'BM-2' ? 'selected' : '' ?>>BM-2: Movimiento de Bienes</option>
                    <option value="BM-3" <?= $tipo_sel == 'BM-3' ? 'selected' : '' ?>>BM-3: Relación de Bienes Faltantes</option>
                    <option value="BM-4" <?= $tipo_sel == 'BM-4' ? 'selected' : '' ?>>BM-4: Resumen Mensual</option>
                </select>
                <br>
                <label for="unidad_trabajo">Unidad de Trabajo:</label>
                <select name="unidad_trabajo" id="unidad_trabajo" required>
                    <option value="">Seleccione una unidad</option>
                    <?php foreach($unidades as $u): ?>
                        <option value="<?= htmlspecialchars($u) ?>" <?= $unidad_sel == $u ? 'selected' : '' ?>><?= htmlspecialchars($u) ?></option>
                    <?php endforeach; ?>
                </select>
                <br>
                <label for="subcategoria">Subcategoría:</label>
                <select name="subcategoria" id="subcategoria">
                    <option value="">Todas</option>
                    <?php foreach($subcategorias_disponibles as $subcat): ?>
                        <option value="<?= htmlspecialchars($subcat) ?>" <?= $subcat_sel == $subcat ? 'selected' : '' ?>><?= htmlspecialchars($subcat) ?></option>
                    <?php endforeach; ?>
                </select>
                <br>
                <label for="fecha">Mes:</label>
                <select name="fecha" id="fecha">
                    <option value="all" <?= (!isset($_GET['fecha']) || $_GET['fecha'] === 'all' || $_GET['fecha'] === '') ? 'selected' : '' ?>>Todos los meses</option>
                    <?php foreach($fechas_disponibles as $fecha): ?>
                        <option value="<?= $fecha ?>" <?= (isset($_GET['fecha']) && $_GET['fecha'] == $fecha) ? 'selected' : '' ?>>
                            <?= date('F Y', strtotime($fecha . '-01')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <label for="año">Año:</label>
                <select name="año" id="año">
                    <option value="all" <?= (!isset($_GET['año']) || $_GET['año'] === 'all' || $_GET['año'] === '') ? 'selected' : '' ?>>Todos los años</option>
                    <?php foreach($años_disponibles as $año): ?>
                        <option value="<?= $año ?>" <?= (isset($_GET['año']) && $_GET['año'] == $año) ? 'selected' : '' ?>>
                            <?= $año ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <br>
                <button type="submit" name="generar" value="1">Generar Informe</button>
                <?php
                $pdf_url = "informes.php?tipo=" . urlencode($tipo_sel) . "&unidad_trabajo=" . urlencode($unidad_sel);
                if (isset($_GET['fecha'])) $pdf_url .= "&fecha=" . urlencode($_GET['fecha']);
                if (isset($_GET['año'])) $pdf_url .= "&año=" . urlencode($_GET['año']);
                if (!empty($_GET['subcategoria'])) $pdf_url .= "&subcategoria=" . urlencode($_GET['subcategoria']);
                $pdf_url .= "&exportar=pdf";
                if ($tipo_sel && $unidad_sel && isset($_GET['generar'])):
                ?>
                    <a href="<?= $pdf_url ?>" target="_blank" class="exportar-pdf">Exportar PDF</a>
                <?php endif; ?>
            </form>
        </div>
        <div>
            <?php
            $fecha_param = (isset($_GET['fecha']) && $_GET['fecha'] !== 'all') ? $_GET['fecha'] : '';
            $año_param = (isset($_GET['año']) && $_GET['año'] !== 'all') ? $_GET['año'] : '';
            $subcat_param = isset($_GET['subcategoria']) ? $_GET['subcategoria'] : '';
            // Mostrar informe solo si se presionó "Generar Informe"
            if (isset($_GET['generar']) && $tipo_sel && $unidad_sel) {
                echo mostrarFormato($tipo_sel, $con, $unidad_sel, $fecha_param, false, $año_param, $subcat_param);
            } else {
                echo '<p>Seleccione todos los parámetros para generar el informe.</p>';
            }
            ?>
        </div>
        <a href="Inicio.php" class="back-button">← Regresar al Menú</a>
    </div>
</div>
</body>
</html>
