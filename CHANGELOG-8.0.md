# Change Log for OXID eShop Community Edition Core Component

## v8.0.0 - unreleased

### Changed
- Admin directory is not removed from the url in `ViewConfig::getModuleUrl` anymore [PR-817](https://github.com/OXID-eSales/oxideshop_ce/pull/817)
- Reset created product "sold" counter during Copying of the product [PR-913](https://github.com/OXID-eSales/oxideshop_ce/pull/913)

### Removed
- Remove console classes from the Internal namespace: `Executor`, `ExecutorInterface`, `CommandsProvider`, `CommandsProviderInterface`
- Cleanup deprecated Private Sales Invite functionality
- `getContainer()` and `dispatchEvent()` methods from Core classes
