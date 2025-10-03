<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario</title>
</head>
<body>
    <h2>Productos del inventario</h2>
    <?php
    $connection = pg_connect("host=aws-1-sa-east-1.pooler.supabase.com dbname=db_teejosh user=postgres.piearfkkossvytunnrfk password=patoloco090");
    if(!$connection) {
        echo "Error de conexion.<br>";
        exit;
    }

    
    ?>
</body>
</html>