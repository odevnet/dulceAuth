# Changelog

## [2.0.1] - 2025-06-04

### Fixed
- Fixed connection array keys in `Bootstrap.php` (from `'dulce_auth_driver'` to `'driver'`, etc.) for Eloquent compatibility.
- Fixed *Config file* and *Database configuration file* section of `README.md` file.

## [2.0.0] - 2025-06-04

### Changed

- Renamed all global constants to include the `DULCE_AUTH_` prefix to avoid collisions in shared environments.

Examples:
  - `BASE_DIR` → `DULCE_AUTH_BASE_DIR`
  - `DRIVER` → `DULCE_AUTH_DRIVER`
  - `CONFIG_PATH` → `DULCE_AUTH_CONFIG_PATH`
  - ...

### Removed

- Removed global constants without prefix (`BASE_DIR`, `DRIVER`, etc.), which breaks backward compatibility.

## [1.0.0] - 2025-04-06

### Added

- Project reboot with a clean structure.
- Code reorganized from scratch, incorporating all improvements made so far.

