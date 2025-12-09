<?php
/**
 * ============================================================================
 * FORMULARIO DE AUTENTICACIÓN - PANTALLA DE LOGIN
 * ============================================================================
 * 
 * ARCHIVO: login.php
 * UBICACIÓN: /app/views/auth/login.php
 * 
 * DESCRIPCIÓN:
 * Vista de autenticación del sistema Teejosh con diseño dark mode completo.
 * Proporciona un formulario seguro de login con validación de credenciales
 * y manejo de errores. Es la puerta de entrada al sistema.
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Formulario de login con campos usuario y contraseña
 * - Validación HTML5 required para campos obligatorios
 * - Manejo y display de mensajes de error
 * - Diseño responsive centrado vertical y horizontalmente
 * - Autofocus en campo de usuario para mejor UX
 * 
 * DATOS IMPORTANTES:
 * - 🟢 COMPLEJIDAD: Baja
 * - 🔐 SEGURIDAD: Protección contra ataques XSS (htmlspecialchars)
 * - 🎯 RESPONSABILIDAD: Control de acceso al sistema
 * - 🎨 DISEÑO: Dark mode completo con estilos inline
 * 
 * RELACIONES:
 * - USADO POR: AuthController::login()
 * - ENVÍA DATOS A: AuthController::authenticate() via POST
 * - INTERACTÚA CON: Variable $error para mensajes de feedback
 * - UTILIZA: Constante BASE_URL para action del formulario
 * 
 * EJEMPLOS DE USO:
 * ```php
 * // En AuthController:
 * public function login() {
 *     $data = [];
 *     if (isset($_SESSION['login_error'])) {
 *         $data['error'] = $_SESSION['login_error'];
 *         unset($_SESSION['login_error']);
 *     }
 *     $this->view('auth/login', $data);
 * }
 * ```
 * 
 * FLUJO DE AUTENTICACIÓN:
 * 1. Usuario accede al sistema (URL raíz sin autenticar)
 * 2. Router redirige a AuthController::login()
 * 3. Se muestra este formulario
 * 4. Usuario ingresa credenciales y envía formulario
 * 5. AuthController::authenticate() valida credenciales
 * 6. Si éxito: redirige a home/index, Si error: vuelve con mensaje
 * 
 * NOTAS CSS/HTML:
 * - DISEÑO: Dark theme (#1e1e1e fondo, #2b2b2b formulario)
 * - ESTRUCTURA: Formulario centrado con sombra y bordes redondeados
 * - COLORES: 
 *   - Primario: #0078D7 (azul Windows)
 *   - Hover: #005EA6 (azul más oscuro)
 *   - Error: #ff6b6b (rojo claro)
 * - TIPOGRAFÍA: Arial, sans-serif
 * - RESPONSIVE: Centrado vertical/horizontal, ancho fijo 300px
 * - ACCESIBILIDAD: Autofocus en username, labels implícitos
 */
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Inventario</title>
    <style>
        /* ESTILOS DARK MODE PARA FORMULARIO DE LOGIN */
        body {
            font-family: Arial, sans-serif;
            background: #1e1e1e; /* Fondo oscuro principal */
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh; /* Altura completa viewport */
            margin: 0;
        }
        
        /* Contenedor del formulario con efecto de elevación */
        form {
            background: #2b2b2b; /* Fondo oscuro secundario */
            padding: 30px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.4); /* Sombra suave */
        }
        
        /* Campos de entrada de texto */
        input {
            width: 90%; /* Ancho casi completo */
            margin: 10px 0;
            padding: 8px;
            border: none;
            border-radius: 5px;
        }
        
        /* Botón de envío principal */
        button {
            background: #0078D7; /* Azul corporativo */
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
            width: 95%; /* Ancho consistente con inputs */
        }
        
        /* Efecto hover para botón */
        button:hover { 
            background: #005EA6; /* Azul más oscuro al hover */
        }
        
        /* Mensajes de error */
        .error { 
            color: #ff6b6b; /* Rojo claro para errores */
            margin-top: 10px;
            font-size: 14px;
        }
        
        /* Encabezados del formulario */
        h2 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- FORMULARIO DE AUTENTICACIÓN -->
    <form method="post" action="<?= BASE_URL ?>auth/login">
        <h2>Inventario Coleccionables</h2>
        <h3>Iniciar sesión</h3>
        
        <!-- Campo de nombre de usuario -->
        <input type="text" name="username" placeholder="Usuario" required autofocus>
        
        <!-- Campo de contraseña -->
        <input type="password" name="password" placeholder="Contraseña" required>
        
        <!-- Botón de envío -->
        <button type="submit">Entrar</button>
        
        <!-- Mostrar mensaje de error si existe -->
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </form>
</body>
</html>