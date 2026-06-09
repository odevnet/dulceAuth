# Changelog

## [2.1.0] - 2026-06-09

### Added
- Implementation of two-step verification (OTP via email).
  - New methods in `Auth.php`: `generateOtpCode()`, `verifyOtp()`, `resendOtp()`.
  - New method in `DulceMail.php`: `sendOtpEmail()` and `sendOtpTemplate()`.
  - New configuration file `otp_email.json` to customize the content of the OTP email and the on-screen message.
  - New configuration constants in `config.php` to enable/disable email 2FA and define OTP expiration.

## [2.0.2] - 2025-07-28

### Fixed
- Fixed explicit nullable type of `$previous` parameter in multiple exception classes to avoid deprecated warnings in PHP 8.1 and above.

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

