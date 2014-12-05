Patch Manager
=============

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/matteosister/patch-manager/badges/quality-score.png?b=dev)](https://scrutinizer-ci.com/g/matteosister/patch-manager/?branch=dev)
[![Build Status](https://travis-ci.org/matteosister/patch-manager.svg?branch=dev)](https://travis-ci.org/matteosister/patch-manager)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/3c24052a-6051-4125-ad12-ad4e210de114/mini.png)](https://insight.sensiolabs.com/projects/3c24052a-6051-4125-ad12-ad4e210de114)

A php library to manage PATCH requests in a standardized (and elegant) way

Install with composer

``` json
{
    "require": {
        "cypresslab/patch-manager": "1.0.*@dev"
    }
}
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

