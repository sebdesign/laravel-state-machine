# Changelog

All Notable changes to `laravel-state-machine` will be documented in this file

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [v2.0.0] - 2019-09-02

### Added
- Add support for Laravel 6.0

### Removed
- Drop support for Laravel 5.1, 5.2, 5.3, and 5.4.

## [v1.4.0] - 2019-05-14

### Added
- Added a MetadataStore to fetch metadata from graphs, states and transitions.

## [1.3.3] - 2019-04-18

### Changed
- Update changelog

### Fixed
- Fix tests

## [1.3.2] - 2019-02-28

### Fixed
- Update dependencies for Laravel 5.8

## [1.3.1] - 2019-02-15

### Added
- Add support for Laravel 5.8

## [1.3.0] - 2018-10-01

### Added
- Implemented authorization using Gates and Policies.
- Display callbacks in the debug command.

### Changed
- Callback methods for classes that are not bound to the container are called statically.
- Return exit codes for errors in the debug command.

## [1.2.5] - 2018-09-05

### Added
- Add support for Laravel 5.7

## [1.2.4] - 2018-02-17

### Added
- Add support for Laravel 5.6

## [1.2.3] - 2017-09-28

### Fixed
- Don't merge default configuration

## [1.2.2] - 2017-08-30

### Fixed 
- Update tests for Laravel 5.5

## [1.2.1] - 2017-08-27

### Added
- Support package auto-discovery

## [1.2.0] - 2017-08-27

### Changed
- Simplify event dispatcher implementation

## [1.1.1] - 2017-01-31

### Added
- Add support for Laravel 5.4

### Changes
- Execute PHPUnit from vendor in Travis CI

## [1.1.0] - 2017-01-19

### Added
- Implement event dispatcher

## [1.0.0] - 2017-01-14

- initial release
