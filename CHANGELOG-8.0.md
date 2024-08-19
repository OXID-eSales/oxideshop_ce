# Change Log for OXID eShop Community Edition Core Component

## v8.0.0 - unreleased

### Added

- Set custom product low stock label [#0004401](https://bugs.oxid-esales.com/view.php?id=4401)
- Support PSR caching interface, related functionalities and applied them on module cache.
- Registration of environment variables via Symfony Dotenv Component
- Bootstrap parameters have been moved from config.inc.php to environment and container parameters
  - Container parameters
    - `oxid_debug_mode`
    - `oxid_smtp_debug_mode`
    - `oxid_multilingual_tables`
    - `oxid_skip_database_views_usage`
    - `oxid_multi_shop_article_fields`
    - `oxid_show_update_views_button`
    - `oxid_shop_url`
    - `oxid_shop_admin_url`
    - `oxid_multi_shop_tables`
- Interface for storing Symfony Service Container parameters in configuration

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
- `oe:setup:shop` command now fetches parameters from the current environment configuration. All corresponding command-line parameters were removed
- Updated list of Search Engines (formerly `aRobots` configuration)

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
- Remove deprecated `BasicContextInterface::getCurrentShopId` and its basic implementation in `BasicContext::getCurrentShopId`
- Remove deprecated model property `Attribute::_sTitle` [PR-914](https://github.com/OXID-eSales/oxideshop_ce/pull/914)
- Obsolete caching related functionalities
- Methods in deprecated `Database` and `DatabaseProvider`, related to configuration management
- Redundant interfaces `TransactionServiceInterface`, `FinderFactoryInterface`
- `ConnectionProviderInterface::get()` was superseded by `ConnectionFactory::create()`
- Deprecated global functions `warningHandler(), dumpVar(), debug()`
- Config parameters 
  - `sShopDir`
  - `sCompileDir`
  - `sLogLevel`
  - `iDebug`
  - `aMultishopArticleFields`
  - `aMultiLangTables`
  - `blShowUpdateViews`
  - `blSkipViewUsage`
  - `sShopURL`
  - `sSSLShopURL`
  - `sAdminSSLURL`
  - `edition`
  - `blDebugTemplateBlocks`
  - `aRobotsExcept`
  - `deactivateSmartyForCmsContent`
  - `iSmartyPhpHandling`
  - `blDoNotDisableModuleOnError`
  - `passwordHashingAlgorithm`
  - `passwordHashingBcryptCost`
  - `passwordHashingArgon2MemoryCost`
  - `passwordHashingArgon2TimeCost`
  - `passwordHashingArgon2Threads`
  - `sAuthOpenIdRandSource`
  - `aSlaveHosts`
  - `iDebugSlowQueryTime`
- The `ConfigFile` will no longer be used. Please use container parameters to obtain the necessary settings
- Deprecated class `ModuleVariablesLocator`
- Redundant `BasicContextInterface` methods
- related configuration parameter method `isTplBlocksDebugMode` of `ViewConfig` class
- Deprecated `NamedArgumentsTrait`
- Deprecated `isEnabledAdminQueryLog` method in ContextInterface. Query logging mode can be fetched directly from container.
- Deprecated `handleDatabaseException` functionality
