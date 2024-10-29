# Changelog

## [2.0.0] - 2024-10-29
### Added
- Se ha añadido este changelog.
- Bootstrap ahora es una clase.
- Se ha añadido una nueva clase llamada **Configuration** que permite cargar un archivo de configuración y sus valores.
- La libreria ahora se instala a través de Composer.

### Changed
- La constante EXCEPTION_LOG ahora se llama LOG_FILE y su ruta por defecto ahora es /logs/log_file.log
- Se ha modificado el 'Bootstrap' y ahora es una clase.
- Se ha modificado y cambiado un poco la estructura de clases. Por ejemplo, ahora **DulceAuth.php** pasa a estar en el namespace *src*.
- El archivo **config.php** lo puedes crear donde quieras, pero la recomendación es crear un directorio llamado */config* y meterlo ahi, junto a los archivos JSON *verification_email.json* y *forgot_password_email.json*.

### Removed
- Se han eliminado las constantes CONFIG_PATH, BOOTSTRAP_PATH y CLASE_PATH.

## [1.0.0] - 2024-08-19
### Added
- Versión inicial del proyecto.
