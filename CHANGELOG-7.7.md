# Change Log for OXID eShop Community Edition Core Component

## v7.7.0 - Unreleased

### Added
- Edition and project migrations as tagged `oxid_esales.migration_path_provider` services

### Changed
- `MigrationExecutor` constructor requires a `ConfigurableMigrationExecutorInterface`
- `Utilities::executeExternalDatabaseMigrationCommand()` and `Utilities::createMigrations()` throw exceptions instead of using the wrapper

### Fixed

### Removed
- `oxid-esales/oxideshop-doctrine-migration-wrapper` package and `oe-eshop-db_migrate` script
