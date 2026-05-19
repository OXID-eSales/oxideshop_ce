# Change Log for OXID eShop Community Edition Core Component

## v7.6.0 - Unreleased

### Added

### Changed
- Console commands return consistent exit codes on success (0) and failure (1)
- Default bcrypt password hashing cost was increased [#0007727](https://bugs.oxid-esales.com/view.php?id=7727)
- Remote address resolution now uses Symfony `Request::getClientIp()` — proxy headers are no longer trusted by default, configure trusted IPs via `oxid_esales.request.trusted_proxies` DI parameter

### Deprecated
- `Argon2IPasswordHashService`
- `ModuleActivateCommand::MESSAGE_MODULE_ACTIVATED`
- `ModuleActivateCommand::MESSAGE_MODULE_NOT_FOUND`
- `ModuleDeactivateCommand::MESSAGE_MODULE_DEACTIVATED`
- `ModuleDeactivateCommand::MESSAGE_MODULE_NOT_FOUND`
- `UtilsServer::getRemoteAddress()`
- `ViewConfig::getRemoteAddress()`

### Fixed

### Removed