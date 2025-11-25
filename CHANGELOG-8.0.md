# Change Log for OXID eShop Community Edition Core Component

## v8.0.0-alpha.2 - Unreleased

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

### Removed

- Remove `DateFormatHelper`
- Deprecated promotions
- Fetch mode support from `DatabaseInterface` and `DatabaseProvider`
- Database `NullLogger` and `DatabaseLoggerFactoryInterface`
- Redundant Logger classes and interfaces: `MonologConfigurationInterface`, `PsrLoggerConfigurationInterface`,
  `LoggerConfigurationValidatorInterface`, `LoggerWrapper`
- Redundant shop state and configuration classes and services: `ShopStateService`, `ShopStateServiceInterface`, `ProjectConfigurationDao`, `ProjectConfigurationDaoInterface`, `ProjectConfiguration`
- A deprecated partner-related method `getBelboonParam()` of class `BaseController` [0006140](https://bugs.oxid-esales.com/view.php?id=6140)
- `ContainerBuilderFactory`

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
