<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ayuda / Manual de Usuario</title>
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
            max-width: 800px;
            width: 96%;
            margin: 40px auto 30px auto;
            background: rgba(255,255,255,0.97);
            border-radius: 8px;
            box-shadow: 0 8px 32px rgba(44,62,80,0.18), 0 2px 10px #ccc;
            padding: 30px;
        }
        h1, h2, h3 {
            color: #2c3e50;
        }
        ul, ol {
            margin-bottom: 20px;
        }
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
        .section {
            margin-bottom: 30px;
        }
        code {
            background: #eee;
            padding: 2px 6px;
            border-radius: 4px;
            color: #333;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            overflow-x: hidden;
        }
        textarea {
            min-height: 38px;
            resize: none;
            overflow: hidden;
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
    </script>
</head>
<body>
<div class="container">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <img src="logo_iaceb.png" alt="Logo IACEB" style="height:60px;">
        <img src="logo_barinas.png" alt="Logo Barinas" style="height:60px;">
    </div>
    <h1>Manual de Usuario - Sistema de Gestión de Bienes Públicos</h1>
    <div class="section">
        <h2>¿Qué es este sistema?</h2>
        <p>
            Es una aplicación para registrar, consultar, transferir, desincorporar y reportar bienes públicos, así como gestionar usuarios y generar informes.
        </p>
    </div>
    <div class="section">
        <h2>Ingreso al sistema</h2>
        <ol>
            <li>Abra el programa y verá la pantalla de <strong>Inicio de Sesión</strong>.</li>
            <li>Ingrese su <strong>usuario</strong> y <strong>contraseña</strong> asignados.</li>
            <li>Presione <strong>Ingresar</strong>.</li>
        </ol>
    </div>
    <div class="section">
        <h2>Menú Principal</h2>
        <p>Desde el menú puede acceder a las siguientes opciones:</p>
        <ul>
            <li><strong>1. Incorporaciones de Bienes:</strong> Registrar nuevos bienes en el sistema.</li>
            <li><strong>2. Desincorporaciones de Bienes:</strong> Marcar bienes como desincorporados o reincorporarlos.</li>
            <li><strong>3. Transferencias de Bienes:</strong> Transferir bienes entre unidades/responsables.</li>
            <li><strong>4. Reportar Faltante:</strong> Reportar bienes que no se encuentran o están en investigación.</li>
            <li><strong>5. Informes:</strong> Generar y exportar reportes de bienes.</li>
            <li><strong>6. Ayuda / Manual de Usuario:</strong> Ver esta guía.</li>
            <li><strong>7. Gestión de Usuarios:</strong> (Solo administradores) Crear, editar o eliminar usuarios y ver historial de actividades.</li>
        </ul>
    </div>
    <div class="section">
        <h2>Acciones principales</h2>
        <ul>
            <li>
                <strong>Registrar un bien:</strong>
                <ol>
                    <li>Ir a <strong>Incorporaciones de Bienes</strong> y seleccionar "Incorporar Nuevo Bien".</li>
                    <li>Llene el formulario con los datos requeridos.</li>
                    <li>Adjunte un documento soporte si es necesario.</li>
                    <li>Presione <strong>Registrar Bien</strong>.</li>
                </ol>
            </li>
            <li>
                <strong>Consultar bienes:</strong>
                <ol>
                    <li>Ir a <strong>Incorporaciones de Bienes</strong> y usar la opción de búsqueda.</li>
                    <li>Puede ver detalles, editar o eliminar bienes desde la lista.</li>
                    <li><em>Si no ingresa ningún término de búsqueda, se mostrarán todos los bienes incorporados.</em></li>
                </ol>
            </li>
            <li>
                <strong>Transferir un bien:</strong>
                <ol>
                    <li>Ir a <strong>Transferencias de Bienes</strong>.</li>
                    <li>Seleccione el bien, la unidad destino y el responsable destino.</li>
                    <li>Presione <strong>Transferir</strong> y descargue el acta de transferencia.</li>
                </ol>
            </li>
            <li>
                <strong>Desincorporar/Reincorporar:</strong>
                <ol>
                    <li>Ir a <strong>Desincorporaciones de Bienes</strong>.</li>
                    <li>Busque el bien y use el botón correspondiente.</li>
                    <li><em>Si no ingresa ningún término de búsqueda, se mostrarán todos los bienes según el estado seleccionado.</em></li>
                </ol>
            </li>
            <li>
                <strong>Reportar faltante:</strong>
                <ol>
                    <li>Ir a <strong>Reportar Faltante</strong>.</li>
                    <li>Llene el formulario y presione <strong>Reportar</strong>.</li>
                </ol>
            </li>
            <li>
                <strong>Generar informes:</strong>
                <ol>
                    <li>Ir a <strong>Informes</strong>.</li>
                    <li>Seleccione el tipo de informe, unidad y fecha.</li>
                    <li>Presione <strong>Generar Informe</strong> o <strong>Exportar PDF</strong>.</li>
                </ol>
            </li>
        </ul>
    </div>
    <div class="section">
        <h2>Gestión de Usuarios (solo administrador)</h2>
        <ul>
            <li>Crear nuevos usuarios, editar o eliminar usuarios existentes.</li>
            <li>Ver el historial de actividades de todos los usuarios.</li>
            <li>Solo el administrador principal puede crear otros usuarios o modificar su propia clave.</li>
        </ul>
    </div>
    <div class="section">
        <h2>Consejos y Seguridad</h2>
        <ul>
            <li>No comparta su usuario ni contraseña.</li>
            <li>Cierre sesión usando el botón <strong>Salir / Cerrar Sesión</strong> al terminar.</li>
            <li>Consulte el historial de actividades para auditar acciones importantes.</li>
        </ul>
    </div>
    <a href="Inicio.php" class="back-button">← Volver al Menú</a>
</div>
</body>
</html>
