# Change Log for OXID eShop Community Edition Core Component

## v8.0.0-alpha.3 - Unreleased
*Compilation release*

### Added
- Locale management
  - Admin page to create, edit, delete and assign locales per shop
  - Configurable global fallback locale (DI parameter `oxid_esales.locale.default_fallback`)
  - `ContextInterface::getCurrentLanguageAbbreviation()`
- Alt text for product images
  - Admin page to edit alt text per image per locale
  - Storefront resolves the alt text through the locale's fallback chain
- `ProductMediaChangedEvent`, `ProductMediaSortedEvent`, `MediaAttributeChangedEvent` and `LocaleChangedEvent`
- YAML-based theme configuration
  - Theme packages provide `metadata.yaml` and `config.yaml`
  - Theme settings are installed per shop
  - Settings can be overridden per environment via `var/configuration.<OXID_ENV>/shops/<shop-id>/themes/<theme-id>.yaml`
  - `ThemeSettingServiceInterface`, `ActiveThemeProviderInterface`, `ThemePathProviderInterface` and `ThemeViewServiceInterface` to read theme settings, the active theme and theme data
  - `ViewConfig::getThemeSettings()` to read theme settings in templates
  - `ThemeConfigurationChangedEvent`
- `ShopRunnerInterface`, `FileResponseFactoryInterface`, `BeforeEmailSendEvent`, the `RequestStack` service and the `#[Cache]` attribute on `#[Route]` controllers
- `RedirectException`, `AccessDeniedException` and `ShopOfflineException` to end a request with a redirect, `403` or `503`, and `NotFoundController` rendering the 404 page
- DI parameters `oxid_esales.http.exceptions` (status code and log level per exception), `oxid_esales.http.security_headers`, `oxid_esales.http.default_cache_control` and `oxid_esales.http.charset`

### Changed
- `RandomTokenGenerator` enforces a minimum token length of eight characters
- Hardened resolution of generated image paths in `GeneratedImagePathProvider`
- Module migrations are no longer discovered automatically; register them via a `MigrationPathProviderInterface` service tagged `oxid_esales.migration_path_provider`
- Theme settings and the active theme are stored in YAML files instead of the database
  - Theme settings are read from and written to YAML instead of the `oxconfig` table
  - Standard theme settings were renamed to remove Hungarian notation, e.g. `bl_showWishlist` is now `showWishlist`
  - The active theme is configured via the per-shop DI parameter `oxid_esales.theme.active` in `var/configuration/shops/<shop-id>/parameters.yaml`
  - The `sTheme` and `sCustomTheme` config parameters are no longer used
  - A child theme declares its parent theme in its `metadata.yaml`
  - `Config::getConfigParam()` no longer returns theme settings, use `ThemeSettingServiceInterface` instead
- `ThemeActivatedEvent` moved from namespace `Internal\Framework\Theme\Event` to `Internal\Framework\Theme\Activation\Event`
- Requests are handled by the Symfony HttpKernel
  - `index.php`, `widget.php`, `admin/oxajax.php` and `api.php` run through `ShopRunnerInterface` and dispatch the kernel events
  - `#[Route]` controllers are matched for the storefront `index.php`, SEO URLs and `/api/`
  - Controllers can return a `Response`; `ViewControllerInterface` declares `hasResponse()` and `getResponse()`
  - Status, headers and cookies, also from `UtilsServer::setOxCookie()`, are set on the `Response`; cookies of HTTPS requests are `Secure`
  - `Utils::redirect()` throws a `RedirectException`
  - Errors are rendered by Symfony's `ErrorListener`: in the storefront as the 404 or offline page, for JSON requests as problem details
  - Without debug mode, failing widgets are logged and left out of the page
- Status codes and headers
  - `404` for unknown pages, controllers and widgets, `503` for the offline shop, `400` for an unhandled `FileTypeNotAllowedException`
  - Security headers on all responses; responses without an own cache policy get `Cache-Control: no-store, no-cache, must-revalidate, private`
  - `Pragma` and `Expires` are no longer sent
- Methods that sent output return a `Response`
  - `ShopControl::process()`, `ListComponentAjax::processRequest()`, `AdminNewsletter::export()` and `VoucherSerieExport::download()`
  - `DiagnosticsMain::downloadResultFile()`, `NavigationController::exturl()` and `File::download()`
  - `ArticlePicturesAjax::addMedia()` and `ArticlePicturesAjax::replaceMedia()`
