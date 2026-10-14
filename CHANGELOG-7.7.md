# Change Log for OXID eShop Community Edition Core Component

## v7.7.0 - Unreleased

### Added
- PHPUnit v13 support
- Community and project migrations are registered as tagged `oxid_esales.migration_path_provider` services (`CommunityEditionMigrationPathProvider`, `ProjectMigrationPathProvider`)
- `MigrationsNotFoundException`

### Changed
- `MigrationExecutor` no longer uses `oxid-esales/oxideshop-doctrine-migration-wrapper` internally
- `Utilities::createMigrations()` throws a `LogicException`

### Deprecated
- `Utilities::createMigrations()`

### Fixed
- Password reset links are invalidated when the user's email address or password is changed

### Removed
- PHP v8.3 support
- PHPUnit v12 support
- `oxid-esales/oxideshop-doctrine-migration-wrapper` composer dependency