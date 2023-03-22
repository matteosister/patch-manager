Patch Manager
=============
[![PHP Version](https://img.shields.io/packagist/php-v/cypresslab/patch-manager/dev-master)](https://packagist.org/packages/cypresslab/patch-manager)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/matteosister/patch-manager/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/matteosister/patch-manager/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/matteosister/patch-manager/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/matteosister/patch-manager/?branch=master)
[![Build Status](https://img.shields.io/github/actions/workflow/status/matteosister/patch-manager/php.yml?branch=master)](https://github.com/matteosister/patch-manager/actions)
[![Packagist Version](https://img.shields.io/packagist/v/cypresslab/patch-manager)](https://packagist.org/packages/cypresslab/patch-manager)
[![Packagist Downloads](https://img.shields.io/packagist/dt/cypresslab/patch-manager)](https://packagist.org/packages/cypresslab/patch-manager)

A php library to manage PATCH requests in a standardized (and elegant) way

### Be careful!!!

*From version 0.3 namespace will change from PatchManager\\... to Cypress\\PatchManager\\...*

## Install

Install with composer

```shell
composer require cypresslab/patch-manager
```

The idea for this library comes from this blog post: [Please. Don't Patch Like An Idiot.](http://williamdurand.fr/2014/02/14/please-do-not-patch-like-an-idiot/) by [William Durand](https://github.com/willdurand)

> It lets you patch resources in an *expressive way*

**PATCH /users/1**
``` json
{ "op": "data", "property": "username", "value": "new username" }
```

And let you **patch entire collections** with multiple operations

**PATCH /books**
``` json
[{ "op": "set_as_read" }, { "op": "return_to_library", "address": "221 B Baker St, London, England"}]
```

> it includes also a **Symfony bundle**

Still interested? [Head over to the wiki...](https://github.com/matteosister/patch-manager/wiki) for documentation

## Useful commands for development

- `composer format`: runs php-cs-fixer
- `composer analyse`: runs phpstan for static analysis
- `composer test`: runs phpunit