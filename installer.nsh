; installer.nsh
;
; DESCRIPCIÓN_GENERAL:
; Script NSIS personalizado para el instalador del Proyecto. Permite agregar acciones
; personalizadas durante la instalación y desinstalación del software.
; Este archivo es incluido automáticamente por electron-builder durante la generación
; del instalador Windows (.exe).
;
; FUNCIONALIDADES_PRINCIPALES:
; - customInstall: Macro para acciones durante la instalación (copiar archivos adicionales,
;   crear accesos directos personalizados, modificar registros)
; - customUnInstall: Macro para acciones durante la desinstalación (eliminar archivos,
;   limpiar registros, eliminar accesos directos)
;
; DATOS_IMPORTANTES:
; - 📍 Ubicación: Debe estar en la raíz del proyecto para que electron-builder lo detecte
; - ⚙️  Compatibilidad: Solo funciona con electron-builder y NSIS (Windows)
; - 🛠️  Extensibilidad: Los macros pueden dejarse vacíos si no se necesitan acciones extras
; - 🔧 Variables disponibles: $INSTDIR (directorio de instalación), $DESKTOP, $STARTMENU
;
; RELACIONES:
; - Usado por: electron-builder durante la generación del instalador Windows
; - Utiliza: Comandos NSIS para operaciones del sistema de archivos y registro
;
; EJEMPLOS_DE_USO:
; ; Crear un acceso directo adicional en el escritorio
; CreateShortCut "$DESKTOP\MiApp.lnk" "$INSTDIR\mi_app.exe"
;
; ; Copiar archivo de configuración al AppData del usuario
; CopyFiles "$INSTDIR\config.ini" "$APPDATA\MiApp\config.ini"
;
; ; Eliminar archivos de logs durante desinstalación
; Delete "$APPDATA\MiApp\logs\*.log"
;
; NOTAS_CSS/HTML:
; - Este script no contiene CSS ni HTML
; - Las acciones definidas aquí afectan la instalación de la aplicación Electron
;   que incluye los archivos HTML/CSS/JS del frontend

; ============================================================================
; MACRO customInstall
; ============================================================================
; Se ejecuta DURANTE la instalación, después de copiar los archivos principales
; pero antes de finalizar el instalador.
!macro customInstall
  ; ============================================
  ; EJEMPLO: Crear acceso directo en escritorio
  ; ============================================
  ; Crea un acceso directo en el escritorio del usuario
  ; Sintaxis: CreateShortCut "ruta_destino.lnk" "ruta_ejecutable"
  ; CreateShortCut "$DESKTOP\Proyecto.lnk" "$INSTDIR\proyecto2electron.exe"
  
  ; ============================================
  ; EJEMPLO: Copiar archivos de configuración
  ; ============================================
  ; Copia archivos adicionales a la carpeta AppData del usuario
  ; AppData es el lugar recomendado para archivos de usuario (configuraciones, logs)
  ; Sintaxis: CopyFiles "origen" "destino"
  ; CopyFiles "$INSTDIR\resources\config.ini" "$APPDATA\Proyecto2\config.ini"
  
  ; ============================================
  ; EJEMPLO: Crear carpeta para logs
  ; ============================================
  ; Crea una carpeta para archivos de log en AppData
  ; CreateDirectory "$APPDATA\Proyecto2\logs"
  
  ; ============================================
  ; EJEMPLO: Agregar entrada al registro de Windows
  ; ============================================
  ; Para registrar la aplicación en "Agregar o quitar programas"
  ; WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\Proyecto2" "DisplayName" "Proyecto"
  ; WriteRegStr HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\Proyecto2" "UninstallString" "$INSTDIR\Uninstall.exe"
  
  ; ============================================
  ; MENSAJE DE DEPURACIÓN (solo durante desarrollo)
  ; ============================================
  ; MessageBox MB_OK "customInstall ejecutado - Proyecto"
!macroend

; ============================================================================
; MACRO customUnInstall
; ============================================================================
; Se ejecuta DURANTE la desinstalación, después de eliminar los archivos principales
; pero antes de finalizar el desinstalador.
!macro customUnInstall
  ; ============================================
  ; EJEMPLO: Eliminar acceso directo del escritorio
  ; ============================================
  ; Elimina el acceso directo creado durante la instalación
  ; Sintaxis: Delete "ruta_archivo"
  ; Delete "$DESKTOP\Proyecto.lnk"
  
  ; ============================================
  ; EJEMPLO: Eliminar archivos de configuración
  ; ============================================
  ; Elimina archivos de configuración y logs del usuario
  ; Sintaxis: Delete "ruta_archivo" o RMDir /r "ruta_carpeta"
  ; Delete "$APPDATA\Proyecto2\config.ini"
  ; RMDir /r "$APPDATA\Proyecto2\logs"
  
  ; ============================================
  ; EJEMPLO: Eliminar carpeta de AppData si está vacía
  ; ============================================
  ; RMDir "$APPDATA\Proyecto2"  ; Solo elimina si está vacía
  
  ; ============================================
  ; EJEMPLO: Eliminar entrada del registro
  ; ============================================
  ; DeleteRegKey HKLM "Software\Microsoft\Windows\CurrentVersion\Uninstall\Proyecto2"
  
  ; ============================================
  ; MENSAJE DE DEPURACIÓN (solo durante desarrollo)
  ; ============================================
  ; MessageBox MB_OK "customUnInstall ejecutado - Proyecto"
!macroend