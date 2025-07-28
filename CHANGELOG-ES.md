# Changelog

## [2.0.2] - 2025-07-28

### Fixed
- Corregido el tipo nullable explícito del parámetro `$previous` en múltiples clases de excepción para evitar advertencias deprecadas en PHP 8.1 y superior.

## [2.0.1] - 2025-06-04

### Fixed
- Corregidas las claves del array de conexión en `Bootstrap.php` (de `'dulce_auth_driver'` a `'driver'`, etc.) para compatibilidad con Eloquent.
- Corregida la sección de *Archivo config* y *Archivo configuración de la base de datos* del archivo `README.md`.

## [2.0.0] - 2025-06-04

### Changed

- Renombradas todas las constantes globales para incluir el prefijo `DULCE_AUTH_`, con el fin de evitar colisiones en entornos compartidos.

Ejemplos:
  - `BASE_DIR` → `DULCE_AUTH_BASE_DIR`
  - `DRIVER` → `DULCE_AUTH_DRIVER`
  - `CONFIG_PATH` → `DULCE_AUTH_CONFIG_PATH`
  - ...

### Removed

- Eliminadas las constantes globales sin prefijo (`BASE_DIR`, `DRIVER`, etc.), lo cual rompe compatibilidad con versiones anteriores.

## [1.0.0] - 2025-04-06

### Added

- Reinicio del proyecto con estructura limpia.
- Código reorganizado desde cero con las mejoras realizadas hasta ahora.




