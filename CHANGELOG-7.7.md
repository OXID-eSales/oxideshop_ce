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
- Legacy request, output and header handling
  - `Oxid`, `ShopControl::start()` and `WidgetControl::start()`
  - `ShopControl::formOutput()`, override `ShopControl::render()` instead
  - `ShopControl::getControllerClass()`, use `ShopControl::resolveControllerClass()` instead
  - `ShopControl`: `handleSystemException()`, `handleRoutingException()`, `handleCookieException()`, `handleBaseException()`, `logException()`, `sendAdditionalHeaders()`, `getOutputManager()`, `runOnce()`, `getTotalTime()`, `startMonitor()`, `stopMonitoring()` and `formMonitorMessage()`
  - `ShopControl`: `$_blMainTasksExecuted`, `$_dTimeStart`, `$_dTimeEnd`, `$_aErrors`, `$_aAllErrors`, `$_aControllerErrors` and `$_oOutput`; `WidgetControl::$_blMainTasksExecuted`
  - `ExceptionErrorController` and the controller key `exceptionerror`
  - `Config::processSeoCall()`, use `SeoDecoder::processSeoCall()` instead
  - `Config`: `handleDbConnectionException()`, `handleCookieException()` and `getExceptionHandler()`
  - `Output`, `Header` and `DebugInfo`
  - `Utils`: `setHeader()`, `showMessageAndExit()`, `showOfflinePage()`, `prepareToExit()` and `addUrlParameters()`
  - `Utils::handlePageNotFoundError()` and `error_404_handler()`, throw a `RoutingException` instead
  - `Utils::simpleRedirect()`, use `Utils::redirect()` instead
  - `oxTriggerOfflinePageDisplay()`
  - `UtilsView::getTemplateOutput()`, use `TemplateRendererBridgeInterface` instead
  - `Session::isHeaderSent()` and `Session::needToSetHeaders()`
  - `Email::makeOutputProcessing()` and `Email::processViewArray()`
  - `DiagnosticsOutput::downloadResultFile()` and `File::getFilenameForUrl()`
  - `BeforeHeadersSendEvent` and `ApplicationExitEvent`
  - `HeaderGeneratorBridgeInterface`, `CsvHeaderGeneratorBridge`, `HeaderGeneratorInterface` and `CsvHeaderGenerator`
  - Inclusion of `modules/functions.php`
  - Language constants `ERROR_MESSAGE_OXID_SHOP_ERROR` and `MESSAGE_PLEASE_DELETE_FOLLOWING_DIRECTORY`
- `Internal\Framework\Api`: `Api`, `ExceptionHandler`, `HttpExceptionListener` and `AttributeRouteControllerLoader`
- API rate limiter
  - `ApiRateLimiterFactoryInterface`, `ApiRateLimiterFactory`, `ClientIdentifierProviderInterface`, `ClientIdentifierProvider`, `ApiRateLimitListener` and `RateLimitHeadersListener`
  - The DI parameters `oxid_esales.rate_limiter.enabled`, `oxid_esales.rate_limiter.limit`, `oxid_esales.rate_limiter.interval`, `oxid_esales.rate_limiter.policy` and `oxid_esales.rate_limiter.excluded_routes`
- `TooManyRequestsException`, `RateLimitResponderInterface` and `RateLimitResponder`

### Fixed
- Password reset links are invalidated when the user's email address or password is changed
- `oe:database:migrate --write-sql` keeps the SQL of all migration sets instead of only the last one [#0006869](https://bugs.oxid-esales.com/view.php?id=6869)
- `class_exists()` checks for unknown classes no longer cause a circular service reference while the DI container is being built
- Product SEO url context is kept when the SEO settings are saved in the admin area, existing entries need to be saved again [#0007952](https://bugs.oxid-esales.com/view.php?id=7952)

### Removed
- PHP v8.3 support
- PHPUnit v12 support
- `oxid-esales/oxideshop-doctrine-migration-wrapper` composer dependency