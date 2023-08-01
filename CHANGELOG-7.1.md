# Change Log for OXID eShop Community Edition Core Component

## v7.1.0 - unreleased

### Added
- Class `ContainerFacade` and method `Base::getService()` for quick access to the DI Container from the non-DI areas,
- PHP v8.2 support

### Deprecated
- Deprecate console classes from the Internal namespace: `Executor`, `ExecutorInterface`, `CommandsProvider`, `CommandsProviderInterface`
- Private Sales Invite functionality is outdated.
- `getContainer()` and `dispatchEvent()` methods in Core classes

### Removed
- PHP v8.0 support
