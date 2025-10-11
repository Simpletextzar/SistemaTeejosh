<nav style="
    background-color: #2ab222ff;
    color: white;
    padding: 10px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-family: Arial, sans-serif;
">
    <!-- IZQUIERDA -->
    <div style="font-weight: bold; font-size: 18px;">
        🃏 Inventario Coleccionables
    </div>

    <!-- DERECHA -->
    <div style="display: flex; gap: 10px; min-width: 250px; justify-content: flex-end;">
        <?php if (basename($_SERVER['PHP_SELF']) !== 'index.php'): ?>
            <a href="index.php" style="
                color: white;
                text-decoration: none;
                background-color: #011a00ff;
                padding: 8px 14px;
                border-radius: 5px;
                transition: background 0.3s;
            " onmouseover="this.style.backgroundColor='#010f00ff'" 
              onmouseout="this.style.backgroundColor='#011a00ff'">
                Volver al inicio
            </a>
        <?php else: ?>
            <!-- Espacio invisible para mantener el tamaño -->
            <a style="
                visibility: hidden;
                padding: 8px 14px;
            ">Volver al inicio</a>
        <?php endif; ?>

        <a href="logout.php" style="
            color: white;
            text-decoration: none;
            background-color: #011a00ff;
            padding: 8px 14px;
            border-radius: 5px;
            transition: background 0.3s;
        " onmouseover="this.style.backgroundColor='#010f00ff'" 
          onmouseout="this.style.backgroundColor='#011a00ff'">
            Cerrar Sesión
        </a>
    </div>
</nav>
