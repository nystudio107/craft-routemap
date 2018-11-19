# Route Map Changelog

## 1.1.4 - 2018.05.15
### Changed
* By default return only Section URLs where `status` is `enabled` (this can still be overridden via the criteria you pass in)

## 1.1.3 - 2018.04.15
### Changed
* Fixed improper controller return types
* More strict checking of Element classes

## 1.1.2 - 2018.04.14
### Changed
* RouteMap now returns the URI (aka path) instead of fully qualified URLs
* Code cleanup

## 1.1.1 - 2018.03.02
### Changed
* Fixed deprecation errors from Craft CMS 3 RC13

## 1.1.0 - 2018.02.20
### Added
* Added support for retrieving category route rules & URLs
* Added support for retrieving Control Panel & `routes.php` rules

### Changed
* Updated the `README.md` to reflect the new functionality

## 1.0.2 - 2018.02.01
### Added
* Renamed the composer package name to `craft-routemap`

## 1.0.1 - 2017-12-29
### Added
* Added cache busting of the Route Map caches when Elements are saved
* Added Route Map Cache as a cache that can be cleared via the Clear Caches utility

## 1.0.0 - 2017-12-27
### Added
* Initial release
