<?php
$connection = pg_connect("host=aws-1-sa-east-1.pooler.supabase.com dbname=db_teejosh user=postgres.piearfkkossvytunnrfk password=patoloco090");
if(!$connection) {
    echo "Error de conexion.<br>";
    exit;
}