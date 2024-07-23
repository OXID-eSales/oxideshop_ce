# Change Log for OXID eShop Community Edition Core Component

## v7.2.0 - unreleased

### Added
- 

### Deprecated
- Filesystem module cache related services and interface will be refactored and some of them will be removed
- Config parameters `sShopDir`, `sCompileDir`, `sLogLevel`, `iDebug`, `sShopURL`, `sSSLShopURL` and `sAdminSSLURL` will be moved from config file to env parameters
- Global functions `warningHandler(), dumpVar(), debug()`
- `ConnectionProviderInterface` will be superseded by `ConnectionFactory` in next major
- Redundant interfaces `TransactionServiceInterface, FinderFactoryInterface`
- `BasicContextInterface` methods
- The `getSslShopUrl` method will be deprecated and replaced by `getShopUrl`, which will now support SSL exclusively
- The configFile for a setup will be replaced by container parameters
- The use of ssl parameter in `getAltImageUrl` method of PictureHandler will be deleted in next major version

### Fixed
- User registration in the Private Sales mode
- New item in basket message display [#0007548](https://bugs.oxid-esales.com/view.php?id=7548) [PR-964](https://github.com/OXID-eSales/oxideshop_ce/pull/964)

### Changed
-  

### Removed
- Obsolete demo data and images from the `source/out` directory
