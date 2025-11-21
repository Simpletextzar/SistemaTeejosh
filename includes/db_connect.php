<?php
$connection = pg_connect("host=db.piearfkkossvytunnrfk.supabase.co dbname=db_teejosh user=postgres password=patoloco090");
if(!$connection) {
    echo "Error de conexion.<br>";
    exit;
}