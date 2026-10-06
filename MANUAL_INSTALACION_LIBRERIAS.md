# Manual de Instalación de Librerías de Exportación

Este manual describe cómo instalar las librerías necesarias para la funcionalidad de exportación (CSV, Excel, PDF) en el sistema SACS Nuevo usando Composer.

## Requisitos Previos

- PHP 8.2 o superior
- XAMPP instalado en Windows
- Composer instalado en el sistema
- Acceso a internet para descargar las librerías

## Paso 1: Instalar Composer

Si no tienes Composer instalado, sigue estos pasos:

1. Descargar Composer desde: https://getcomposer.org/download/
2. Ejecutar el instalador de Composer-Setup.exe
3. Seleccionar la ruta de PHP de XAMPP (usualmente: `C:\xampp\php\php.exe`)
4. Completar la instalación

Verificar la instalación:
```powershell
composer --version
```

Deberías ver algo como:
```
Composer version 2.x.x
```

## Paso 2: Instalar Librerías con Composer

1. Abrir una terminal o PowerShell
2. Navegar al directorio del proyecto:
   ```powershell
   cd C:\xampp\htdocs\sacs_nuevo
   ```

3. Ejecutar el comando de instalación:
   ```powershell
   composer install
   ```

Este comando instalará:
- **phpoffice/phpspreadsheet** - Para generación de archivos Excel (XLSX)
- **tecnickcom/tcpdf** - Para generación de archivos PDF

4. Esperar a que se complete la instalación. Composer descargará las librerías y sus dependencias automáticamente.

## Paso 3: Verificar Estructura de Directorios

Después de la instalación, se creará el directorio `vendor/` con la siguiente estructura:

```
C:\xampp\htdocs\sacs_nuevo\
├── config/
├── public/
├── src/
├── vendor/
│   ├── composer/
│   ├── phpoffice/
│   │   └── phpspreadsheet/
│   ├── tecnickcom/
│   │   └── tcpdf/
│   └── autoload.php
└── views/
```

## Paso 4: Configurar Autoload en Bootstrap

El archivo `composer.json` ya está configurado con el autoload PSR-4. Verificar que el archivo `config/bootstrap.php` incluya el autoload de Composer:

```php
require __DIR__ . '/../vendor/autoload.php';
```

Esta línea debe agregarse al inicio del archivo `config/bootstrap.php`.

## Paso 5: Verificar Instalación

Para verificar que las librerías están correctamente instaladas:

1. Crear un archivo de prueba `test_librerias.php` en la raíz del proyecto:
   ```php
   <?php
   require 'config/bootstrap.php';
   
   // Verificar PhpSpreadsheet
   if (class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
       echo "✓ PhpSpreadsheet instalado correctamente\n";
   } else {
       echo "✗ PhpSpreadsheet no encontrado\n";
   }
   
   // Verificar TCPDF
   if (class_exists('TCPDF')) {
       echo "✓ TCPDF instalado correctamente\n";
   } else {
       echo "✗ TCPDF no encontrado\n";
   }
   ?>
   ```

2. Ejecutar el archivo desde el navegador:
   ```
   http://localhost/sacs_nuevo/test_librerias.php
   ```

Deberías ver:
```
✓ PhpSpreadsheet instalado correctamente
✓ TCPDF instalado correctamente
```

## Solución de Problemas Comunes

### Problema: "composer: command not found"

**Solución:**
- Verificar que Composer esté instalado correctamente
- Agregar Composer al PATH del sistema
- Reiniciar la terminal después de la instalación

### Problema: "Your lock file does not contain up-to-date information"

**Solución:**
```powershell
composer update
```

### Problema: "Class 'PhpOffice\PhpSpreadsheet\Spreadsheet' not found"

**Solución:**
- Verificar que el directorio `vendor/` existe
- Verificar que el archivo `vendor/autoload.php` existe
- Verificar que la línea `require __DIR__ . '/../vendor/autoload.php';` esté en `config/bootstrap.php`

### Problema: "Class 'TCPDF' not found"

**Solución:**
- Ejecutar `composer install` nuevamente
- Verificar que `vendor/tecnickcom/tcpdf/` existe
- Verificar que el autoload esté configurado correctamente

### Problema: Permisos en Windows

**Solución:**
- Ejecutar PowerShell como Administrador
- Verificar que tienes permisos de escritura en el directorio del proyecto

## Notas Importantes

- **Composer** es el gestor de dependencias estándar de PHP
- Las librerías instaladas son modernas y bien mantenidas
- **PhpSpreadsheet** reemplaza a las librerías antiguas de Excel
- **TCPDF** es una librería moderna para generación de PDF
- El archivo `composer.lock` garantiza que todos los desarrolladores usen las mismas versiones
- Las librerías se instalan en `vendor/` y no deben ser modificadas manualmente

## Actualización a Producción

Para actualizar a producción:

1. **Opción A: Copiar vendor/ (No recomendada)**
   - Copiar la carpeta `vendor/` completa al servidor de producción
   - Riesgo: archivos binarios pueden no ser compatibles entre sistemas

2. **Opción B: Ejecutar composer install en producción (Recomendada)**
   - Copiar `composer.json` y `composer.lock` al servidor
   - Ejecutar `composer install` en el servidor de producción
   - Esto garantiza compatibilidad

3. **Opción C: Usar composer install --no-dev**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

## Comandos Útiles de Composer

```powershell
# Instalar dependencias
composer install

# Actualizar dependencias
composer update

# Verificar dependencias instaladas
composer show

# Limpiar caché de Composer
composer clear-cache

# Verificar que composer.json sea válido
composer validate
```

## Soporte

Si encuentras algún problema durante la instalación:

1. Revisa este manual
2. Verifica que Composer esté instalado
3. Ejecuta `composer diagnose` para diagnosticar problemas
4. Revisa el archivo `composer.json`
5. Contacta al equipo de desarrollo

---

**Última actualización:** 2026-10-06