- `GenericImport::importFile()` and `CategoryList::updateCategoryTree()` no longer print
- `UtilsFile::processFiles()` throws `FileTypeNotAllowedException` for forbidden file types instead of exiting; admin controllers show it as a form error
- Overridable `ShopControl` methods declare types: `getStartControllerKey(): string`, `resolveControllerClass(string $controllerKey): string`, `executeMaintenanceTasks(): void`, `isDebugMode(): bool` and `getErrors(): array`
- The storefront rate limiter is enabled by default and also limits the API with the default rule `api` instead of `global`
- `StorefrontRateLimiterInterface::enforce()` and `RuleProviderInterface::getMatchingRules()` take the `Request`

### Deprecated
- `ProjectMigrationPathProvider`
- The `$blAddRedirectParam` parameter of `Utils::redirect()`
- `api.php`, route `/api/` to `index.php` instead as the shipped `.htaccess` does

### Removed
- `OXURL` from the storefront-updatable user fields `UserUpdatableFields::getUpdatableFields()`
- The backwards-compatibility autoloader for legacy class names (e.g. `oxarticle`, `oxorder`)
  - `BaseModel::getClassName()` and `BaseModel::$_sClassName`
  - `UtilsObject::getClassAliasName()`
  - `Registry::getStorageKey()`
  - `BasicContextInterface::getBackwardsCompatibilityClassMap()`
  - The `$classAlias` parameter of `ModuleChainsGenerator::createClassChain()`
- The `Argon2IPasswordHashService` and its configuration have been removed
- Remove deprecated constant `Database::MYSQL_ATTR_INIT_COMMAND`
- PHP v8.3 support
- `ModuleActivateCommand::MESSAGE_MODULE_ACTIVATED`
- `ModuleActivateCommand::MESSAGE_MODULE_NOT_FOUND`
- `ModuleDeactivateCommand::MESSAGE_MODULE_DEACTIVATED`
- `ModuleDeactivateCommand::MESSAGE_MODULE_NOT_FOUND`
- `UtilsServer::getRemoteAddress()`
- `ViewConfig::getRemoteAddress()`
- `SeoEncoderArticle::createArticleCategoryUri()`
- deprecated `ProjectYamlImportServiceInterface::addImport()` and `ProjectYamlImportServiceInterface::removeImport()`
- `MigrationExecutorInterface`
- `AdminThemeBridgeInterface`, use the DI parameter `oxid_esales.theme.admin.name` instead
- `ThemeSettingChangedEvent`, use `ThemeConfigurationChangedEvent` instead
- Theme configuration classes and methods
  - `Theme` and `SettingsHandler`
  - `ThemeSettingDaoInterface` and `ThemeSetting`
  - `ThemeConfiguration::saveConfVars()`, `ThemeConfiguration::$_sTheme` and `ThemeMain::themeInConfigFile()`
  - `Config::isThemeOption()`, `Config::$_aThemeConfigParams` and `Config::OXMODULE_THEME_PREFIX`
  - `ViewConfig::getViewThemeParam()`, use `ViewConfig::getThemeSettings()` instead
  - `Language::getCustomThemeLanguageFiles()`
  - `ShopAdapterInterface`: `getActiveThemesList()`, `getCustomTheme()`, `getActiveThemeId()`, `themeExists()` and `activateTheme()`
