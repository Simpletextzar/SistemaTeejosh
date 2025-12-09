/**
 * create-template.js
 * 
 * DESCRIPCIÓN_GENERAL:
 * Script para crear un cluster PostgreSQL pre-configurado y optimizado para el Proyecto.
 * Genera una plantilla (template) de base de datos que se incluye en el instalador,
 * reduciendo el tiempo de primer inicio de ~10 segundos a ~2 segundos al evitar la
 * inicialización desde cero en cada instalación.
 * 
 * FUNCIONALIDADES_PRINCIPALES:
 * - Verifica la existencia de binarios de PostgreSQL (initdb.exe)
 * - Elimina templates anteriores si existen
 * - Crea un nuevo cluster PostgreSQL con configuración UTF8 y autenticación trust
 * - Aplica optimizaciones específicas para arranque rápido en entorno local
 * - Genera un informe con ubicación y tamaño del template creado
 * 
 * DATOS_IMPORTANTES:
 * - Requisitos: Node.js instalado y binarios PostgreSQL en postgres/bin/
 * - Beneficio: Reduce tiempo de primer inicio de base de datos en ~80%
 * - Ubicación output: postgres/pgdata_template/ (se incluye en el build)
 * - Precaución: El template puede ser grande (~30MB), incluirlo aumenta tamaño del instalador
 * 
 * RELACIONES:
 * - Usado por: Desarrolladores para preparar el template antes de construir el instalador
 * - Utiliza: Módulos Node.js (child_process, fs, path) y binarios PostgreSQL
 * - Genera: postgres/pgdata_template/ que se referencia en package.json -> extraResources
 * 
 * EJEMPLOS_DE_USO:
 * // Ejecutar desde la raíz del proyecto (donde está package.json):
 * node create-template.js
 * 
 * // Salida esperada:
 * // TEMPLATE CREADO EXITOSAMENTE
 * // Ubicación: D:\Proyecto2\postgres\pgdata_template
 * // Tamaño: 32.45 MB
 * 
 * NOTAS_CSS/HTML:
 * - Este script no modifica CSS/HTML directamente
 * - El template generado afecta el rendimiento de inicio de la aplicación Electron
 *   que contiene el frontend HTML/CSS/JS
 */

// ============================================================================
// IMPORTACIÓN DE MÓDULOS
// ============================================================================
const { spawn } = require('child_process');  // Para ejecutar comandos del sistema
const path = require('path');                // Para manejo de rutas multiplataforma
const fs = require('fs');                    // Para operaciones de archivos

// ============================================================================
// CONFIGURACIÓN DE RUTAS
// ============================================================================
// POSTGRES_BIN: Ruta a los binarios de PostgreSQL (se espera initdb.exe aquí)
const POSTGRES_BIN = path.join(__dirname, 'postgres', 'bin');

// TEMPLATE_DIR: Ruta donde se creará el template (se incluirá en el instalador)
const TEMPLATE_DIR = path.join(__dirname, 'postgres', 'pgdata_template');

// ============================================================================
// FUNCIÓN AUXILIAR: execCommand
// ============================================================================
/**
 * Ejecuta un comando del sistema y retorna una promesa con el resultado.
 * Muestra la salida en tiempo real y captura stdout/stderr para análisis.
 * 
 * @param {string} cmd - Comando a ejecutar (ej: 'initdb.exe')
 * @param {string[]} args - Argumentos para el comando
 * @returns {Promise<{code: number, stdout: string, stderr: string}>}
 * 
 * @example
 * await execCommand('initdb.exe', ['-D', 'ruta', '-U', 'postgres']);
 */
function execCommand(cmd, args) {
  return new Promise((resolve, reject) => {
    // Mostrar comando que se ejecutará (útil para depuración)
    console.log(`Ejecutando: ${cmd} ${args.join(' ')}`);
    
    // spawn crea un proceso hijo sin bloqueo
    const p = spawn(cmd, args, { 
      windowsHide: true  // En Windows, oculta la ventana de consola
    });
    
    let stdout = '';  // Captura de salida estándar
    let stderr = '';  // Captura de errores
    
    // Escuchar datos de stdout (salida normal)
    p.stdout?.on('data', (d) => {
      stdout += d.toString();
      process.stdout.write(d);  // Mostrar en consola en tiempo real
    });
    
    // Escuchar datos de stderr (errores)
    p.stderr?.on('data', (d) => {
      stderr += d.toString();
      process.stderr.write(d);  // Mostrar errores en consola
    });
    
    // Manejar errores del proceso (ej: comando no encontrado)
    p.on('error', reject);
    
    // Cuando el proceso termina
    p.on('close', (code) => {
      resolve({ code, stdout, stderr });
    });
  });
}

