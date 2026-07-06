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
- Support for subscribing to Symfony kernel events (`KernelEvents`)
- Security headers on shop responses (DI parameter `oxid_esales.http.security_headers`)
- Default `Cache-Control` policy for responses without an explicit one (DI parameter `oxid_esales.http.default_cache_control`)

### Fixed
- Undefined array key warning in `ExceptionErrorController::displayExceptionError()` when no errors are queued for display

### Changed
- `RandomTokenGenerator` enforces a minimum token length of eight characters
- HTTP header and request handling on the shop kernel
  - `index.php` resolves Symfony `#[Route]` attribute controllers (compiled from public services); requests without a matching route fall back to the legacy shop controller resolution
  - The response status, headers and cookies are owned by the Symfony `Response`; cookies set during the request (`UtilsServer::setOxCookie()`) are applied to the outgoing response through a `kernel.response` listener
  - A `ResponseReady` signal thrown from a kernel event listener is delivered as the response instead of surfacing as an uncaught error
  - `Symfony\Component\HttpFoundation\RequestStack` is available as a public container service; the kernel pushes the current request onto it
  - Admin authorization failure inside a redirect loop responds with 403 instead of exiting with a message body
  - Product picture upload errors (`ArticlePicturesAjax`) return a single well-formed JSON response instead of emitting two responses
  - Uncaught shop exceptions (`SystemComponentException`, `RoutingException`, `StandardException`) are converted to responses by the `ShopExceptionResponseListener` on the `kernel.exception` event instead of inside `ShopControl`
  - Request-terminating legacy helpers (`Utils::redirect()`, `Utils::showMessageAndExit()`, the 404 handler, file downloads) dispatch a `ResponseReadyEvent`; a framework subscriber ends the request and the kernel proceeds with the carried response - subscribe to the event to observe or replace the outgoing response
  - An uncaught `StandardException` outside debug mode re-renders the requested controller with the error queued for display (`UtilsView::addErrorToDisplay()`) instead of responding with an empty 500; `ThemeMain` and `ThemeConfiguration` rely on this instead of catching
  - Shop exceptions and response signals thrown by template-embedded widgets propagate to the HTTP kernel: a redirect or 404 raised inside a widget applies to the whole page again (as in OXID eShop 7) and a failing widget results in the central shop error handling instead of an inline debug block
  - The `Cache-Control` header is owned by the `Response`; PHP's session cache limiter is disabled

### Removed
- `Output` - process output with a `kernel.response` listener
- `Header` - set headers on the `Response` or use a `kernel.response` listener
- `DebugInfo` and the debug-mode monitor block appended to storefront pages – use a profiler
- `BeforeHeadersSendEvent` - subscribe to the `kernel.response` event instead
- The `Argon2IPasswordHashService` and its configuration have been removed
- Remove deprecated constant `Database::MYSQL_ATTR_INIT_COMMAND`
- PHP v8.3 support
- `ModuleActivateCommand::MESSAGE_MODULE_ACTIVATED`
- `ModuleActivateCommand::MESSAGE_MODULE_NOT_FOUND`
- `ModuleDeactivateCommand::MESSAGE_MODULE_DEACTIVATED`
- `ModuleDeactivateCommand::MESSAGE_MODULE_NOT_FOUND`
- `UtilsServer::getRemoteAddress()`
- `ViewConfig::getRemoteAddress()`
- `Utils::setHeader()` - set headers on the `Response` or subscribe to the `kernel.response` event
- `Oxid` entry-point class (`Oxid::run()`, `Oxid::runWidget()`), `ShopControl::start()` and `WidgetControl::start()` - the entry scripts run through the HTTP kernel; use `ShopControl::buildResponse()` / `WidgetControl::buildWidgetResponse()`
- `ShopControl` internals superseded by the kernel migration 
- `File::getFilenameForUrl()`

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
