<div class="form-container">
    <h2>Buscar por nombre de producto o id ítem</h2>
    <form action="../busqueda/buscar.php" method="get">
        <input type="text" name="query" placeholder="Nombre o ID del ítem" required>
        <br>
        <button type="submit">Buscar</button>
    </form>

    <form action="../inventario/items.php" method="get">
        <button type="submit">Mostrar todo</button>
    </form>
</div>