- `Oxid`, `ShopControl::start()` and `WidgetControl::start()`, use `ShopRunnerInterface`, `ShopControl::handleRequest()`, `ShopControl::buildResponse()` and `WidgetControl::buildIncludedWidgetResponse()` instead
- `Output`, `Header`, `Utils::setHeader()`, `Utils::showMessageAndExit()`, `BeforeHeadersSendEvent` and `ApplicationExitEvent`, use the `Response` or the `kernel.response` event instead
- `Utils::simpleRedirect()`, `Utils::showOfflinePage()`, `Utils::handlePageNotFoundError()`, `oxTriggerOfflinePageDisplay()` and `error_404_handler()`, use `Utils::redirect()` or throw a `ShopOfflineException` or `RoutingException` instead
- `ShopControl::formOutput()`, use `ShopControl::render()` instead
- `ShopControl::getControllerClass()`, use `ShopControl::resolveControllerClass()` instead
- `ShopControl::sendAdditionalHeaders()`, `handleSystemException()`, `handleRoutingException()`, `handleCookieException()`, `handleBaseException()` and `logException()`, use kernel event listeners or `oxid_esales.http.exceptions` instead
- `UtilsView::getTemplateOutput()`, use `TemplateRendererBridgeInterface` instead
- `Email::makeOutputProcessing()`, use `BeforeEmailSendEvent` instead
- `Config::processSeoCall()`, use `SeoDecoder::processSeoCall()` instead
- `DiagnosticsOutput::downloadResultFile()`, use `DiagnosticsOutput::getResultFileResponse()` instead
- `HeaderGeneratorBridgeInterface` and `HeaderGeneratorInterface`, use `FileResponseFactoryInterface` instead
- `DebugInfo`, `ExceptionErrorController`, `ShopControl::getTotalTime()`, `Session::isHeaderSent()` and protected output, header, exception and monitoring members of `ShopControl`, `WidgetControl`, `Utils`, `Config`, `Session`, `Email` and `File`
- `ShopControl::$_aErrors`, `$_aAllErrors`, `$_aControllerErrors` and `$_blMainTasksExecuted`, use `$errors`, `$remainingErrors`, `$errorControllers` and `$skipMaintenanceTasks` instead
- Inclusion of `modules/functions.php`, use Composer's `autoload.files` instead
- `FilesystemTagAwareAdapterFactory`, the shop cache uses `PhpFilesAdapter` with a generation per shop instead
- The API rate limiter and its DI parameters `oxid_esales.rate_limiter.{enabled,limit,interval,policy,excluded_routes}`, use a storefront rule with `format: json` instead
- `TooManyRequestsException` and `RateLimitResponderInterface`, use Symfony's `TooManyRequestsHttpException` instead

### Fixed
- Unknown download links (`cl=download`) redirect to the account downloads page with an error message instead of failing with `500`

## v8.0.0-alpha.2 - 2026-02-12
*Compilation release*

### Added

- ClearShopCacheEvent
- Validation for country VAT number prefix in the admin area
- Upgrade to Symfony version 7.3

### Fixed

