# Changelog

All Notable changes to `laravel-state-machine` will be documented in this file

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v3.4.6] - 2025-03-28

### Fixed

-   Use explicit nullable types (Fixed [#74](https://github.com/sebdesign/laravel-state-machine/issues/74))

## [v3.4.5] - 2025-02-20

### Added

-   Laravel 12.x Compatibility ([#73](https://github.com/sebdesign/laravel-state-machine/pull/73))

### Fixed

-   Convert null state to empty string. (Fixes [#72](https://github.com/sebdesign/laravel-state-machine/issues/72))

## [v3.4.4] - 2024-03-12

### Added

-   Laravel 11.x Compatibility ([#71](https://github.com/sebdesign/laravel-state-machine/pull/71))

## [v3.4.3] - 2024-01-30

### Fixed

-   compatibility update of Dispatcher with EventDispatcherInterface from Symfony7 ([#69](https://github.com/sebdesign/laravel-state-machine/pull/69))

## [v3.4.2] - 2023-11-16

### Fixed

-   Handle enum states (Fixes [#68](https://github.com/sebdesign/laravel-state-machine/issues/68))

## [v3.4.1] - 2023-04-18

### Added

-   Don't escape unicode characters ([#66](https://github.com/sebdesign/laravel-state-machine/issues/66))

## [v3.4.0] - 2023-02-01

### Added

-   Add support for Laravel 10 ([#65](https://github.com/sebdesign/laravel-state-machine/issues/65))
-   Add support for PHP 8.2 ([#65](https://github.com/sebdesign/laravel-state-machine/issues/65))

## [v3.3.0] - 2022-02-02

### Added

-   Add support for Laravel 9.0

### Fixed

-   Fix return types to event dispatcher ([#60](https://github.com/sebdesign/laravel-state-machine/issues/60))

## [v3.2.2] - 2021-11-27

Add support for PHP 8.1

## [v3.2.1] - 2021-08-25

-   Update composer.json

## [v3.2.0] - 2021-03-04

-   Add support for PHP 8

## [v3.1.2] - 2020-12-09

-   Switch to GitHub Actions

## [v3.1.1] - 2020-11-09

-   Normalize null states to associative arrays

## [v3.1.0] - 2020-09-07

-   Add support for Laravel 8

## [v3.0.2] - 2020-07-31

-   Remove bool typehint from $soft parameter

## [v3.0.1] - 2020-05-18

-   Quote state and transition names ([#41](https://github.com/sebdesign/laravel-state-machine/pull/41))

## [v3.0.0] - 2020-03-03

### Added

-   Add support for Laravel 7
-   Add command to generate images of graphs ([#32](https://github.com/sebdesign/laravel-state-machine/pull/32))

### Fixed

-   Display array when debugging metadata ([#33](https://github.com/sebdesign/laravel-state-machine/pull/33))

### Removed

-   Remove support for Laravel 6

## [v2.1.0] - 2019-11-30

### Added

-   Check or apply transitions with additional context

## [v2.0.4] - 2019-11-29

### Fixed

-   Fix service provider

## [v2.0.3] - 2019-11-02

### Fixed

-   Use loose equality operator for comparing states (#27) Thanks @ddevdreamer

## [v2.0.2] - 2019-09-12

### Fixed

-   Fix normalization of numeric states

## [v2.0.1] - 2019-09-02

### Removed

-   Drop support for PHP 7.0

## [v2.0.0] - 2019-09-02

### Added

-   Add support for Laravel 6.0

### Removed

-   Drop support for Laravel 5.1, 5.2, 5.3, and 5.4.

## [v1.4.0] - 2019-05-14

### Added

-   Added a MetadataStore to fetch metadata from graphs, states and transitions.

## [1.3.3] - 2019-04-18

### Changed

-   Update changelog

### Fixed

-   Fix tests

## [1.3.2] - 2019-02-28

### Fixed

-   Update dependencies for Laravel 5.8

## [1.3.1] - 2019-02-15

### Added

-   Add support for Laravel 5.8

## [1.3.0] - 2018-10-01

### Added

-   Implemented authorization using Gates and Policies.
-   Display callbacks in the debug command.

### Changed

-   Callback methods for classes that are not bound to the container are called statically.
-   Return exit codes for errors in the debug command.

## [1.2.5] - 2018-09-05

### Added

-   Add support for Laravel 5.7

## [1.2.4] - 2018-02-17

### Added

-   Add support for Laravel 5.6

## [1.2.3] - 2017-09-28

### Fixed

-   Don't merge default configuration

## [1.2.2] - 2017-08-30

### Fixed

-   Update tests for Laravel 5.5

## [1.2.1] - 2017-08-27

### Added

-   Support package auto-discovery

## [1.2.0] - 2017-08-27

### Changed

-   Simplify event dispatcher implementation

## [1.1.1] - 2017-01-31

### Added

-   Add support for Laravel 5.4

### Changes

-   Execute PHPUnit from vendor in Travis CI

## [1.1.0] - 2017-01-19

### Added

-   Implement event dispatcher

## [1.0.0] - 2017-01-14

-   initial release
