/**
 * main.js
 * 
 * DESCRIPCIÓN_GENERAL:
 * Punto de entrada principal de la aplicación Electron para el Proyecto.
 * Controla el ciclo de vida de la aplicación, inicia servicios (PostgreSQL, PHP), 
 * maneja la creación de ventanas y proporciona comunicación entre procesos.
 * 
 * FUNCIONALIDADES_PRINCIPALES:
 * - Gestión del ciclo de vida de la aplicación Electron (app.whenReady, window-all-closed)
 * - Inicialización y control de servicios: PostgreSQL y PHP Server
 * - Creación y configuración de la ventana principal con splash screen
 * - Sistema de logging y métricas de rendimiento
 * - Manejo de archivos de configuración y templates de base de datos
 * - Comunicación IPC entre procesos (main y renderer)
 * 
 * DATOS_IMPORTANTES:
 * -  ARQUITECTURA: Proceso principal de Electron (Node.js/Electron APIs)
 * -  ENTORNO: Distingue entre desarrollo y producción con app.isPackaged
 * -  SERVICIOS: PostgreSQL en puerto 5432, PHP en puerto 8000 (ambos en localhost)
 * -  ALMACENAMIENTO: Usa app.getPath('userData') para datos persistentes del usuario
 * -  LOGS: Escribe logs en archivo en userData/app.log
 * 
 * RELACIONES:
 * - Es ejecutado por: Electron runtime (comando: electron .)
 * - Requiere: package.json para configuración y dependencias
 * - Se comunica con: Ventana renderer (HTML/CSS/JS) a través de preload.js e IPC
 * - Controla: PostgreSQL (pg_ctl, initdb) y PHP (php.exe) como procesos hijos
 * 
 * EJEMPLOS_DE_USO:
 * // Ejecutar en desarrollo:
 * npm start
 * 
 * // Ejecutar versión empaquetada:
 * ./dist/Teejosh Inventario Setup 1.0.0.exe
 * 
 * // Ver logs de la aplicación:
 * C:\Users\[Usuario]\AppData\Roaming\teejoshelectron\app.log
 * 
 * NOTAS_CSS/HTML:
 * - Contiene HTML/CSS embebido para el splash screen (líneas 441-518)
 * - Define estilos visuales para la pantalla de carga inicial
 * - Usa CSS moderno: flexbox, gradientes, animaciones, sombras
 * - El HTML incluye un script para recibir mensajes de estado
 */

// ============================================================================
// IMPORTACIÓN DE MÓDULOS
// ============================================================================
// Electron: Framework principal para aplicaciones de escritorio
const { app, BrowserWindow, dialog, ipcMain } = require("electron");

// Node.js Core: Módulos para manejo de sistema de archivos y procesos
const path = require("path");      // Manejo de rutas multiplataforma
const fs = require("fs");          // Operaciones de archivos
const { spawn } = require("child_process"); // Ejecutar procesos del sistema

// ============================================================================
// VARIABLES GLOBALES
// ============================================================================
let phpServer = null;        // Referencia al proceso del servidor PHP
let postgresStarted = false; // Flag para controlar si PostgreSQL está ejecutándose
let mainWindow = null;       // Referencia a la ventana principal de la aplicación

// ============================================================================
// FUNCIÓN: rootPath()
// ============================================================================
/**
 * Determina la ruta raíz de la aplicación según si está empaquetada o en desarrollo.
 * 
 * @returns {string} Ruta absoluta al directorio de recursos
 * 
 * DESCRIPCIÓN:
 * - En desarrollo: Retorna __dirname (donde está main.js)
 * - En producción: Retorna process.resourcesPath (dentro del .app o .exe empaquetado)
 * 
 * IMPORTANCIA: Esta función es CRÍTICA para encontrar los binarios correctos (PHP, PostgreSQL)
 * independientemente de si la app está empaquetada o se ejecuta desde código fuente.
 */
const isDev = !app.isPackaged; // Detecta si la app está empaquetada (producción)
function rootPath() {
  return isDev ? __dirname : process.resourcesPath;
}

// ============================================================================
// OPTIMIZACIONES DE PERFORMANCE PARA ELECTRON
// ============================================================================
// Estas opciones se aplican SOLO en producción para mejorar el rendimiento
if (app.isPackaged) {
  // Optimizaciones del motor V8 JavaScript:
  // --expose-gc: Permite llamar a garbage collection manualmente si es necesario
  // --max-old-space-size=512: Limita la memoria máxima a 512MB para evitar fugas
  app.commandLine.appendSwitch('js-flags', '--expose-gc --max-old-space-size=512');
  
  // Evita que Electron reduzca prioridad de la ventana cuando está en segundo plano
  app.commandLine.appendSwitch('disable-renderer-backgrounding');
}

