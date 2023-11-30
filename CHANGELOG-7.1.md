# Change Log for OXID eShop Community Edition Core Component

## v7.1.0 - unreleased

### Added
- Class `ContainerFacade` and method `Base::getService()` for quick access to the DI Container from the non-DI areas,
- Command `bin/oe-console oe:theme:activate <theme>` to activate a theme from CLI
- PHP v8.2 support
- Shops can have their own service configuration
- Dependencies between modules feature is introduced.

### Deprecated
- Deprecate console classes from the Internal namespace: `Executor`, `ExecutorInterface`, `CommandsProvider`, `CommandsProviderInterface`
- Private Sales Invite functionality is outdated.
- `getContainer()` and `dispatchEvent()` methods in Core classes
- Global function \makeReadable()

### Fixed
- Wrong property "_oUserData" used in ContactController [PR-918](https://github.com/OXID-eSales/oxideshop_ce/pull/918)

### Changed
-  Executing `oe-console` command with an invalid `shop-id` value will be interrupted

### Removed
- PHP v8.0 support
