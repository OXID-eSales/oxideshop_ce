# Change Log for OXID eShop Community Edition Core Component

## v7.3.0 - unreleased

### Added
- PHPUnit v11 support
- Category detail page codeception test

### Fixed
- Shop ID resolution considers SSL language URLs

### Changed
- Raised minimum required version of Symfony components to 6.4

### Removed
- PHPUnit v10 support

### Changed
- Set the default value of blSkipDebitOldBankInfo to true

### Deprecated
- Config parameters from [config.inc.php](https://docs.oxid-esales.com/developer/en/latest/development/modules_components_themes/project/configincphp.html) will be moved to env or container parameters
- Global functions `warningHandler(), dumpVar(), debug()`
- `ConnectionProviderInterface` will be superseded by `ConnectionFactory` in next version
- Redundant interfaces `TransactionServiceInterface, FinderFactoryInterface`
- `BasicContextInterface` methods
- The `getSslShopUrl` method will be deprecated and replaced by `getShopUrl`, which will now support SSL exclusively
- The use of SSL parameter in `getAltImageUrl` method of PictureHandler will be deleted in next major version
- Method `isEnabledAdminQueryLog()` of ContextInterface
- `handleDatabaseException` functionality