// ============================================================================
// SISTEMA DE LOGGING MEJORADO
// ============================================================================
// Ruta del archivo de log: En el directorio de datos del usuario (persistente)
const logFile = path.join(app.getPath('userData'), 'app.log');

/**
 * Función de logging unificada que escribe en consola y archivo
 * 
 * @param {string} message - Mensaje a loguear (se limpian emojis para archivo)
 * 
 * FUNCIONALIDAD:
 * 1. Agrega timestamp ISO 8601
 * 2. Limpia emojis para compatibilidad con editores de texto plano
 * 3. Escribe en consola (visible en DevTools)
 * 4. Añade al archivo de log en userData
 * 
 * USO:
 * log("Iniciando servicios..."); // Output: "[2024-01-15T10:30:00Z] Iniciando servicios..."
 */
function log(message) {
  const timestamp = new Date().toISOString();
  
  // Expresión regular para eliminar emojis (solo en archivo, no en consola)
  const cleanMessage = message.replace(/[\u{1F300}-\u{1F9FF}]|[\u{2600}-\u{26FF}]|[\u{2700}-\u{27BF}]/gu, '');
  
  const logMessage = `[${timestamp}] ${message}\n`;
  console.log(cleanMessage); // Consola (DevTools)
  fs.appendFileSync(logFile, logMessage); // Archivo persistente
}

// ============================================================================
// SISTEMA DE MÉTRICAS DE RENDIMIENTO
// ============================================================================
// Objeto para almacenar marcas de tiempo de diferentes etapas del inicio
const perfMarks = {};

/**
 * Registra una marca de tiempo para medir duración entre eventos
 * 
 * @param {string} label - Nombre identificador de la marca de tiempo
 * 
 * FUNCIONALIDAD:
 * 1. Almacena la hora actual con Date.now()
 * 2. Si hay una marca anterior, calcula y loguea la diferencia
 * 3. Útil para optimizar tiempos de inicio de la aplicación
 * 
 * EJEMPLO:
 * markPerf('app_start');
 * // ... código que tarda tiempo ...
 * markPerf('services_ready');
 * // Log: "Tiempo app_start -> services_ready: 1250ms"
 */
function markPerf(label) {
  perfMarks[label] = Date.now();
  
  // Si hay más de una marca, calcular y mostrar la diferencia con la anterior
  if (Object.keys(perfMarks).length > 1) {
    const keys = Object.keys(perfMarks);
    const prev = keys[keys.length - 2];
    const diff = perfMarks[label] - perfMarks[prev];
    log(`Tiempo ${prev} -> ${label}: ${diff}ms`);
  }
}

// ============================================================================
// FUNCIÓN: execCommand()
// ============================================================================
/**
 * Ejecuta un comando del sistema y retorna una promesa con el resultado
 * 
 * @param {string} cmd - Comando a ejecutar (ej: 'pg_ctl.exe')
 * @param {string[]} args - Argumentos para el comando
 * @param {Object} options - Opciones adicionales para spawn
 * @returns {Promise<{code: number, stdout: string, stderr: string}>}
 * 
 * CARACTERÍSTICAS:
 * - Usa spawn (no exec) para manejar salida en tiempo real
 * - Captura stdout y stderr por separado
 * - Oculta la ventana de consola en Windows (windowsHide: true)
 * - Maneja errores y los loguea apropiadamente
 * 
 * USO:
 * const result = await execCommand('pg_ctl', ['-D', dataDir, 'start']);
 */
function execCommand(cmd, args, options = {}) {
  return new Promise((resolve, reject) => {
    // spawn inicia un proceso hijo
    const p = spawn(cmd, args, { 
      ...options, 
      windowsHide: true // Importante en Windows para no mostrar consola
    });

    let stdout = '';  // Salida estándar del comando
    let stderr = '';  // Errores del comando

    // Capturar salida normal
    p.stdout?.on("data", (d) => {
      stdout += d.toString();
    });

    // Capturar errores
    p.stderr?.on("data", (d) => {
      stderr += d.toString();
    });

    // Manejar error en el proceso mismo (ej: comando no encontrado)
    p.on("error", (error) => {
      log(`[ERROR] ${error.message}`);
      reject(error);
    });

    // Cuando el proceso termina (éxito o error)
    p.on("close", (code) => {
      // code 0 = éxito, otros códigos = error
      resolve({ code, stdout, stderr });
    });
  });
}

