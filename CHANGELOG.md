# 4.1.0

## Changes

## 🚀 Features

- Move configuration to PHP @core23 (#50)

## 📦 Dependencies

- Add support for doctrine/common 3 @core23 (#53)
- Drop support for PHP 7.2 @core23 (#56)

# 4.0.0

## Changes

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

# 3.3.0

## Changes

- Add return type to EntityManagerTrait @core23 (#39)
- Add missing strict file header @core23 (#30)
- Remove old symfony <4.2 code @core23 (#25)
- Add support for symfony 5 @core23 (#21)
- Removed explicit private visibility of services @core23 (#15)

## 🐛 Bug Fixes

- Added more strict phpstan rules @core23 (#13)
