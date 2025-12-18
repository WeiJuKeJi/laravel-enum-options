# Changelog

All notable changes to `laravel-enum-options` will be documented in this file.

## [Unreleased]

## [1.1.0] - 2025-01-18

### Added
- **EnumRegistry Service**: Automatic enum discovery and registration system
  - Auto-discover preset enums from `src/Presets` directory
  - Auto-discover app enums from `app/Enums` directory
  - Support for multi-level category directories
  - Zero maintenance required - enums automatically registered on file creation
- **New API Endpoint**: `GET /enums/list` for retrieving metadata of all available enums
  - Returns enum keys, names, descriptions, routes, counts, and categories
  - Supports dynamic enum registration
- **toArraySafe() Method**: Safe enum value conversion with graceful degradation
  - Handles invalid/legacy enum values without throwing exceptions
  - Returns fallback object with configurable label transformation
  - Prevents application crashes from legacy data
- **Fallback Configuration**: New config options for invalid enum value handling
  - `fallback_color`: Default color for invalid values (default: 'default')
  - `fallback_label_transform`: Label transformation strategy (none/upper/lower/ucfirst/ucwords)
  - `fallback_labels`: Custom labels for specific invalid values
- **API Documentation**: Complete OpenAPI 3.0.3 specification for Apifox
  - 11 documented API endpoints with request/response examples
  - Bilingual documentation (Chinese)

### Changed
- **API Response Format**: Standardized to `{ "list": [...], "total": n }` structure
  - Applies to all enum option endpoints
  - More consistent and frontend-friendly
- **Default Configuration**:
  - `auto_register_routes` now defaults to `true` (was `false`)
  - `route_middleware` now defaults to `['api']` (was `['auth:sanctum']`)
  - Simplified initial setup for new installations
- **EnumRegistry**: Converted from hardcoded to fully automatic discovery
  - Removed ~60 lines of manual enum registration code
  - Dynamic scanning using Reflection API

### Fixed
- PHPDoc annotations conflict in EnumOptions trait
  - Removed duplicate `@method` annotations that caused IDE warnings
- Vue.js integration example corrected to access `data.data.list` path

### Documentation
- Added comprehensive documentation for automatic enum discovery system
- Added `toArraySafe()` usage examples with fallback configuration
- Added `GET /enums/list` endpoint documentation
- Fixed API response format examples across all endpoints
- Updated frontend integration examples (Vue, React)
- Synchronized English and Chinese README files

## [1.0.0] - 2025-01-17

First stable release.

### Added
- Initial release
- EnumOptions trait for PHP 8.1+ enums
- 10 preset enum classes covering common use cases
- Multi-language support (zh-CN, en)
- Configuration system for label and color overrides
- Artisan commands: `make:enum`, `enum:publish`, `enum:list-presets`
- API Resource integration examples
- Comprehensive documentation

### Features
- Frontend-friendly enum handling with labels, colors, and icons
- Flexible preset system: use as-is, publish, or create custom
- Translation priority: user > config > preset > default
- Multiple UI framework color scheme support