// ============================================================================
// FUNCIONES DE CONTROL DE ARCHIVOS
// ============================================================================
/**
 * Verifica si la base de datos ya fue importada
 * 
 * @returns {boolean} true si existe el archivo marcador .db_imported
 * 
 * PROPÓSITO: Evitar reimportar la base de datos en cada inicio, 
 * ahorrando tiempo y evitando errores por datos duplicados.
 */
function isDatabaseImported() {
  return fs.existsSync(path.join(app.getPath('userData'), ".db_imported"));
}

/**
 * Marca que la base de datos fue importada exitosamente
 * 
 * PROPÓSITO: Crear un archivo marcador para recordar que la 
 * importación ya se realizó en ejecuciones futuras.
 */
function markDatabaseImported() {
  fs.writeFileSync(
    path.join(app.getPath('userData'), ".db_imported"),
    new Date().toISOString() // Guarda timestamp para referencia
  );
}

// ============================================================================
// 1. VERIFICACIÓN DE ARCHIVOS NECESARIOS
// ============================================================================
/**
 * Verifica que todos los archivos necesarios estén presentes antes de iniciar
 * 
 * @throws {Error} Si falta algún archivo crítico
 * 
 * ARCHIVOS VERIFICADOS:
 * - PostgreSQL: pg_ctl.exe, psql.exe, pg_isready.exe
 * - PHP: php.exe
 * - Aplicación: app/public/index.php (punto de entrada PHP)
 * 
 * IMPORTANCIA: Previene errores en tiempo de ejecución verificando 
 * dependencias al inicio.
 */
function verifyFiles() {
  log("=== Verificando archivos necesarios ===");
  
  // Lista de archivos críticos para el funcionamiento
  const requiredFiles = [
    path.join(rootPath(), "postgres", "bin", "pg_ctl.exe"),
    path.join(rootPath(), "postgres", "bin", "psql.exe"),
    path.join(rootPath(), "postgres", "bin", "pg_isready.exe"),
    path.join(rootPath(), "php", "php.exe"),
    path.join(rootPath(), "app", "public", "index.php")
  ];

  // Filtrar archivos que no existen
  const missing = requiredFiles.filter(file => !fs.existsSync(file));
  
  if (missing.length > 0) {
    log(`ERROR: Archivos faltantes: ${missing.join(', ')}`);
    throw new Error("Faltan archivos necesarios. Revisa app.log");
  }

  log("OK: Todos los archivos necesarios estan presentes");
}

// ============================================================================
// 2. VERIFICACIÓN DE POSTGRESQL EN EJECUCIÓN
// ============================================================================
/**
 * Verifica si PostgreSQL ya está corriendo en localhost:5432
 * 
 * @returns {Promise<boolean>} true si PostgreSQL está listo para conexiones
 * 
 * FUNCIONALIDAD:
 * - Usa pg_isready.exe para probar la conexión
 * - Timeout de 2 segundos para evitar bloqueos largos
 * - Retorna false si hay error o timeout (no lanza excepción)
 */
async function isPostgresRunning() {
  const pgIsReady = path.join(rootPath(), "postgres", "bin", "pg_isready.exe");
  
  try {
    // pg_isready prueba si PostgreSQL acepta conexiones
    const result = await execCommand(pgIsReady, ["-h", "127.0.0.1", "-p", "5432"], {
      timeout: 2000 // Timeout de 2 segundos
    });
    return result.code === 0; // Código 0 = PostgreSQL listo
  } catch {
    return false; // Error o timeout = PostgreSQL no disponible
  }
}

// ============================================================================
// 3. INICIALIZACIÓN DEL CLUSTER POSTGRESQL
// ============================================================================
/**
 * Asegura que PostgreSQL esté inicializado, usando template si está disponible
 * 
 * FLUJO:
 * 1. Si existe template pre-configurado, cópialo (más rápido)
 * 2. Si ya existe directorio de datos, úsalo
 * 3. Si no hay nada, inicializa desde cero con initdb
 * 
 * OPTIMIZACIÓN: El template reduce tiempo de inicio de ~10s a ~2s
 */
