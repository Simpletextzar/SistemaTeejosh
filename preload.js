/**
 * preload.js
 * 
 * DESCRIPCIÓN_GENERAL:
 * Script de preload para Electron en el Proyecto. Actúa como puente seguro entre el
 * proceso principal (main) y el proceso de renderizado (web). Expone una API controlada
 * que permite a la ventana web interactuar con el sistema de manera segura sin exponer
 * APIs sensibles de Node.js/Electron directamente.
 * 
 * FUNCIONALIDADES_PRINCIPALES:
 * - Expone funciones seguras de comunicación IPC (Inter-Process Communication)
 * - Proporciona métodos para notificar estado de la aplicación PHP
 * - Permite escuchar actualizaciones de estado desde el proceso principal
 * - Ofrece información de la aplicación y utilidades para desarrollo
 * - Implementa patrón de seguridad contextBridge para aislar contextos
 * 
 * DATOS_IMPORTANTES:
 * - SEGURIDAD: Solo expone funciones específicas, NUNCA todo ipcRenderer directamente
 * - DESARROLLO: Las funciones de desarrollo deben estar protegidas en producción
 * - UBICACIÓN: Debe referenciarse en main.js en la configuración de BrowserWindow
 * - RENDIMIENTO: Se ejecuta antes que cualquier script de la página web
 * 
 * RELACIONES:
 * - Usado por: La ventana de Electron (renderer process) para comunicarse con main process
 * - Utiliza: Módulos de Electron (contextBridge, ipcRenderer)
 * - Referenciado en: main.js en new BrowserWindow({ webPreferences: { preload: ... } })
 * 
 * EJEMPLOS_DE_USO:
 * // En el renderer (HTML/JS de la ventana):
 * // Notificar que la app está lista
 * window.electronAPI.appReady();
 * 
 * // Escuchar actualizaciones de estado
 * window.electronAPI.onLoadingStatus((message) => {
 *   document.getElementById('status').innerText = message;
 * });
 * 
 * // Obtener versión de la app
 * const version = window.electronAPI.getAppVersion();
 * console.log('Versión:', version);
 * 
 * NOTAS_CSS/HTML:
 * - Este script no modifica CSS/HTML directamente
 * - La API expuesta puede ser usada por scripts en las páginas HTML para actualizar
 *   la interfaz de usuario (ej: mostrar/ocultar loader, actualizar estado)
 * - Es común usar esta API para inyectar estilos/scripts condicionales
 */

// ============================================================================
// IMPORTACIÓN DE MÓDULOS DE ELECTRON
// ============================================================================
const { contextBridge, ipcRenderer } = require('electron');

