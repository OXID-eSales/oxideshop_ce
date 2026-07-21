# Change Log for OXID eShop Community Edition Core Component

## v7.6.0 - Unreleased

### Added

### Changed
- Console commands return consistent exit codes on success (0) and failure (1)
- Default bcrypt password hashing cost was increased [#0007727](https://bugs.oxid-esales.com/view.php?id=7727)
- Remote address resolution now uses Symfony `Request::getClientIp()` — proxy headers are no longer trusted by default, configure trusted IPs via `oxid_esales.request.trusted_proxies` DI parameter

### Deprecated
- `Argon2IPasswordHashService`
- `Config::$_aThemeConfigParams`
- `Config::isThemeOption()`
- `ThemeSettingChangedEvent`
- `ModuleActivateCommand::MESSAGE_MODULE_ACTIVATED`
- `ModuleActivateCommand::MESSAGE_MODULE_NOT_FOUND`
- `ModuleDeactivateCommand::MESSAGE_MODULE_DEACTIVATED`
- `ModuleDeactivateCommand::MESSAGE_MODULE_NOT_FOUND`
- `Theme`
- `UtilsServer::getRemoteAddress()`
- `ViewConfig::getRemoteAddress()`
- `ViewConfig::getViewThemeParam()`
- `ThemeMain::themeInConfigFile()`
- `ShopAdapterInterface::getActiveThemesList()`
- `ShopAdapterInterface::getCustomTheme()`
- `ShopAdapterInterface::getActiveThemeId()`
- `ShopAdapterInterface::themeExists()`
- `ShopAdapterInterface::activateTheme()`
- `SettingsHandler`
- `Config::OXMODULE_THEME_PREFIX`
- `ThemeConfiguration::saveConfVars()`

### Fixed
- Cascade delete now removes all related records when deleting a user by ID [#0007138](https://bugs.oxid-esales.com/view.php?id=7138)
- Case-sensitive column name handling in admin order article search [#0006025](https://bugs.oxid-esales.com/view.php?id=6025)

### Removed