async function ensurePostgresInitialized() {
  const dataDir = path.join(app.getPath('userData'), "pgdata");
  const templateDir = path.join(rootPath(), "postgres", "pgdata_template");
  
  log(`Directorio de datos: ${dataDir}`);

  // CASO IDEAL: Usar template pre-configurado (más rápido)
  if (!fs.existsSync(dataDir) && fs.existsSync(templateDir)) {
    log("Copiando cluster PostgreSQL pre-configurado...");
    markPerf('copy_cluster_start');
    fs.cpSync(templateDir, dataDir, { recursive: true }); // Copia recursiva
    markPerf('copy_cluster_end');
    log("OK: Cluster copiado exitosamente");
    return;
  }

  // CASO NORMAL: Directorio de datos ya existe
  if (fs.existsSync(dataDir)) {
    const files = fs.readdirSync(dataDir);
    if (files.length > 0) {
      log("OK: Cluster PostgreSQL ya existe");
      return;
    }
  }

  // CASO FALLBACK: Inicializar desde cero (más lento)
  log("Inicializando cluster PostgreSQL...");
  const initdb = path.join(rootPath(), "postgres", "bin", "initdb.exe");
  
  // Crear directorio si no existe
  if (!fs.existsSync(dataDir)) {
    fs.mkdirSync(dataDir, { recursive: true });
  }

  // Ejecutar initdb para crear nuevo cluster
  const result = await execCommand(initdb, [
    "-D", dataDir,           // Directorio de datos
    "-U", "postgres",        // Usuario superusuario
    "-E", "UTF8",           // Codificación UTF-8
    "--locale=C",           // Configuración regional simple
    "--auth=trust"          // Sin autenticación (para desarrollo local)
  ]);

  if (result.code !== 0) {
    throw new Error(`initdb fallo con codigo ${result.code}`);
  }

  // Aplicar optimizaciones de configuración
  await optimizePostgresConfig(dataDir);
  
  log("OK: Cluster PostgreSQL creado y optimizado");
}

// ============================================================================
// 4. OPTIMIZACIÓN DE CONFIGURACIÓN POSTGRESQL
// ============================================================================
/**
 * Aplica optimizaciones al archivo postgresql.conf para entorno local
 * 
 * @param {string} dataDir - Directorio de datos de PostgreSQL
 * 
 * OPTIMIZACIONES APLICADAS:
 * - shared_buffers: Reduce memoria usada (32MB vs default 128MB)
 * - fsync, synchronous_commit, full_page_writes: Desactivados para velocidad
 * - wal_level: minimal reduce overhead de logs
 * - checkpoint_timeout: Menos checkpoints = menos I/O
 * 
 * ADVERTENCIA: Estas configuraciones NO son seguras para producción
 */
async function optimizePostgresConfig(dataDir) {
  const configFile = path.join(dataDir, "postgresql.conf");
  
  if (!fs.existsSync(configFile)) {
    log("WARN: postgresql.conf no encontrado, omitiendo optimizacion");
    return;
  }

  const optimizations = `

# ===== OPTIMIZACIONES TEEJOSH =====
# Configuracion para arranque rapido en entorno local
shared_buffers = 32MB
max_connections = 20
fsync = off
synchronous_commit = off
full_page_writes = off
wal_level = minimal
max_wal_senders = 0
checkpoint_timeout = 15min
checkpoint_completion_target = 0.9
# ==================================
`;

  fs.appendFileSync(configFile, optimizations);
  log("OK: Configuracion PostgreSQL optimizada");
}

// ============================================================================
// 5. INICIO DE POSTGRESQL (MÉTODO MEJORADO)
// ============================================================================
/**
 * Inicia PostgreSQL con detección robusta de estado
 * 
 * FLUJO:
 * 1. Verificar si ya está corriendo
 * 2. Iniciar pg_ctl sin esperar (-w) para evitar bloqueos en Windows
 * 3. Verificar periódicamente con pg_isready hasta que esté listo
 * 4. Fallback: Verificar archivo de log si pg_isready falla
 * 
 * CRÍTICO: No usar flag -w en Windows porque causa deadlocks
 */
