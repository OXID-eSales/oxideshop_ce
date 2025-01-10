# Change Log for OXID eShop Community Edition Core Component

## v7.3.0 - unreleased

### Added
- PHPUnit v11 support
- Category detail page codeception test
- Registration of environment variables via .env file
- Controllers can be registered as DI services

### Fixed
- Shop ID resolution considers SSL language URLs
- Email existence check when changing from user to guest email [#0006860](https://bugs.oxid-esales.com/view.php?id=6860)

### Changed
- Raised minimum required version of Symfony components to 6.4
- Set the default value of blSkipDebitOldBankInfo to true
- Add to basket does not force a refresh of order confirmation step [#0007254](https://bugs.oxid-esales.com/view.php?id=7254)

### Removed
- PHPUnit v10 support

### Deprecated
- Config parameters from [config.inc.php](https://docs.oxid-esales.com/developer/en/latest/development/modules_components_themes/project/configincphp.html) will be moved to env variables and container parameters
- Global functions `warningHandler(), dumpVar(), debug()`
- `ConnectionProviderInterface` will be superseded by `ConnectionFactory` in next version
- Redundant interfaces `TransactionServiceInterface, FinderFactoryInterface`
- `BasicContextInterface` methods
- The `getSslShopUrl` method will be deprecated and replaced by `getShopUrl`, which will now support SSL exclusively
- The use of SSL parameter in `getAltImageUrl` method of PictureHandler will be deleted in next major version
- Method `isEnabledAdminQueryLog()` of ContextInterface
- `handleDatabaseException` functionality
- `FileCache` and `SubShopSpecificFileCache` classes 
- Related Methods for managing cache files in Utils will be deleted in next major version
- Parameter `includePermanentCache` of the method `Utils:oxResetFileCache()`
- The interface `CacheConnectorInterface`
- The helper `DateFormatHelper` will be removed in next major version
