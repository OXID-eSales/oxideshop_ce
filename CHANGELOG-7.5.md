# Change Log for OXID eShop Community Edition Core Component

## v7.5.0 - Unreleased

### Added
- New `fromString()` method in `Id` class to create IDs from strings without validation
- New interface for filtering unsafe HTML elements and attributes
- API entry points functionality with Symfony RouterListener for route handling
- API rate limiter
- Alternative email transport integration

### Changed
- Filter unsafe HTML elements and attributes from CMS content in the admin area
- Config::getConfigParam() no longer triggers full shop initialization
- No MD5-style hash validation in the `Id::fromUid()` method

### Deprecated
- Method `Id::fromUid()` use `Id::fromString()` instead