async function startPostgres() {
  // Verificar si ya está corriendo
  if (await isPostgresRunning()) {
    log("OK: PostgreSQL ya esta corriendo");
    postgresStarted = true;
    return;
  }

  const pgCtl = path.join(rootPath(), "postgres", "bin", "pg_ctl.exe");
  const dataDir = path.join(app.getPath('userData'), "pgdata");
  const logFilePg = path.join(app.getPath('userData'), "postgres.log");

  // Limpiar archivo de log anterior si existe y está bloqueado
  try {
    if (fs.existsSync(logFilePg)) {
      fs.unlinkSync(logFilePg);
      log("Archivo de log anterior eliminado");
    }
  } catch (error) {
    log(`WARN: No se pudo eliminar postgres.log: ${error.message}`);
  }

  log("Iniciando PostgreSQL...");

  // IMPORTANTE: No usar -w (wait) en Windows - causa deadlocks
  // Iniciamos el proceso y luego verificamos manualmente
  const startProcess = spawn(pgCtl, [
    "-D", dataDir,
    "-l", logFilePg,        // Archivo de log para PostgreSQL
    "-o", "-p 5432 -h 127.0.0.1", // Opciones: puerto 5432, solo localhost
    "start"
  ], { 
    windowsHide: true, 
    detached: false,
    stdio: 'ignore' // CRÍTICO: Ignorar stdio para evitar bloqueos
  });

  // Esperar máximo 2 segundos para que el comando se ejecute
  // Usamos Promise.race para tener un timeout seguro
  await Promise.race([
    new Promise((resolve) => {
      startProcess.on('error', (error) => {
        log(`ERROR al ejecutar pg_ctl: ${error.message}`);
        resolve(); // No fallar, intentaremos verificar después
      });
      
      startProcess.on('close', (code) => {
        if (code !== 0 && code !== 1) {
          log(`WARN: pg_ctl retorno codigo ${code}`);
        }
        resolve();
      });
    }),
    new Promise(resolve => setTimeout(resolve, 2000)) // Timeout de 2 segundos
  ]);

  log("Comando pg_ctl ejecutado, verificando estado...");

  // Verificar periódicamente si PostgreSQL está listo (hasta 10 segundos)
  const pgIsReady = path.join(rootPath(), "postgres", "bin", "pg_isready.exe");
  log("Esperando que PostgreSQL este listo...");
  
  for (let i = 0; i < 50; i++) { // 50 intentos * 200ms = 10 segundos máximo
    try {
      const readyResult = await execCommand(pgIsReady, ["-h", "127.0.0.1", "-p", "5432"]);

      if (readyResult.code === 0) {
        log(`OK: PostgreSQL listo en ${(i + 1) * 200}ms`);
        postgresStarted = true;
        return;
      }
    } catch (error) {
      // Ignorar errores durante la espera
    }

    await new Promise((r) => setTimeout(r, 200)); // Esperar 200ms entre intentos
  }

  // Último recurso: Verificar archivo de log de PostgreSQL
  if (fs.existsSync(logFilePg)) {
    const logContent = fs.readFileSync(logFilePg, 'utf8');
    if (logContent.includes('ready to accept connections')) {
      log("OK: PostgreSQL esta listo (verificado via log)");
      postgresStarted = true;
      return;
    }
  }

  throw new Error("PostgreSQL no respondio despues de 10 segundos");
}

// ============================================================================
// 6. IMPORTACIÓN DE BASE DE DATOS (dump.sql)
// ============================================================================
/**
 * Importa la base de datos desde dump.sql si no se ha importado antes
 * 
 * FLUJO:
 * 1. Verificar marcador .db_imported
 * 2. Crear base de datos 'teejosh' si no existe
 * 3. Ejecutar psql con el archivo dump.sql
 * 4. Marcar como importada para futuros inicios
 * 
 * OPTIMIZACIÓN: Solo se ejecuta la primera vez que la app se inicia
 */
async function importDatabase() {
  // Evitar reimportar si ya se hizo
  if (isDatabaseImported()) {
    log("OK: Base de datos ya importada");
    return;
  }

  const dumpPath = path.join(rootPath(), "postgres", "dump.sql");
  const psql = path.join(rootPath(), "postgres", "bin", "psql.exe");

  // Si no existe dump.sql, solo marcar como importada y continuar
  if (!fs.existsSync(dumpPath)) {
    log("WARN: dump.sql no encontrado, omitiendo importacion");
    markDatabaseImported();
    return;
  }

  log("Importando base de datos...");

  // Notificar a la interfaz si la ventana está disponible
  if (mainWindow) {
    mainWindow.webContents.send('loading-status', 'Importando datos iniciales...');
  }

  // Crear base de datos (ignorar error si ya existe)
  await execCommand(psql, [
    "-U", "postgres",
    "-h", "127.0.0.1",
    "-p", "5432",
    "-d", "postgres", // Conectarse a base de datos por defecto
    "-c", "CREATE DATABASE teejosh WITH ENCODING='UTF8';"
  ], {
    env: { ...process.env, PGPASSWORD: "" } // Sin contraseña (auth=trust)
  });

  // Importar estructura y datos desde dump.sql
  const result = await execCommand(psql, [
    "-U", "postgres",
    "-h", "127.0.0.1",
    "-p", "5432",
    "-d", "teejosh",  // Base de datos destino
    "-f", dumpPath    // Archivo SQL a ejecutar
  ], {
    env: { ...process.env, PGPASSWORD: "" }
  });

  // Códigos de salida: 0=éxito, 3=ya existe (podemos ignorar)
  if (result.code === 0 || result.code === 3) {
    log("OK: Base de datos importada exitosamente");
    markDatabaseImported();
  } else {
    log(`WARN: Importacion termino con codigo ${result.code}`);
  }
}