// ============================================================================
// CONFIGURACIÓN DE API SEGURA CON CONTEXTBRIDGE
// ============================================================================
// contextBridge.exposeInMainWorld expone una API en el objeto global "window"
// Solo las funciones expuestas aquí serán accesibles desde el renderer (web)
contextBridge.exposeInMainWorld('electronAPI', {
  
  // --------------------------------------------------------------------------
  // COMUNICACIÓN DE ESTADO DE LA APLICACIÓN
  // --------------------------------------------------------------------------
  
  /**
   * Notifica al proceso principal que la aplicación PHP está lista.
   * Se llama cuando el servidor PHP ha iniciado completamente y la página
   * web principal se ha cargado. Normalmente desencadena la ocultación del
   * loader/splash screen.
   * 
   * @example
   * // Llamar cuando la página web esté completamente cargada
   * window.addEventListener('DOMContentLoaded', () => {
   *   window.electronAPI.appReady();
   * });
   */
  appReady: () => {
    ipcRenderer.send('app-ready');
  },
  
  /**
   * Escucha actualizaciones de estado desde el proceso principal.
   * El proceso principal envía mensajes de progreso (ej: "Iniciando PHP...",
   * "Conectando a base de datos...") que pueden mostrarse en la interfaz.
   * 
   * @param {Function} callback - Función que recibe el mensaje de estado
   * 
   * @example
   * window.electronAPI.onLoadingStatus((message) => {
   *   // Actualizar elemento HTML con el mensaje
   *   document.getElementById('loading-status').innerText = message;
   * });
   */
  onLoadingStatus: (callback) => {
    // Escuchar el evento 'loading-status' desde el main process
    ipcRenderer.on('loading-status', (event, message) => {
      callback(message);
    });
  },
  
  // --------------------------------------------------------------------------
  // INFORMACIÓN DE LA APLICACIÓN
  // --------------------------------------------------------------------------
  
  /**
   * Obtiene la versión actual de la aplicación.
   * Normalmente se lee desde package.json. Útil para mostrar en "Acerca de..."
   * o para verificar compatibilidad.
   * 
   * @returns {string} Versión de la aplicación
   * 
   * @example
   * const version = window.electronAPI.getAppVersion();
   * document.title = `Mi App v${version}`;
   *
   * getAppVersion: () => {
   *  // En una implementación real, esto podría leer de package.json
   *  // o recibir la versión desde el main process
   *  return '1.0.0'; // Versión hardcodeada - idealmente se obtiene dinámicamente
   * },
   */

  /**
   * Obtener version de la aplicacion desde package.json
   * Uso: const version = await window.electronAPI.getAppVersion()
   * @returns {Promise<string>} Version en formato semver (ej: "1.0.0")
   */
  getAppVersion: async () => {
    try {
      const version = await ipcRenderer.invoke('get-app-version');
      return version;
    } catch (error) {
      console.error('Error al obtener version:', error);
      return '1.0.0'; // Fallback
    }
  },
  
  /**
   * Obtiene información detallada de la aplicación.
   * Retorna un objeto con nombre, versión, entorno y otras métricas.
   * 
   * @returns {Promise<Object>} Información de la app
   */
  getAppInfo: () => {
    return ipcRenderer.invoke('get-app-info');
  },
  
  // --------------------------------------------------------------------------
  // UTILIDADES DE DESARROLLO (SOLO EN DESARROLLO)
  // --------------------------------------------------------------------------
  
  /**
   * Abre las herramientas de desarrollo (DevTools).
   * Solo debe estar disponible en entorno de desarrollo.
   * En producción, esta función podría no exponerse o estar deshabilitada.
   * 
   * @example
   * // Atajo de teclado para abrir DevTools en desarrollo
   * document.addEventListener('keydown', (e) => {
   *   if (e.ctrlKey && e.shiftKey && e.key === 'I') {
   *     window.electronAPI.openDevTools();
   *   }
   * });
   */
  openDevTools: () => {
    ipcRenderer.send('open-devtools');
  },
  
  /**
   * Recarga la ventana actual (similar a F5 en navegador).
   * Útil durante desarrollo para ver cambios sin reiniciar la app.
   */
  reloadWindow: () => {
    ipcRenderer.send('reload-window');
  },
  
  // --------------------------------------------------------------------------
  // GESTIÓN DE LA APLICACIÓN
  // --------------------------------------------------------------------------
  
  /**
   * Reinicia la aplicación completamente.
   * Útil para aplicar configuraciones que requieren reinicio o recuperarse
   * de errores críticos.
   */
  restartApp: () => {
    ipcRenderer.send('restart-app');
  },
  
  /**
   * Cierra la aplicación.
   * Permite un cierre controlado desde la interfaz web.
   */
  closeApp: () => {
    ipcRenderer.send('close-app');
  },
  
  // --------------------------------------------------------------------------
  // LOGGING Y DIAGNÓSTICO
  // --------------------------------------------------------------------------
  
  /**
   * Envía un mensaje de log al proceso principal.
   * Los logs se pueden escribir en archivo o mostrar en consola.
   * 
   * @param {string} level - Nivel de log ('info', 'warn', 'error', 'debug')
   * @param {string} message - Mensaje a loguear
   * 
   * @example
   * window.electronAPI.sendLog('info', 'Usuario hizo clic en botón X');
   * window.electronAPI.sendLog('error', 'Falló la conexión a la BD');
   */
  sendLog: (level, message) => {
    ipcRenderer.send('log-message', { level, message });
  },
  
  // --------------------------------------------------------------------------
  // COMUNICACIÓN CON BASE DE DATOS (SI APLICA)
  // --------------------------------------------------------------------------
  
  /**
   * Ejecuta una consulta en la base de datos PostgreSQL.
   * Nota: Esta es una implementación de ejemplo. En producción, preferiblemente
   * las consultas se harían a través de la API PHP, no directamente desde Electron.
   * 
   * @param {string} query - Consulta SQL a ejecutar
   * @returns {Promise<Object>} Resultado de la consulta
   */
  queryDatabase: (query) => {
    return ipcRenderer.invoke('query-database', query);
  },
  
  /**
   * Verifica el estado de la base de datos.
   * Retorna información sobre conexión, versión y estadísticas.
   * 
   * @returns {Promise<Object>} Estado de la base de datos
   */
  getDatabaseStatus: () => {
    return ipcRenderer.invoke('get-database-status');
  }
});

// ============================================================================
// CONFIRMACIÓN DE CARGA
// ============================================================================
// Este mensaje aparece en la consola del proceso de preload (no en la web)
// Útil para verificar que el script se cargó correctamente
console.log('Preload script cargado - Proyecto Teejosh Inventario');
console.log('API expuesta en window.electronAPI');

// ============================================================================
// SEGURIDAD ADICIONAL
// ============================================================================
// Eliminar referencias a módulos sensibles para evitar que el renderer los acceda
// Esto es una medida de seguridad adicional
process.once('loaded', () => {
  // Limpiar referencias globales si es necesario
  // delete global.require;
  // delete global.process;
});