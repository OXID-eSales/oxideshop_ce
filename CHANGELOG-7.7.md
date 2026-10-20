# Change Log for OXID eShop Community Edition Core Component

## v7.7.0 - Unreleased

### Added
- PHPUnit v13 support
- Community and project migrations are registered as tagged `oxid_esales.migration_path_provider` services (`CommunityEditionMigrationPathProvider`, `ProjectMigrationPathProvider`)
- `MigrationsNotFoundException`

### Changed
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
- Infinite recursion when the `ArticleBox` or `Review` widget is called directly without a parent view [#0005484](https://bugs.oxid-esales.com/view.php?id=5484)

### Removed
- PHP v8.3 support
- PHPUnit v12 support
- `oxid-esales/oxideshop-doctrine-migration-wrapper` composer dependency