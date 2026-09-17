# Change Log for OXID eShop Community Edition Core Component

## v7.7.0 - Unreleased

### Added
- Community and project migrations are registered as tagged `oxid_esales.migration_path_provider` services (`CommunityEditionMigrationPathProvider`, `ProjectMigrationPathProvider`)

### Changed
- `MigrationExecutor` no longer uses `oxid-esales/oxideshop-doctrine-migration-wrapper` internally
- `Utilities::createMigrations()` throws a `LogicException`

### Fixed

### Removed
- `oxid-esales/oxideshop-doctrine-migration-wrapper` composer dependency