// ============================================================================
// 7. INICIO DEL SERVIDOR PHP
// ============================================================================
/**
 * Inicia el servidor PHP embebido
 * 
 * CONFIGURACIÓN:
 * - PHP ejecutándose en: 127.0.0.1:8000
 * - Directorio raíz: app/public (contiene index.php)
 * - phpServer se mantiene como referencia global para cerrarlo limpiamente
 */
function startPHP() {
  const php = path.join(rootPath(), "php", "php.exe");
  const appFolder = path.join(rootPath(), "app", "public");

  log("Iniciando servidor PHP...");

  // spawn de PHP con servidor web embebido
  phpServer = spawn(
    php,
    ["-S", "127.0.0.1:8000", "-t", appFolder], // Servidor en puerto 8000, directorio app/public
    { cwd: appFolder, windowsHide: true } // Directorio de trabajo y ocultar consola
  );

  // Capturar y loguear salida de PHP
  phpServer.stdout?.on("data", (d) => {
    log(`[PHP] ${d.toString().trim()}`);
  });

  phpServer.stderr?.on("data", (d) => {
    log(`[PHP] ${d.toString().trim()}`);
  });

  log("OK: Servidor PHP iniciado en http://127.0.0.1:8000");
}

// ============================================================================
// 8. ESPERA POR PHP (VERIFICACIÓN DE DISPONIBILIDAD)
// ============================================================================
/**
 * Verifica que el servidor PHP esté respondiendo
 * 
 * @returns {Promise<boolean>} true si PHP responde, false si timeout
 * 
 * MÉTODO: Intenta hacer una petición HTTP GET a localhost:8000
 * hasta 20 veces con intervalos de 100ms (total 2 segundos máximo)
 */
async function waitForPHP() {
  const http = require('http'); // Módulo HTTP nativo de Node.js
  
  log("Esperando que PHP este listo...");
  
  for (let i = 0; i < 20; i++) {
    try {
      await new Promise((resolve, reject) => {
        const req = http.get('http://127.0.0.1:8000', (res) => {
          resolve(); // Éxito: PHP respondió
        });
        req.on('error', reject); // Error: PHP no responde
        req.setTimeout(100); // Timeout de 100ms por intento
      });
      
      log(`OK: PHP listo en ${(i + 1) * 100}ms`);
      return true;
    } catch (error) {
      await new Promise(r => setTimeout(r, 100)); // Esperar 100ms antes de reintentar
    }
  }
  
  log("WARN: PHP no respondio en 2 segundos, continuando de todas formas...");
  return false; // Timeout, pero continuamos
}

// ============================================================================
// 9. CREACIÓN DE VENTANA CON SPLASH SCREEN
// ============================================================================
/**
 * Crea y configura la ventana principal de Electron
 * 
 * CARACTERÍSTICAS:
 * - Tamaño: 1200x800 píxeles
 * - Sin barra de menú por defecto (autoHideMenuBar: true)
 * - Color de fondo oscuro (#1e1e2e) mientras carga
 * - Precarga preload.js para comunicación segura con renderer
 * - Splash screen HTML embebido mientras inician servicios
 */
