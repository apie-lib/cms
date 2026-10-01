<img src="https://raw.githubusercontent.com/apie-lib/apie-lib-monorepo/main/docs/apie-logo.svg" width="100px" align="left" />
<h1>cms</h1>






 [![Latest Stable Version](https://poser.pugx.org/apie/cms/v)](https://packagist.org/packages/apie/cms) [![Total Downloads](https://poser.pugx.org/apie/cms/downloads)](https://packagist.org/packages/apie/cms) [![Latest Unstable Version](https://poser.pugx.org/apie/cms/v/unstable)](https://packagist.org/packages/apie/cms) [![License](https://poser.pugx.org/apie/cms/license)](https://packagist.org/packages/apie/cms) [![PHP Composer](https://apie-lib.github.io/projectCoverage/coverage-cms.svg)](https://apie-lib.github.io/projectCoverage/cms/index.html)  

[![PHP Composer](https://github.com/apie-lib/cms/actions/workflows/php.yml/badge.svg?event=push)](https://github.com/apie-lib/cms/actions/workflows/php.yml)

This package is part of the [Apie](https://github.com/apie-lib) library.
The code is maintained in a monorepo, so PR's need to be sent to the [monorepo](https://github.com/apie-lib/apie-lib-monorepo/pulls)

## Documentation
`apie/cms` builds a complete admin panel (dashboard, resource CRUD forms, method-call actions) on top of your
Apie domain objects. It renders pages through `apie/html-builders` components and regular HTTP controllers, and
relies on a separate layout package (see below) to actually style the pages.

### Standalone usage
```bash
composer require apie/cms
```

A CMS layout must be installed to render anything, e.g. `apie/cms-layout-graphite`, `apie/cms-layout-ionic`,
`apie/cms-layout-kids` or `apie/cms-layout-ugly`; `Apie\Cms\LayoutPicker` selects the active layout (defaulting to `LayoutEnum::LAYOUT`,
overridable per-request with a `?layout=` query parameter). You can also register your own `TwigRenderer` in the
service container with your own templates instead of using a bundled layout.

### Symfony integration
Through `apie/apie-bundle`, the CMS controllers (`DashboardController`, `GetResourceController`,
`CreateResourceFormController`, ...) and their route definitions in `Apie\Cms\RouteDefinitions` are registered
automatically, exposing the admin panel routes for every configured bounded context. CSRF protection comes from
Symfony's framework bundle, so it is recommended to keep that bundle enabled.

### Laravel integration
`apie/laravel-apie` registers the generated `Apie\Cms\CmsServiceProvider`, wiring up the same controllers, route
definitions and `LayoutPicker`/`IconResolver` services for a Laravel application.
