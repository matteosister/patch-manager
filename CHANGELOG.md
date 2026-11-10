CHANGELOG
=========

6.0.0
-----
- Drop support for PHP < 8.3
- Add support for Symfony 8
- Update CI

5.0.0
-----
- Drop support for Symfony 4 and 5
- Improve CI checks
- Add strict types declaration
- Remove unneeded phpdoc

4.0.1
-----
- Add support for Symfony 6.0

4.0.0
-----
- Drop support for PHP < 8.0

3.0.0
-----
- Removed unsafe handlers that allowed modifying arbitrary data for a potential attacker
  - `Cypress\PatchManager\Handler\DataDoctrineHandler` 
  - `Cypress\PatchManager\Handler\DataHandler` 

2.0.0
-----
- Updated dependencies and minimum PHP version
- Added PHP CS Fixer
- Added PHPStan
- Added GitHub Actions pipeline
- Code cleanup

1.0.0
-----

- First release