# Changelog
## [3.0.0] -

### Changed
- Se han cambiado algunos nombres de constantes:
    - JSON_FILE_VERIFICATION_EMAIL ahora se llama **VERIFICATION_EMAIL_JSON_FILE**.
    - VERIFICATION_PAGE ahora se llama **VERIFICATION_PAGE_URL**.
    - JSON_FILE_FORGOT_PASSWORD_EMAIL ahora se llama **FORGOT_PASSWORD_EMAIL_JSON_FILE**.
    - FORGOT_PASSWORD_PAGE ahora se llama **FORGOT_PASSWORD_PAGE_URL**.

- Se ha modificado el archivo config.
- La configuración de la base de datos ahora se establece por defecto mediante constantes.
- Se ha modificado la clase Configuration completamente.
- Se ha modificado la clase DulceMail. Concretamente el método sendEmail().

### Added
- Se ha añadido un nuevo tipo de clave llamada "type" para los archivos *verification_email.json* y *forgot_password_email.json*.
- Se han agregado tres métodos nuevos llamados 'load()', 'loadedFiles()' y 'all()' a la clase 'Configuration'.
- Se han añadido dos constantes nuevas (CUSTOM_VERIFICATION_EMAIL_URL y CUSTOM_FORGOT_PASSWORD_EMAIL_URL) por si queremos personalizar la url de los emails de verification y contraseña olvidada.
- Se ha agregado un pequeño script de instalación que crea automaticamente los siguientes archivos: *config.php*, *config-db.php*,
*verification_email.json*, *forgot_password_email.json* y *log_file.log*.

### Removed
- Se ha eliminado el constructor de la clase 'Configuration'.



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
