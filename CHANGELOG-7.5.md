# Change Log for OXID eShop Community Edition Core Component

## v7.5.0 - Unreleased

### Added
- New `fromString()` method in `Id` class to create IDs from strings without validation
- New interface for filtering unsafe HTML elements and attributes
- API entry points functionality with Symfony RouterListener for route handling
- API rate limiter
- Alternative email transport integration
- PHP v8.5 support
- PHPUnit v12.5 support
- `ThemeActivatedEvent` dispatched on theme activation

### Changed
- Filter unsafe HTML elements and attributes from CMS content in the admin area
- Config::getConfigParam() no longer triggers full shop initialization
- No MD5-style hash validation in the `Id::fromUid()` method
- Avoid repeated forced basket recalculations in `BasketComponent::render()`
- Cache `getModuleIds()` result in `ActiveModulesDataProvider`
- Cache `getEdition()` and `getCacheDirectory()` results in `BasicContext`
- Cache `FilesystemModuleCache` entries in memory to avoid repeated filesystem reads

### Deprecated
- Method `Id::fromUid()` use `Id::fromString()` instead

### Fixed
- Model extension chain bypass [#0007881](https://bugs.oxid-esales.com/view.php?id=7881)

### Removed
- PHP v8.2 support
- PHPUnit v11 support
