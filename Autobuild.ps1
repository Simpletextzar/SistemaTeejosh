# Autobuild.ps1
# 
# DESCRIPCIÓN_GENERAL:
# Script de construcción automática para el Proyecto. Elimina builds anteriores,
# reinstala dependencias, y genera una nueva versión del proyecto Electron.
# Ideal para preparar el entorno de desarrollo o generar builds de distribución.
# 
# FUNCIONALIDADES_PRINCIPALES:
# - Cierra procesos antiguos de la aplicación para evitar conflictos
# - Elimina carpetas de builds anteriores (dist/, node_modules/)
# - Elimina instalaciones previas del sistema
# - Opcionalmente limpia templates de PostgreSQL
# - Reinstala dependencias npm y ejecuta el proceso de build
# 
# DATOS_IMPORTANTES:
# - Ejecutar con PowerShell como administrador si hay problemas de permisos
# - Ajustar las variables de rutas al inicio según la ubicación real del proyecto
# - El script ELIMINA DATOS - Verificar rutas antes de ejecutar
# - Ideal para integrar en pipelines de CI/CD
# 
# RELACIONES:
# - Usado por: Desarrolladores para reconstruir el proyecto desde cero
# - Utiliza: npm (Node Package Manager) y scripts definidos en package.json
# 
# EJEMPLOS_DE_USO:
# # Ejecutar desde PowerShell en la raíz del proyecto:
# .\Autobuild.ps1
# 
# # Ejecutar paso a paso (comentar secciones):
# # Comentar líneas 50-60 para no eliminar node_modules/
# 
# NOTAS_CSS/HTML:
# - Este script no modifica CSS/HTML directamente
# - Afecta la construcción de la aplicación Electron que incluye los assets web

# ============================================================================
# ENCABEZADO VISUAL
# ============================================================================
Write-Host "==========================" -ForegroundColor Cyan
Write-Host " Build Automático Electron " -ForegroundColor Cyan
Write-Host "==========================" -ForegroundColor Cyan

# ============================================================================
# VARIABLES DE CONFIGURACIÓN
# ============================================================================
# Ruta absoluta al proyecto - AJUSTAR SEGÚN TU ENTORNO
$projectPath   = "D:\Apps\XAMPP\htdocs\TeejoshElectron"

# Rutas derivadas (no modificar normalmente)
$distPath      = "$projectPath\dist"                            # Carpeta de distribución generada por electron-builder
$nodeModules   = "$projectPath\node_modules"                    # Dependencias de Node.js (se reinstalan)
$installedApp  = "$env:LOCALAPPDATA\Programs\teejoshelectron"   # Ruta donde se instala la app en Windows

# Ruta opcional para template de PostgreSQL 
$postgresTemplate = "$projectPath\postgres\pgdata_template"

# ============================================================================
# PASO 1: CERRAR PROCESOS ANTIGUOS
# ============================================================================
Write-Host "`n[1/6] Cerrando posibles procesos antiguos..." -ForegroundColor Yellow
# Busca y detiene cualquier instancia en ejecución de la aplicación
# ErrorAction SilentlyContinue evita errores si no hay procesos
Get-Process "teejoshelectron" -ErrorAction SilentlyContinue | Stop-Process -Force -ErrorAction SilentlyContinue

# ============================================================================
# PASO 2: ELIMINAR CARPETA dist/
# ============================================================================
Write-Host "[2/6] Eliminando carpeta dist/..." -ForegroundColor Yellow
# Test-Path verifica si la carpeta existe antes de intentar eliminarla
if (Test-Path $distPath) {
    # Remove-Item elimina recursivamente (-Recurse) y fuerza (-Force) la eliminación
    Remove-Item $distPath -Recurse -Force
    Write-Host "   ✓ dist/ eliminada" -ForegroundColor Green
} else {
    Write-Host "   X dist/ no existe, omitiendo..." -ForegroundColor Gray
}

# ============================================================================
# PASO 3: ELIMINAR node_modules/
# ============================================================================
Write-Host "[3/6] Eliminando carpeta node_modules/..." -ForegroundColor Yellow
if (Test-Path $nodeModules) {
    # node_modules/ puede ser muy grande, esto asegura limpieza completa
    Remove-Item $nodeModules -Recurse -Force
    Write-Host "   ✓ node_modules/ eliminada" -ForegroundColor Green
} else {
    Write-Host "   X node_modules/ no existe, omitiendo..." -ForegroundColor Gray
}

# ============================================================================
# PASO 4: ELIMINAR INSTALACIÓN PREVIA
# ============================================================================
Write-Host "[4/6] Eliminando instalación previa..." -ForegroundColor Yellow
if (Test-Path $installedApp) {
    # Elimina la aplicación instalada en el sistema
    Remove-Item $installedApp -Recurse -Force
    Write-Host "   ✓ Instalación previa eliminada" -ForegroundColor Green
} else {
    Write-Host "   X No hay instalación previa, omitiendo..." -ForegroundColor Gray
}

# ============================================================================
# PASO 5: LIMPIEZA OPCIONAL DE POSTGRESQL TEMPLATE
# ============================================================================
Write-Host "[5/6] Limpiando postgres/pgdata_template/..." -ForegroundColor Yellow
# Solo ejecutar si el proyecto usa PostgreSQL y se necesita template fresco
if (Test-Path $postgresTemplate) {
    Remove-Item $postgresTemplate -Recurse -Force
    Write-Host "   ✓ Template PostgreSQL eliminado" -ForegroundColor Green
} else {
    Write-Host "   X Template PostgreSQL no existe, omitiendo..." -ForegroundColor Gray
}

# ============================================================================
# PASO 6: INSTALAR DEPENDENCIAS Y CONSTRUIR
# ============================================================================
Write-Host "[6/6] Instalando dependencias npm..." -ForegroundColor Green
# npm install descarga e instala todas las dependencias listadas en package.json
npm install

Write-Host "Ejecutando npm run build..." -ForegroundColor Green
# npm run build ejecuta el script "build" definido en package.json
# Normalmente ejecuta electron-builder o similar
npm run build

# ============================================================================
# FINALIZACIÓN
# ============================================================================
Write-Host "==========================" -ForegroundColor Cyan
Write-Host "     BUILD COMPLETADO     " -ForegroundColor Cyan
Write-Host "==========================" -ForegroundColor Cyan
Write-Host "Ubicación del build: $distPath" -ForegroundColor White
Write-Host "La aplicación está lista para distribuir" -ForegroundColor White