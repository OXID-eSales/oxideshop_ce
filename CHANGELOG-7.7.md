# Change Log for OXID eShop Community Edition Core Component

## v7.7.0 - Unreleased

### Added
- PHPUnit v13 support
- Community and project migrations are registered as tagged `oxid_esales.migration_path_provider` services (`CommunityEditionMigrationPathProvider`, `ProjectMigrationPathProvider`)
- `MigrationsNotFoundException`
- `MigrationSqlOutputException`

### Changed
- `oe:database:migrate --write-sql` fails before running any migration when the target file cannot be written
- `MigrationExecutor` no longer uses `oxid-esales/oxideshop-doctrine-migration-wrapper` internally
- `Utilities::createMigrations()` throws a `LogicException`

### Deprecated
- `Utilities::createMigrations()`
- Theme configuration classes and methods
  - `Theme` and `SettingsHandler`
  - `ThemeConfiguration::saveConfVars()`, `ThemeConfiguration::$_sTheme` and `ThemeMain::themeInConfigFile()`
  - `Config::isThemeOption()`, `Config::$_aThemeConfigParams` and `Config::OXMODULE_THEME_PREFIX`
  - `ViewConfig::getViewThemeParam()`
  - `Language::getCustomThemeLanguageFiles()`
  - `ShopAdapterInterface`: `getActiveThemesList()`, `getCustomTheme()`, `getActiveThemeId()`, `themeExists()` and `activateTheme()`
- `ThemeSettingChangedEvent`
- `AdminThemeBridgeInterface`, use the DI parameter `oxid_esales.theme.admin.name` instead
- `Internal\Framework\Theme\Event\ThemeActivatedEvent`

### Fixed
- Password reset links are invalidated when the user's email address or password is changed
- `--write-sql` output being overwritten when multiple migration sets exist

### Removed
- PHP v8.3 support
- PHPUnit v12 support
- `oxid-esales/oxideshop-doctrine-migration-wrapper` composer dependency