# Changelog
## [3.0.0] -

### Changed
- Some constant names have been changed:
    - JSON_FILE_VERIFICATION_EMAIL is now called **VERIFICATION_EMAIL_JSON_FILE**.
    - VERIFICATION_PAGE is now called **VERIFICATION_PAGE_URL**.
    - JSON_FILE_FORGOT_PASSWORD_EMAIL is now called **FORGOT_PASSWORD_EMAIL_JSON_FILE**.
    - FORGOT_PASSWORD_PAGE is now called **FORGOT_PASSWORD_PAGE_URL**.

- The config file has been modified.
- The database configuration is now set by default using constants.
- The Configuration class has been completely modified.
- The DulceMail class has been modified, specifically the sendEmail() method.

### Added
- A new key type called "type" has been added for the *verification_email.json* and *forgot_password_email.json* files.
- Three new methods called 'load()', 'loadedFiles()' and 'all()' have been added to the 'Configuration' class.
- Two new constants have been added (CUSTOM_VERIFICATION_EMAIL_URL and CUSTOM_FORGOT_PASSWORD_EMAIL_URL) in case we want to customize the url of verification and forgotten password emails.
- A small installation script has been added that automatically creates the following files: *config.php*, *config-db.php*,
*verification_email.json*, *forgot_password_email.json* and *log_file.log*.

### Removed
- The constructor for the 'Configuration' class has been removed.



## [2.0.0] - 2024-10-29
### Added
- This changelog has been added.
- Bootstrap is now a class.
- A new class called **Configuration** has been added which allows loading a configuration file and its values.
- The library is now installed via Composer.

### Changed
- The EXCEPTION_LOG constant is now called LOG_FILE and its default path is now /logs/log_file.log
- The 'Bootstrap' has been modified and is now a class.
- The class structure has been modified and changed a bit. For example, **DulceAuth.php** is now in the *src* namespace.
- You can create the **config.php** file wherever you want, but the recommendation is to create a directory called */config* and put it there, along with the JSON files *verification_email.json* and *forgot_password_email.json*.

### Removed
- CONFIG_PATH, BOOTSTRAP_PATH and CLASS_PATH constants have been removed.

## [1.0.0] - 2024-08-19
### Added
- Initial version of the project.