function createWindow() {
  log("Creando ventana principal...");

  // Configuración de seguridad para webPreferences
  const preloadPath = path.join(__dirname, 'preload.js');
  const webPreferences = {
    nodeIntegration: false,     // CRÍTICO: No permitir Node.js en renderer
    contextIsolation: true,    // Aislar contexto de Electron del renderer
    sandbox: true,            // Ejecutar renderer en sandbox
    backgroundThrottling: false, // Evitar pausa cuando app está en segundo plano
    v8CacheOptions: 'code'    // Optimizar caché de V8
  };
  
  // Solo agregar preload si el archivo existe
  if (fs.existsSync(preloadPath)) {
    webPreferences.preload = preloadPath;
  }

  // Crear ventana principal con configuración
  mainWindow = new BrowserWindow({
    width: 1200,
    height: 800,
    autoHideMenuBar: true,     // Ocultar barra de menú (Alt para mostrar)
    show: false,               // No mostrar hasta que esté lista
    backgroundColor: '#1e1e2e', // Color oscuro para transiciones suaves
    webPreferences
  });

  // ==========================================================================
  // HTML/CSS DEL SPLASH SCREEN (EMBEBIDO)
  // ==========================================================================
  /**
   * NOTAS_CSS/HTML DETALLADAS:
   * 
   * ESTRUCTURA HTML:
   * - Contenedor principal centrado con flexbox
   * - Logo "Teejosh" grande (48px)
   * - Subtítulo "Sistema de Inventario"
   * - Spinner de carga animado con CSS
   * - Área de estado para mensajes de progreso
   * - Versión en la parte inferior
   * 
   * ESTILOS CSS:
   * - Fondo: Gradiente púrpura-azul (linear-gradient)
   * - Tipografía: Sistema del SO (apple-system, BlinkMacSystemFont, etc.)
   * - Animaciones: fadeIn para entrada, spin para spinner
   * - Sombras: text-shadow para logo
   * - Posicionamiento: absolute para versión, flex para centrado
   * 
   * JAVASCRIPT:
   * - Escucha mensajes del proceso principal para actualizar estado
   * - No tiene dependencias externas (todo inline)
   */

  const packageJson = require('./package.json');
  const appVersion = packageJson.version || '0.0.0';

  const splashHTML = `
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="UTF-8">
      <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
          display: flex;
          justify-content: center;
          align-items: center;
          height: 100vh;
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
          overflow: hidden;
        }
        .container {
          text-align: center;
          color: white;
          animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
          from { opacity: 0; transform: translateY(20px); }
          to { opacity: 1; transform: translateY(0); }
        }
        .logo {
          font-size: 48px;
          font-weight: 700;
          margin-bottom: 20px;
          text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .spinner {
          border: 4px solid rgba(255,255,255,0.3);
          border-top: 4px solid white;
          border-radius: 50%;
          width: 50px;
          height: 50px;
          animation: spin 1s linear infinite;
          margin: 30px auto;
        }
        @keyframes spin {
          0% { transform: rotate(0deg); }
          100% { transform: rotate(360deg); }
        }
        .status {
          font-size: 16px;
          opacity: 0.9;
          margin-top: 20px;
          font-weight: 300;
        }
        .version {
          position: absolute;
          bottom: 20px;
          width: 100%;
          text-align: center;
          font-size: 12px;
          opacity: 0.7;
        }
      </style>
    </head>
    <body>
      <div class="container">
        <div class="logo">Teejosh</div>
        <div style="font-size: 20px; font-weight: 300; margin-bottom: 10px;">
          Sistema de Inventario
        </div>
        <div class="spinner"></div>
        <div class="status" id="status">Iniciando servicios...</div>
      </div>
      <div class="version">v${appVersion}</div>
      <script>
        window.addEventListener('message', (event) => {
          if (event.data.type === 'loading-status') {
            document.getElementById('status').textContent = event.data.message;
          }
        });
      </script>
    </body>
    </html>
  `;

  // Cargar el HTML del splash screen (data URL)
  mainWindow.loadURL('data:text/html;charset=utf-8,' + encodeURIComponent(splashHTML));
  mainWindow.show(); // Mostrar ventana

  // Manejar errores de carga
  mainWindow.webContents.on('did-fail-load', (event, errorCode, errorDescription) => {
    log(`ERROR al cargar: ${errorCode} - ${errorDescription}`);
  });

  // Abrir DevTools automáticamente en desarrollo
  if (isDev) {
    mainWindow.webContents.openDevTools();
  }

  log("OK: Ventana mostrada");
}

// ============================================================================
// 10. FLUJO PRINCIPAL PARALELO DE INICIO
// ============================================================================
/**
 * Evento principal: Se ejecuta cuando Electron está listo
 * 
 * FLUJO OPTIMIZADO:
 * 1. Mostrar ventana INMEDIATAMENTE (splash screen)
 * 2. Verificar archivos (rápido)
 * 3. PostgreSQL primero (crítico), luego PHP
 * 4. Importar DB e iniciar PHP en PARALELO (más rápido)
 * 5. Cargar aplicación real cuando todo esté listo
 * 
 * OPTIMIZACIÓN: Uso de Promise.all() para ejecución paralela
 */
