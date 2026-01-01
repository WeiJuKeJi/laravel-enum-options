# Changelog

All notable changes to `laravel-enum-options` will be documented in this file.

## [1.2.2] - 2026-01-01

### Fixed
- **Critical: Namespace mismatch**: Fixed namespace mismatch between published file location and namespace declaration
  - Published enums now maintain directory structure: `app/Enums/{Category}/{Name}Enum.php`
  - Namespace correctly matches file location: `namespace App\Enums\{Category}`
  - Example: `src/Presets/Payment/PaymentMethodEnum.php` → `app/Enums/Payment/PaymentMethodEnum.php`
  - EnumRegistry now scans category subdirectories when discovering app enums
  - Added `findCategory()` method in PublishEnumCommand to determine enum category
  - **BREAKING**: If you previously published enums with v1.2.0 or v1.2.1, you need to delete old flat files and republish

## [1.2.1] - 2026-01-01

### Fixed
- **Critical: Published enum conflict**: Fixed "Cannot declare enum" error when using `php artisan enum:publish`
  - EnumRegistry now checks if preset enum has been published to app before loading
  - Published enums in `app/Enums/` now take priority over preset enums
  - Added `isEnumPublishedToApp()` method to detect published enum files
  - Prevents duplicate enum declaration conflicts after running `enum:publish --all`

## [1.2.0] - 2025-12-19

### Added
- **Tree format for enum list**: Added `?format=tree` query parameter support for `/api/enums/list` endpoint
  - Returns hierarchical structure grouped by category for frontend left-tree-right-table layout
  - Fully dynamic category label resolution with configurable labels
  - Priority: `category_labels` config > translation files > ucfirst(category)
  - New translation files: `lang/zh-CN/categories.php` and `lang/en/categories.php`
  - Zero configuration required - automatically uses directory names with proper capitalization

### Changed
- **BREAKING: Color system redesign**: Complete overhaul to Element Plus standard types
  - Replaced all vivid colors (orange, blue, cyan, green, etc.) with semantic types
  - New color system: `success`, `primary`, `warning`, `danger`, `info`, `''` (default)
  - Color semantics:
    - `success`: Completed/successful states (green) - e.g., paid, active, published
    - `primary`: In-progress/main operations (blue) - e.g., processing, scheduled
    - `warning`: Pending/needs attention (orange) - e.g., unpaid, pending approval
    - `danger`: Failed/error/negative states (red) - e.g., failed, rejected, banned
    - `info`: Neutral/cancelled/inactive (gray) - e.g., cancelled, draft, archived
    - `''`: Default style with no special coloring
  - Updated all 9 preset enums: PaymentStatusEnum, PaymentMethodEnum, RefundStatusEnum, OrderStatusEnum, OrderTypeEnum, UserStatusEnum, GenderEnum, ApprovalStatusEnum, PublishStatusEnum
  - Simplified config: Removed `color_scheme` and `color_maps` sections
  - Added reference documentation for Element Plus standard types in config
  - **BREAKING**: Custom color overrides using old color names need updating to Element Plus types
  - **BREAKING**: No backward compatibility - all old color names removed from preset enums

### Fixed
- **Critical: Route kebab-case conversion**: Fixed `Str::kebab()` not converting snake_case to kebab-case
  - Changed from `Str::kebab($key)` to `Str::slug($key, '-')` in EnumRegistry and ServiceProvider
  - Routes now correctly generate as `/api/enums/approval-statuses` instead of `/api/enums/approval_statuses`
  - Affects both actual route registration and `/api/enums/list` metadata response
  - Root cause: `Str::kebab()` is designed for camelCase → kebab-case, not snake_case → kebab-case
  - Solution: `Str::slug()` is Laravel's built-in method for URL-friendly string conversion

## [1.1.0] - 2025-01-18

### Added
- **EnumRegistry Service**: Automatic enum discovery and registration system
  - Auto-discover preset enums from `src/Presets` directory
  - Auto-discover app enums from `app/Enums` directory
  - Support for multi-level category directories
  - Zero maintenance required - enums automatically registered on file creation
- **Dynamic Route Registration**: Fully automatic API route generation
  - Routes automatically created for all discovered enums
  - Uses `EnumController::show()` with dynamic enum key parameter
  - Supports unlimited enums without code changes
  - Consistent kebab-case URL naming (e.g., `payment_methods` → `payment-methods`)
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
- **EnumController**: Completely refactored for dynamic operation
  - Removed 9 hardcoded controller methods (e.g., `paymentMethods()`, `orderStatuses()`)
  - Removed 9 hardcoded enum class imports
  - Added dynamic `show(string $key)` method for all enums
  - Made `all()` method dynamic - automatically includes new enums
  - Reduced code from 173 lines to 124 lines (-28%)
- **ServiceProvider**: Routes now registered programmatically
  - Removed dependency on static `routes/api.php` file
  - Routes generated dynamically from EnumRegistry
  - Automatic route creation for any new enum
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
- Added dynamic route registration documentation with 3 manual options
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
