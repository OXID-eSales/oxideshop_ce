# Change Log for OXID eShop Community Edition Core Component

## v7.1.0 - unreleased

### Added
- Class `ContainerFacade` and method `Base::getService()` for quick access to the DI Container from the non-DI areas,
- PHP v8.2 support

### Deprecated
- Deprecate console classes from the Internal namespace: `Executor`, `ExecutorInterface`, `CommandsProvider`, `CommandsProviderInterface`
- Private Sales Invite functionality is outdated.
- `getContainer()` and `dispatchEvent()` methods in Core classes

### Fixed
- Inactive articles are shown in the detail page under some conditions [PR-911](https://github.com/OXID-eSales/oxideshop_ce/pull/911) [#0007476](https://bugs.oxid-esales.com/view.php?id=7476)

### Removed
- PHP v8.0 support
