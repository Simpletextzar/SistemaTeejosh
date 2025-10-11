<?php
$connection = pg_connect("host=db.piearfkkossvytunnrfk.supabase.co dbname=postgres user=postgres password=patoloco090");
if(!$connection) {
    echo "Error de conexion.<br>";
    exit;
}