// ============================================================================
// FUNCIÓN PRINCIPAL: createTemplate
// ============================================================================
/**
 * Función principal que orquesta la creación del template PostgreSQL.
 * Sigue un flujo paso a paso: verificar → limpiar → crear → optimizar → verificar.
 */
async function createTemplate() {
  console.log('==========================================');
  console.log('    CREAR CLUSTER POSTGRESQL TEMPLATE     ');
  console.log('==========================================\n');

  // --------------------------------------------------------------------------
  // PASO 1: VERIFICAR BINARIOS DE POSTGRESQL
  // --------------------------------------------------------------------------
  console.log('[1/5] Verificando binarios de PostgreSQL...');
  const initdb = path.join(POSTGRES_BIN, 'initdb.exe');
  
  // Verificar que initdb.exe existe en la ruta esperada
  if (!fs.existsSync(initdb)) {
    console.error(' X Error: No se encontro initdb.exe en postgres/bin/');
    console.error('   Posibles soluciones:');
    console.error('   1. Descargar PostgreSQL portable y extraer en postgres/');
    console.error('   2. Verificar que la estructura sea: postgres/bin/initdb.exe');
    console.error('   3. Ajustar la ruta en POSTGRES_BIN si es necesario');
    process.exit(1);  // Terminar con código de error
  }
  console.log('   ✓ initdb.exe encontrado');

  // --------------------------------------------------------------------------
  // PASO 2: LIMPIAR TEMPLATE ANTERIOR SI EXISTE
  // --------------------------------------------------------------------------
  console.log('[2/5] Limpiando template anterior...');
  if (fs.existsSync(TEMPLATE_DIR)) {
    // Eliminar recursivamente el directorio existente
    fs.rmSync(TEMPLATE_DIR, { recursive: true, force: true });
    console.log('   ✓ Template anterior eliminado');
  } else {
    console.log('   X  No hay template anterior, continuando...');
  }

  // --------------------------------------------------------------------------
  // PASO 3: CREAR DIRECTORIO PARA EL TEMPLATE
  // --------------------------------------------------------------------------
  console.log('[3/5] Creando directorio template...');
  // mkdirSync con recursive:true crea directorios padres si no existen
  fs.mkdirSync(TEMPLATE_DIR, { recursive: true });
  console.log('   ✓ Directorio creado:', TEMPLATE_DIR);

  // --------------------------------------------------------------------------
  // PASO 4: INICIALIZAR CLUSTER POSTGRESQL
  // --------------------------------------------------------------------------
  console.log('[4/5] Inicializando cluster PostgreSQL...\n');
  
  // Argumentos para initdb.exe:
  // -D: Directorio de datos (donde se crea el cluster)
  // -U: Usuario superusuario (postgres por convención)
  // -E: Codificación de caracteres (UTF8 para soporte internacional)
  // --locale=C: Configuración regional simple (evita problemas de collation)
  // --auth=trust: Autenticación sin contraseña (para entorno local/desarrollo)
  const result = await execCommand(initdb, [
    '-D', TEMPLATE_DIR,
    '-U', 'postgres',
    '-E', 'UTF8',
    '--locale=C',
    '--auth=trust'
  ]);

  // Verificar que initdb terminó exitosamente (código 0)
  if (result.code !== 0) {
    console.error(` X Error: initdb fallo con codigo ${result.code}`);
    console.error('   Salida de error:', result.stderr);
    process.exit(1);
  }
  console.log('   ✓ Cluster PostgreSQL inicializado');

  // --------------------------------------------------------------------------
  // PASO 5: OPTIMIZAR CONFIGURACIÓN PARA ARRANQUE RÁPIDO
  // --------------------------------------------------------------------------
  console.log('[5/5] Optimizando configuración...');
  
  // Ruta al archivo de configuración de PostgreSQL
  const configFile = path.join(TEMPLATE_DIR, 'postgresql.conf');
  
  // Opciones de optimización para entorno local/desarrollo:
  // - shared_buffers: Memoria para caché (32MB es suficiente para desarrollo)
  // - max_connections: Límite de conexiones concurrentes
  // - fsync, synchronous_commit, full_page_writes: Desactivados para mayor velocidad
  // - wal_level: minimal reduce overhead de Write-Ahead Logging
  // - checkpoint_timeout: Menos checkpoints = menos I/O
  const optimizations = `

# =========================================================
# OPTIMIZACIONES PARA ENTORNO LOCAL
# Configuración para arranque rápido y bajo consumo de recursos
# =========================================================
shared_buffers = 32MB
max_connections = 20
fsync = off
synchronous_commit = off
full_page_writes = off
wal_level = minimal
max_wal_senders = 0
checkpoint_timeout = 15min
checkpoint_completion_target = 0.9
# =========================================================
`;

  // Agregar las optimizaciones al final del archivo de configuración
  fs.appendFileSync(configFile, optimizations);
  console.log('   ✓ Configuracion optimizada para arranque rapido');

  // --------------------------------------------------------------------------
  // INFORMACIÓN FINAL Y MÉTRICAS
  // --------------------------------------------------------------------------
  
  /**
   * Calcula el tamaño total de un directorio de forma recursiva.
   * @param {string} dirPath - Ruta al directorio
   * @returns {number} Tamaño en bytes
   */
  const getDirectorySize = (dirPath) => {
    let size = 0;
    const files = fs.readdirSync(dirPath);
    
    for (const file of files) {
      const filePath = path.join(dirPath, file);
      const stats = fs.statSync(filePath);
      
      if (stats.isDirectory()) {
        // Llamada recursiva para subdirectorios
        size += getDirectorySize(filePath);
      } else {
        // Sumar tamaño del archivo
        size += stats.size;
      }
    }
    
    return size;
  };

  // Calcular tamaño del template creado
  const sizeBytes = getDirectorySize(TEMPLATE_DIR);
  const sizeMB = (sizeBytes / (1024 * 1024)).toFixed(2);

  // Mostrar informe final detallado
  console.log('==========================================');
  console.log('     ✓ TEMPLATE CREADO EXITOSAMENTE       ');
  console.log('==========================================');
  console.log(`Ubicación: ${TEMPLATE_DIR}`);
  console.log(`Tamaño: ${sizeMB} MB`);
  console.log(`Usuario: postgres (sin contrasena)`);
  console.log(`Codificación: UTF8`);
  console.log('\nSIGUIENTE PASO:');
  console.log('   En package.json, asegúrate de incluir en "extraResources":');
  console.log('   {');
  console.log('     "from": "postgres/pgdata_template/",');
  console.log('     "to": "postgres/pgdata_template/"');
  console.log('   }');
  console.log('\nBENEFICIO:');
  console.log('   Tiempo de primer inicio reducido de ~10s a ~2s');
  console.log('   (No necesita inicializar cluster en cada instalacion)');
  console.log('\nPRECAUCIÓN:');
  console.log('   Este template NO es para producción');
  console.log('   La autenticacion está en modo "trust" (sin contrasena)');
  console.log('==========================================\n');
}

// ============================================================================
// EJECUCIÓN PRINCIPAL Y MANEJO DE ERRORES
// ============================================================================
// Ejecutar la función principal y capturar cualquier error no manejado
createTemplate().catch((error) => {
  console.error('\nError durante la creación del template:');
  console.error('   Mensaje:', error.message);
  console.error('   Stack:', error.stack);
  console.error('\nSoluciones posibles:');
  console.error('   1. Verificar que PostgreSQL esté descargado en postgres/');
  console.error('   2. Comprobar permisos de escritura en el directorio');
  console.error('   3. Cerrar cualquier instancia de PostgreSQL en ejecución');
  process.exit(1);  // Terminar con código de error
});