<?php
session_start();

$valid_user = 'teejosh';
$valid_pass = 'esis3412';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username']);
    $pass = trim($_POST['password']);

    if ($user === $valid_user && $pass === $valid_pass) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $user;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Usuario o contraseña incorrectos.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #1e1e1e;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        form {
            background: #2b2b2b;
            padding: 30px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.4);
        }
        input {
            width: 90%;
            margin: 10px 0;
            padding: 8px;
            border: none;
            border-radius: 5px;
        }
        button {
            background: #0078D7;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
        }
        button:hover { background: #005EA6; }
        .error { color: #ff6b6b; margin-top: 10px; }
    </style>
</head>
<body>
    <form method="post">
        <h2>Iniciar sesión</h2>
        <input type="text" name="username" placeholder="Usuario" required><br>
        <input type="password" name="password" placeholder="Contraseña" required><br>
        <button type="submit">Entrar</button>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </form>
</body>
</html>
