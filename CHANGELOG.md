# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 4.3.1 - TBD

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 4.3.0 - 2021-10-07


-----

### Release Notes for [4.3.0](https://github.com/nucleos/nucleos-doctrine-extensions/milestone/3)

Feature release (minor)

### 4.3.0

- Total issues resolved: **0**
- Total pull requests resolved: **3**
- Total contributors: **1**

#### dependency

 - [258: Drop support for unmaintained symfony versions](https://github.com/nucleos/nucleos-doctrine-extensions/pull/258) thanks to @core23
 - [257: Add support for doctrine/dbal 3](https://github.com/nucleos/nucleos-doctrine-extensions/pull/257) thanks to @core23
 - [246: Drop PHP 7.3 support](https://github.com/nucleos/nucleos-doctrine-extensions/pull/246) thanks to @core23

## 4.2.0 - 2021-02-08



-----

### Release Notes for [4.2.0](https://github.com/nucleos/nucleos-doctrine-extensions/milestone/1)



### 4.2.0

- Total issues resolved: **0**
- Total pull requests resolved: **3**
- Total contributors: **1**

 - [80: Deprecate helper classes](https://github.com/nucleos/nucleos-doctrine-extensions/pull/80) thanks to @core23
 - [39: Add return type to EntityManagerTrait](https://github.com/nucleos/nucleos-doctrine-extensions/pull/39) thanks to @core23

#### dependency

 - [73: Add support for PHP 8](https://github.com/nucleos/nucleos-doctrine-extensions/pull/73) thanks to @core23

## 4.1.0

### Changes

### 🚀 Features

- Move configuration to PHP [@core23] ([#50])

### 📦 Dependencies

- Add support for doctrine/common 3 [@core23] ([#53])
- Drop support for PHP 7.2 [@core23] ([#56])

## 4.0.0

### Changes

* Renamed namespace `Core23\Doctrine` to `Nucleos\Doctrine` after move to [@nucleos]

  Run

  ```
  $ composer remove nucleos/doctrine-extensions
  ```

  and

  ```
  $ composer require nucleos/doctrine-extensions
  ```

  to update.

  Run

  ```
  $ find . -type f -exec sed -i '.bak' 's/Core23\\Doctrine/Nucleos\\Doctrine/g' {} \;
  ```

  to replace occurrences of `Core23\Doctrine` with `Nucleos\Doctrine`.

  Run

  ```
  $ find -type f -name '*.bak' -delete
  ```

  to delete backup files created in the previous step.

## 3.3.0

### Changes

- Add return type to EntityManagerTrait [@core23] ([#39])
- Add missing strict file header [@core23] ([#30])
- Remove old symfony <4.2 code [@core23] ([#25])
- Add support for symfony 5 [@core23] ([#21])
- Removed explicit private visibility of services [@core23] ([#15])

### 🐛 Bug Fixes

- Added more strict phpstan rules [@core23] ([#13])

[#56]: https://github.com/nucleos/nucleos-doctrine-extensions/pull/56
[#53]: https://github.com/nucleos/nucleos-doctrine-extensions/pull/53
[#50]: https://github.com/nucleos/nucleos-doctrine-extensions/pull/50
[#39]: https://github.com/nucleos/nucleos-doctrine-extensions/pull/39
[#30]: https://github.com/nucleos/nucleos-doctrine-extensions/pull/30
[#25]: https://github.com/nucleos/nucleos-doctrine-extensions/pull/25
[#21]: https://github.com/nucleos/nucleos-doctrine-extensions/pull/21
[#15]: https://github.com/nucleos/nucleos-doctrine-extensions/pull/15
[#13]: https://github.com/nucleos/nucleos-doctrine-extensions/pull/13
[@nucleos]: https://github.com/nucleos
[@core23]: https://github.com/core23
