<?php
$connection = pg_connect("host=localhost dbname=Tee_josh user=postgres password=1234");
if(!$connection) {
    echo "Error de conexion.<br>";
    exit;
}