- Creating a new country does not check the VAT-ID prefix [#0007205](https://bugs.oxid-esales.com/view.php?id=7205)
- Use raw SQL in migrations instead of Doctrine's schema tools to keep doctrine/dbal v4.3.0 installs working
- Admin cookie handling to avoid explode type errors on non-string values
- Restrict shutdown logging to fatal errors
- Read `OXID_DEBUG_MODE` from dotenv before shutdown logging

### Changed

- Product pictures functionality has been redesigned
  - Unlimited product images (no longer limited to 12)
  - Images stored in separate tables (removed from `oxarticles`)
  - New reusable media handling infrastructure (`Internal\Domain\Media`)
  - New product media services (`Internal\Domain\Product\Media`)
  - Redesigned Admin UI for image management
- Module environment configuration file paths
- Updated doctrine/dbal dependency to ^4.2
- `ResultSet` constructor now strictly requires `$statement` to be a `Doctrine\DBAL\Statement`
- Refactored database query logging configuration.
- Database adapter fetcher methods now return associative results by default
- Method `getLastInsertId` of `DatabaseInterface` Adapter will throw `DatabaseErrorException` if no insert ID is available.
- The `DatabaseConfiguration` namespace has been renamed for consistency, and the `getScheme` method has been replaced with `getDriver`
- Shop setup proceeds with an empty database
- The method `prepareModulesInformation` now returns module data as arrays instead of stdClass objects
- `SetupDbValidatorInterface` no longer throws `DatabaseNotEmptyException`; database checks are now handled by a separate validator.
- `database_schema.sql` no longer removes existing `oxmigrations` tables during shop setup. The schema dump should only be applied to an empty database.

### Deprecated

- `RequestInterface` - use `Symfony\Component\HttpFoundation\Request` instead

### Removed

- Deprecated helper class `DateFormatHelper`
- Deprecated promotions
- Fetch mode support from `DatabaseInterface` and `DatabaseProvider`
- Redundant Logger classes and interfaces: `MonologConfigurationInterface`, `PsrLoggerConfigurationInterface`,
  `LoggerConfigurationValidatorInterface`, `LoggerWrapper`, `NullLogger` and `DatabaseLoggerFactoryInterface`
- Redundant shop state and configuration classes and services: `ShopStateServiceInterface`, `ProjectConfigurationDaoInterface`, `ProjectConfiguration`
- A deprecated partner-related method `getBelboonParam()` of class `BaseController` [0006140](https://bugs.oxid-esales.com/view.php?id=6140)
- `ContainerBuilderFactory` class
- Media refactoring — replaced with MediaView-based access
  - `Article`: `getThumbnailUrl()`, `getIconUrl()`, `getPictureUrl()`, `getMasterZoomPictureUrl()`, `getZoomPictureUrl()`, `getPictureFieldValue()`, `getMasterPicturePath()`, `getPicturesProduct()`, `getMasterZoomPicture()`, `getZoomMedia()`
  - `ArticleDetails`: `getActPicture()`, `morePics()`, `getIcons()`, `showZoomPics()`, `getZoomPics()`
  - `ArticleDetailsController`: `getActPictureId()`, `showZoomPics()`
- `RequestAdapter`  - use `Symfony\Component\HttpFoundation\Request` instead
- Deprecated method `Id::fromUid()`

## v8.0.0-alpha.1 - 2025-02-03

### Added

- Set custom product low stock label [#0004401](https://bugs.oxid-esales.com/view.php?id=4401)
- Support PSR caching interface, related functionalities and applied them on module cache.
- Registration of environment variables via Symfony Dotenv Component
- Interface for storing Symfony Service Container parameters in configuration
- Support Symfony caching interface with tags

### Deprecated

- `Utils` methods for managing cache will be replaced by using Symfony cache directly

### Changed

- Configuration parameters have been moved from `config.inc.php` to environment and container parameters
- Admin directory is not removed from the url in `ViewConfig::getModuleUrl`
  anymore [PR-817](https://github.com/OXID-eSales/oxideshop_ce/pull/817)
- Reset created product "sold" counter during Copying of the
  product [PR-913](https://github.com/OXID-eSales/oxideshop_ce/pull/913)
- `ModuleConfigurationValidatorInterface` is not optional anymore in the module activation service.
- The visibility of time-activated products has changed, products with an undefined end date appear in the shop for an
  unlimited period of time
- Functionality to extend Symfony DIC for environments and shops
- Method `getAltImageUrl` of PictureHandler will not use ssl parameter anymore
- `oe:setup:shop` command now fetches parameters from the current environment configuration.
  All corresponding command-line parameters were removed
- Updated list of Search Engines (formerly `aRobots` configuration)
- Browser-based application setup was discontinued. Only console-based setup is available
- Replace file caching in `Utils` with Symfony cache
- Removed $includePermanentCache parameter from `oxResetFileCache` method, all cache files are now cleared without exclusions.

### Removed

- Remove console classes from the Internal
  namespace: `Executor`, `ExecutorInterface`, `CommandsProvider`, `CommandsProviderInterface`
- Cleanup deprecated Private Sales Invite functionality
- `getContainer()` and `dispatchEvent()` methods from Core classes
- Remove deprecated global function \makeReadable()
- Redundant `TemplateFileResolverInterface` functionality
- Smarty templates support
- `PAYMENT_INFO_OFF`
  translation [#0006426](https://bugs.oxid-esales.com/view.php?id=6426) [PR-953](https://github.com/OXID-eSales/oxideshop_ce/pull/953)
- Remove deprecated `TemplateCacheService` implementation
- Remove deprecated `BasicContextInterface::getCurrentShopId` and its basic implementation in
  `BasicContext::getCurrentShopId`
- Remove deprecated model property `Attribute::_sTitle` [PR-914](https://github.com/OXID-eSales/oxideshop_ce/pull/914)
- Obsolete caching related functionalities
- Methods in deprecated `Database` and `DatabaseProvider`, related to configuration management
- Redundant interfaces `TransactionServiceInterface`, `FinderFactoryInterface`
- `ConnectionProviderInterface::get()` was superseded by `ConnectionFactory::create()`
- Deprecated global functions `warningHandler(), dumpVar(), debug()`
- Superseded and obsolete `config.inc.php` parameters
- Obsolete `ConfigFile` class and functionality for `config.inc.php` management
- Deprecated class `ModuleVariablesLocator`
- Redundant `BasicContextInterface` methods
- Related configuration parameter method `isTplBlocksDebugMode` of `ViewConfig` class
- Deprecated `NamedArgumentsTrait`
- Deprecated `isEnabledAdminQueryLog` method in ContextInterface. Query logging mode can be fetched directly from
  container.
- Deprecated `handleDatabaseException` functionality
- Dependency on `oxideshop-facts` component
- `FileCache` and `SubShopSpecificFileCache` classes. Use `ContextInterface::getCurrentShopId()` instead
- Legacy file-based caching methods from `Utils` class
- Remove deprecated Interface `CacheConnectorInterface`