app.whenReady().then(async () => {
  // Encabezado de inicio
  log("==========================================");
  log("   INICIANDO TEEJOSH INVENTARIO");
  log("==========================================");
  log(`Modo: ${isDev ? 'DESARROLLO' : 'PRODUCCION'}`);
  log(`userData: ${app.getPath('userData')}`); // Datos persistentes del usuario
  log(`resources: ${rootPath()}`);             // Ruta a recursos
  log("==========================================");

  markPerf('app_start'); // Marca inicial para medir tiempo total

  try {
    // 1. MOSTRAR VENTANA INMEDIATAMENTE (Experiencia de usuario)
    createWindow();
    markPerf('window_created');

    // 2. VERIFICAR ARCHIVOS (Rápido, no bloqueante)
    verifyFiles();
    markPerf('files_verified');

    // 3. POSTGRESQL PRIMERO (Servicio más crítico)
    await ensurePostgresInitialized();
    markPerf('postgres_initialized');
    
    await startPostgres();
    markPerf('postgres_started');
    
    // 4. EJECUCIÓN PARALELA: PHP + IMPORTACIÓN DB (Optimización de tiempo)
    await Promise.all([
      // Importar base de datos (si es necesario)
      (async () => {
        if (!isDatabaseImported()) {
          await importDatabase();
          markPerf('database_imported');
        }
      })(),
      
      // Iniciar PHP en paralelo
      (async () => {
        startPHP();
        markPerf('php_started');
        await waitForPHP();
        markPerf('php_ready');
      })()
    ]);

    // 5. CARGAR APLICACIÓN REAL (Todo listo)
    log("Cargando aplicacion...");
    mainWindow.loadURL("http://127.0.0.1:8000"); // Cargar aplicación PHP
    markPerf('app_loaded');

    log("OK: Aplicacion iniciada correctamente");
    
    // Mostrar resumen de tiempos de inicio
    const totalTime = perfMarks['app_loaded'] - perfMarks['app_start'];
    log(`Tiempo total de inicio: ${totalTime}ms`);

  } catch (error) {
    // MANEJO DE ERRORES FATALES
    log("==========================================");
    log("ERROR FATAL AL INICIAR");
    log(`Error: ${error.message}`);
    log(`Stack: ${error.stack}`);
    log("==========================================");

    // Mostrar diálogo de error al usuario
    dialog.showErrorBox(
      'Error al iniciar la aplicacion',
      `No se pudo iniciar la aplicacion.\n\nError: ${error.message}\n\nRevisa el archivo de log en:\n${logFile}`
    );

    app.quit(); // Salir de la aplicación
  }
});

// ============================================================================
// 11. COMUNICACIÓN IPC CON EL RENDERER
// ============================================================================
/**
 * Maneja el evento 'app-ready' enviado desde el renderer (preload.js)
 * 
 * PROPÓSITO: La aplicación PHP (renderer) notifica cuando está completamente
 * cargada y lista. Podría usarse para ocultar loader o activar funcionalidades.
 */
ipcMain.on('app-ready', () => {
  log("OK: Aplicacion PHP reporto que esta lista");
});

// Exponer version de package.json al renderer (solo para la version)
ipcMain.handle('get-app-version', () => {
  try {
    const packageJson = require('./package.json');
    return packageJson.version;
  } catch (error) {
    log(`WARN: No se pudo leer version de package.json: ${error.message}`);
    return '0.0.0'; // Fallback
  }
});

// ============================================================================
// 12. CIERRE LIMPIO DE LA APLICACIÓN
// ============================================================================
/**
 * Evento: Cuando todas las ventanas están cerradas
 * 
 * FLUJO DE CIERRE:
 * 1. Detener PostgreSQL si estaba ejecutándose
 * 2. Detener servidor PHP
 * 3. Cerrar aplicación completamente
 * 
 * IMPORTANCIA: Previene procesos zombis y libera recursos del sistema
 */
app.on("window-all-closed", async () => {
  log("Cerrando aplicacion...");

  // Detener PostgreSQL si lo iniciamos
  if (postgresStarted) {
    const pgCtl = path.join(rootPath(), "postgres", "bin", "pg_ctl.exe");
    const dataDir = path.join(app.getPath('userData'), "pgdata");

    log("Deteniendo PostgreSQL...");
    
    try {
      // Parada rápida (-m fast) para cierre ágil
      await execCommand(pgCtl, ["-D", dataDir, "stop", "-m", "fast"]);
      log("OK: PostgreSQL detenido");
    } catch (error) {
      log(`WARN: Error al detener PostgreSQL: ${error.message}`);
    }
  }

  // Detener PHP si está ejecutándose
  if (phpServer) {
    log("Deteniendo PHP...");
    phpServer.kill(); // Enviar señal SIGTERM
    log("OK: PHP detenido");
  }

  log("Aplicacion cerrada");
  app.quit(); // Salir de la aplicación
});

// ============================================================================
// MANEJO DE ERRORES NO CAPTURADOS
// ============================================================================
// Capturar excepciones no manejadas (bugs críticos)
process.on('uncaughtException', (error) => {
  log(`UNCAUGHT EXCEPTION: ${error.message}`);
  log(error.stack);
});

// Capturar promesas rechazadas no manejadas
process.on('unhandledRejection', (reason, promise) => {
  log(`UNHANDLED REJECTION: ${reason}`);
});