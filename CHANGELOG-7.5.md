# Change Log for OXID eShop Community Edition Core Component

## v7.5.1 - 2026-05-11

### Added
- `ClearShopCacheEvent`, dispatched when the shop cache is cleared, allowing subscribers to invalidate additional caches

### Fixed
- Clear template chain cache when clearing shop cache

## v7.5.0 - 2026-04-08

### Added
- API entry points with Symfony RouterListener for route handling
- API rate limiter
- HTML sanitization interface for filtering unsafe elements and attributes
- Alternative email transport integration
- `ProductSearchServiceInterface` for custom product search implementations
- `ThemeActivatedEvent` dispatched on theme activation
- PHP v8.5 support
- PHPUnit v12.5 support
- `Id::fromString()` method for creating IDs from strings

### Changed
- Filter unsafe HTML elements and attributes from CMS content in the admin area
- `Config::getConfigParam()` no longer triggers full shop initialization
- Avoid repeated forced basket recalculations in `BasketComponent::render()`
- Remove MD5-style hash validation in `Id::fromUid()`
- Cache `FilesystemModuleCache` entries in memory to avoid repeated filesystem reads
- Cache `getModuleIds()` result in `ActiveModulesDataProvider`
- Cache `getEdition()` and `getCacheDirectory()` results in `BasicContext`

### Deprecated
- `Id::fromUid()` — use `Id::fromString()` instead

### Fixed
- Model extension chain bypass [#0007881](https://bugs.oxid-esales.com/view.php?id=7881)

### Removed
- PHP v8.2 support
- PHPUnit v11 support
