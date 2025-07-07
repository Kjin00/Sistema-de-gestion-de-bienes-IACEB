<?php
// Archivo de conexión a la base de datos MySQL
// Define la función conectar() que establece y retorna la conexión
function conectar(){
    // Parámetros de conexión
    $host="localhost";
    $user="root";
    $pass="";

    $bd="Registro";

    // Conexión al servidor MySQL
    $con=mysqli_connect($host,$user,$pass);

    // Selección de la base de datos
    mysqli_select_db($con,$bd);

    // Retorna el objeto de conexión
    return $con;
}

