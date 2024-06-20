# Change Log for OXID eShop Community Edition Core Component

## v7.2.0 - unreleased

### Added
- 

### Deprecated
- Filesystem module cache related services and interface will be refactored and some of them will be removed
- Config parameters sShopDir, sCompileDir, sLogLevel and iDebug will be moved from config file to env parameters
- Global functions `warningHandler(), dumpVar(), debug()`
- `ConnectionProviderInterface` will be superseded by `ConnectionFactory` in next major
- Redundant interfaces `TransactionServiceInterface, FinderFactoryInterface`

### Fixed
- User registration in the Private Sales mode

### Changed
-  

### Removed
- Obsolete demo data and images from the `source/out` directory
