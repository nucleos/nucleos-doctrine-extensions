# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 4.2.0 - TBD

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
