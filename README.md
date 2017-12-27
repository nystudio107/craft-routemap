# Route Map plugin for Craft CMS 3.x

Returns a list of public routes for elements with URLs

![Screenshot](resources/img/plugin-logo.png)

Related: [Route Map for Craft 2.x](https://github.com/nystudio107/routemap)

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require nystudio107/craft3-routemap

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Route Map.

## Route Map Overview

Route Map is a plugin to help bridge the routing gap between frontend technologies like Vue/React and Craft CMS. Using Route Map, you can define your routes in Craft CMS as usual, and use an XHR to get a list of the routes in JSON format for use in your Vue/React frontend (it converts `blog/{slug}` dynamic routes to `/blog/:slug`).

This allows you to create your routes dynamically in Craft CMS using the AdminCP, and have them translate automatically to your frontend framework of choice.

Route Map also assists with ServiceWorkers by providing a list of all of the URLs on your Craft CMS site, or just the specific sections you're interested in. You can limit the URLs returned via any `ElementCriteriaModel` attributes, and Route Map can even return a list of URLs to all of the Assets that a particular Entry uses (whether in Assets fields, or embedded in Matrix/Neo blocks).

This allows you, for instance, to have a ServiceWorker that will automatically pre-cache the latest 5 blog entries on your site, as well as any images displayed on those pages, so that they will work with offline browsing.

Route Map maintains a cache of each requested set of URLs for excellent performance for repeated requests. This cache is automatically cleared whenever entries are created or modified.

## Configuring Route Map

There's nothing to configure.

## Using Route Map via XHR

### Route Rules

The controller API endpoint `/admin/actions/routeMap/getAllRouteRules` will return all of your website's route rules in an associative array. By default, they are in Craft CMS format (e.g.: `blog/{slug}`):

```
{
  "notFound": {
    "handle": "notFound",
    "type": "single",
    "url": "404",
    "template": "404"
  },
  "blog": {
    "handle": "blog",
    "type": "channel",
    "url": "blog\/{slug}",
    "template": "blog\/_entry"
  },
  "blogIndex": {
    "handle": "blogIndex",
    "type": "single",
    "url": "blog",
    "template": "blog\/index"
  },
  "homepage": {
    "handle": "homepage",
    "type": "single",
    "url": "\/",
    "template": "index"
  }
}
```

The `format` URL parameter allows you to specify either `Craft` | `React` | `Vue` format for your URL routes. For example, the controller API endpoint `/admin/actions/routeMap/getAllRouteRules?format=Vue` will return the same route rules above, but formatted for `Vue`  (e.g.: `blog/:slug`):

```
{
  "notFound": {
    "handle": "notFound",
    "type": "single",
    "url": "\/404",
    "template": "404"
  },
  "blog": {
    "handle": "blog",
    "type": "channel",
    "url": "\/blog\/:slug",
    "template": "blog\/_entry"
  },
  "blogIndex": {
    "handle": "blogIndex",
    "type": "single",
    "url": "\/blog",
    "template": "blog\/index"
  },
  "homepage": {
    "handle": "homepage",
    "type": "single",
    "url": "\/",
    "template": "index"
  }
}
```

Note that `blog\/{slug}` was changed to `blog\/:slug`. This allows you to easily map both static and dynamic Craft CMS routes to your router of choice.

If you want just the route rules for a particular section, you can use the controller API endpoint `/admin/actions/routeMap/getSectionRouteRules?section=blog` (note the required `section` parameter that specifies the Section handle you want):

```
{
  "handle": "blog",
  "type": "channel",
  "url": "blog\/{slug}",
  "template": "blog\/_entry"
}
```

You can also pass in the optional `format` parameter to get route rules from a specific section, in a particular format via the controller API endpoint `/admin/actions/routeMap/getSectionRouteRules?section=blog&format=Vue`

```
{
  "handle": "blog",
  "type": "channel",
  "url": "blog\/:slug",
  "template": "blog\/_entry"
}
```

### Entry URLs

The controller API endpoint `/admin/actions/routeMap/getAllUrls` will return a list of _all_ of the URLs to all of the Entries on your website:

```
[
  "http:\/\/nystudio107.dev\/404",
  "http:\/\/nystudio107.dev\/blog\/a-gulp-workflow-for-frontend-development-automation",
  "http:\/\/nystudio107.dev\/blog\/making-websites-accessible-americans-with-disabilities-act-ada",
  "http:\/\/nystudio107.dev\/blog\/static-caching-with-craft-cms",
  "http:\/\/nystudio107.dev\/blog\/the-case-of-the-missing-php-session",
  "http:\/\/nystudio107.dev\/blog\/so-you-wanna-make-a-craft-3-plugin",
  "http:\/\/nystudio107.dev\/blog\/a-b-split-testing-with-nginx-craft-cms",
  "http:\/\/nystudio107.dev\/blog\/mobile-testing-local-dev-sharing-with-homestead",
  "http:\/\/nystudio107.dev\/blog\/simple-static-asset-versioning",
  "http:\/\/nystudio107.dev\/blog\/tags-gone-wild",
  "http:\/\/nystudio107.dev\/blog\/local-development-with-vagrant-homestead",
  "http:\/\/nystudio107.dev\/blog\/mitigating-disaster-via-website-backups",
  "http:\/\/nystudio107.dev\/blog\/web-hosting-for-agencies-freelancers",
  "http:\/\/nystudio107.dev\/blog\/implementing-critical-css",
  "http:\/\/nystudio107.dev\/blog\/autocomplete-search-with-the-element-api-vuejs",
  "http:\/\/nystudio107.dev\/blog\/json-ld-structured-data-and-erotica",
  "http:\/\/nystudio107.dev\/blog\/craft-3-beta-executive-summary",
  "http:\/\/nystudio107.dev\/blog\/prevent-google-from-indexing-staging-sites",
  "http:\/\/nystudio107.dev\/blog\/loadjs-as-a-lightweight-javascript-loader",
  "http:\/\/nystudio107.dev\/blog\/creating-a-content-builder-in-craft-cms",
  "http:\/\/nystudio107.dev\/blog\/service-workers-and-offline-browsing",
  "http:\/\/nystudio107.dev\/blog\/using-phpstorm-with-vagrant-homestead",
  "http:\/\/nystudio107.dev\/blog\/frontend-dev-best-practices-for-2017",
  "http:\/\/nystudio107.dev\/blog\/using-systemjs-as-javascript-loader",
  "http:\/\/nystudio107.dev\/blog\/a-better-package-json-for-the-frontend",
  "http:\/\/nystudio107.dev\/blog\/modern-seo-snake-oil-vs-substance",
  "http:\/\/nystudio107.dev\/blog\/lazy-loading-with-the-element-api-vuejs",
  "http:\/\/nystudio107.dev\/blog\/installing-mozjpeg-on-ubuntu-16-04-forge",
  "http:\/\/nystudio107.dev\/blog\/a-pretty-website-isnt-enough",
  "http:\/\/nystudio107.dev\/blog\/using-vuejs-2-0-with-craft-cms",
  "http:\/\/nystudio107.dev\/blog\/image-optimization-project-results",
  "http:\/\/nystudio107.dev\/blog\/database-asset-syncing-between-environments-in-craft-cms",
  "http:\/\/nystudio107.dev\/blog\/hardening-craft-cms-permissions",
  "http:\/\/nystudio107.dev\/blog\/multi-environment-config-for-craft-cms",
  "http:\/\/nystudio107.dev\/blog\/google-amp-should-you-care",
  "http:\/\/nystudio107.dev\/blog\/creating-optimized-images-in-craft-cms",
  "http:\/\/nystudio107.dev\/blog\/the-craft-cache-tag-in-depth",
  "http:\/\/nystudio107.dev\/blog\/twig-processing-order-and-scope",
  "http:\/\/nystudio107.dev\/blog\/stop-using-htaccess-files-no-really",
  "http:\/\/nystudio107.dev\/blog",
  "http:\/\/nystudio107.dev\/"
]
```

You can retrieve just the entries for a particular section via the controller API endpoint `/admin/actions/routeMap/getSectionUrls?section=blog` (note the required `section` parameter that specifies the Section handle you want):

```
[
  "http:\/\/nystudio107.dev\/blog\/a-gulp-workflow-for-frontend-development-automation",
  "http:\/\/nystudio107.dev\/blog\/making-websites-accessible-americans-with-disabilities-act-ada",
  "http:\/\/nystudio107.dev\/blog\/static-caching-with-craft-cms",
  "http:\/\/nystudio107.dev\/blog\/the-case-of-the-missing-php-session",
  "http:\/\/nystudio107.dev\/blog\/so-you-wanna-make-a-craft-3-plugin",
  "http:\/\/nystudio107.dev\/blog\/a-b-split-testing-with-nginx-craft-cms",
  "http:\/\/nystudio107.dev\/blog\/mobile-testing-local-dev-sharing-with-homestead",
  "http:\/\/nystudio107.dev\/blog\/simple-static-asset-versioning",
  "http:\/\/nystudio107.dev\/blog\/tags-gone-wild",
  "http:\/\/nystudio107.dev\/blog\/local-development-with-vagrant-homestead",
  "http:\/\/nystudio107.dev\/blog\/mitigating-disaster-via-website-backups",
  "http:\/\/nystudio107.dev\/blog\/web-hosting-for-agencies-freelancers",
  "http:\/\/nystudio107.dev\/blog\/implementing-critical-css",
  "http:\/\/nystudio107.dev\/blog\/autocomplete-search-with-the-element-api-vuejs",
  "http:\/\/nystudio107.dev\/blog\/json-ld-structured-data-and-erotica",
  "http:\/\/nystudio107.dev\/blog\/craft-3-beta-executive-summary",
  "http:\/\/nystudio107.dev\/blog\/prevent-google-from-indexing-staging-sites",
  "http:\/\/nystudio107.dev\/blog\/loadjs-as-a-lightweight-javascript-loader",
  "http:\/\/nystudio107.dev\/blog\/creating-a-content-builder-in-craft-cms",
  "http:\/\/nystudio107.dev\/blog\/service-workers-and-offline-browsing",
  "http:\/\/nystudio107.dev\/blog\/using-phpstorm-with-vagrant-homestead",
  "http:\/\/nystudio107.dev\/blog\/frontend-dev-best-practices-for-2017",
  "http:\/\/nystudio107.dev\/blog\/using-systemjs-as-javascript-loader",
  "http:\/\/nystudio107.dev\/blog\/a-better-package-json-for-the-frontend",
  "http:\/\/nystudio107.dev\/blog\/modern-seo-snake-oil-vs-substance",
  "http:\/\/nystudio107.dev\/blog\/lazy-loading-with-the-element-api-vuejs",
  "http:\/\/nystudio107.dev\/blog\/installing-mozjpeg-on-ubuntu-16-04-forge",
  "http:\/\/nystudio107.dev\/blog\/a-pretty-website-isnt-enough",
  "http:\/\/nystudio107.dev\/blog\/using-vuejs-2-0-with-craft-cms",
  "http:\/\/nystudio107.dev\/blog\/image-optimization-project-results",
  "http:\/\/nystudio107.dev\/blog\/database-asset-syncing-between-environments-in-craft-cms",
  "http:\/\/nystudio107.dev\/blog\/hardening-craft-cms-permissions",
  "http:\/\/nystudio107.dev\/blog\/multi-environment-config-for-craft-cms",
  "http:\/\/nystudio107.dev\/blog\/google-amp-should-you-care",
  "http:\/\/nystudio107.dev\/blog\/creating-optimized-images-in-craft-cms",
  "http:\/\/nystudio107.dev\/blog\/the-craft-cache-tag-in-depth",
  "http:\/\/nystudio107.dev\/blog\/twig-processing-order-and-scope",
  "http:\/\/nystudio107.dev\/blog\/stop-using-htaccess-files-no-really"
]
```

Both of the above controller API endpoints support an optional `attributes` parameter that lets you pass in an array of `ElementCriteriaModel` attribute key/value pairs to be used to refine the Entries selected.

For instance, if you wanted just the most recent 5 Entries from the `blog` section, you'd use the controller API endpoint `/admin/actions/routeMap/getSectionUrls?section=blog&attributes[limit]=5`:

```
[
  "http:\/\/nystudio107.dev\/blog\/a-gulp-workflow-for-frontend-development-automation",
  "http:\/\/nystudio107.dev\/blog\/making-websites-accessible-americans-with-disabilities-act-ada",
  "http:\/\/nystudio107.dev\/blog\/static-caching-with-craft-cms",
  "http:\/\/nystudio107.dev\/blog\/the-case-of-the-missing-php-session",
  "http:\/\/nystudio107.dev\/blog\/so-you-wanna-make-a-craft-3-plugin"
]
```

Or if you wanted the 5 oldest Entries from the `blog` section, you'd use the controller API endpoint `/admin/actions/routeMap/getSectionUrls?section=blog&attributes[limit]=5&attributes[order]=postDate asc`:

```
[
  "http:\/\/nystudio107.dev\/blog\/stop-using-htaccess-files-no-really",
  "http:\/\/nystudio107.dev\/blog\/twig-processing-order-and-scope",
  "http:\/\/nystudio107.dev\/blog\/the-craft-cache-tag-in-depth",
  "http:\/\/nystudio107.dev\/blog\/creating-optimized-images-in-craft-cms",
  "http:\/\/nystudio107.dev\/blog\/google-amp-should-you-care"
]
```

### Entry URL Assets

The controller API endpoint `/admin/actions/routeMap/getUrlAssetUrls?url=/blog/tags-gone-wild` will return all of the image Assets from the Entry with the URI of `/blog/tags-gone-wild`, whether in Assets fields, or embedded in Matrix/Neo blocks (note the required `url` parameter that specifies the URL to the entry you want):

```
[
  "http:\/\/nystudio107.dev\/img\/blog\/buried-in-tag-manager-tags.jpg",
  "http:\/\/nystudio107.dev\/img\/blog\/they-told-two-friends.png",
  "http:\/\/nystudio107.dev\/img\/blog\/tag-manager-tags-gone-wild.png",
  "http:\/\/nystudio107.dev\/img\/blog\/google-chrome-activity-indicator.png",
  "http:\/\/nystudio107.dev\/img\/blog\/tag-javascript-executing.png",
  "http:\/\/nystudio107.dev\/img\/blog\/tags-are-prescription-drugs.jpg",
  "http:\/\/nystudio107.dev\/img\/blog\/taming-tags-whip.jpg"
]
```

Either a full URL or a partial URI can be passed in via the `url` parameter.

By default, it only returns Assets of the type `image` but using the optional parameter `assetTypes` you can pass in an array of the types of Assets you want returned. For instance, if we wanted `image`, `video`, and `pdf` Assets returned, we'd use the controller API endpoint `/admin/actions/routeMap/getUrlAssetUrls?url=/blog/tags-gone-wild&assetTypes[0]=image&assetTypes[1]=video&assetTypes[2]=pdf'`.

## Using Route Map in your Twig Templates

You can also access any of the aforementioned functionality from within Craft CMS Twig templates.

### Route Rules

To get all of your website's route rules:

```
{% set routeRules = craft.routeMap.getAllRouteRules() %}
```

To specify the format that the route rules should be returned in, pass in either `Craft` | `React` | `Vue`:

```
{% set routeRules = craft.routeMap.getAllRouteRules('Vue') %}
```

To get route rules from only a specific section (such as `blog`, in this case), pass in the Section handle:

```
{% set routeRules = craft.routeMap.getSectionRouteRules('blog') %}
```

You can also pass in the optional `format` parameter to get route rules from a specific section, in a particular format:

```
{% set routeRules = craft.routeMap.getSectionRouteRules('blog', 'Vue') %}
```

### Entry URLs

To get all of your website's public Entry URLs:

```
{% set urls = craft.routeMap.getAllUrls() %}
```

To refine the URLs returned, you can pass in optional `ElementCriteriaModel` attributes via key/value pairs:

```
{% set urls = craft.routeMap.getAllUrls({'limit': 5}) %}
```

or

```
{% set urls = craft.routeMap.getAllUrls({'limit': 5, 'order': 'postDate asc'}) %}
```

To get URLs from just a specific Section:

```
{% set urls = craft.routeMap.getSectionUrls('blog') %}
```

To refine the URLs returned, you can pass in optional `ElementCriteriaModel` attributes via key/value pairs:

```
{% set urls = craft.routeMap.getSectionUrls('blog', {'limit': 5}) %}
```

or

```
{% set urls = craft.routeMap.getSectionUrls('blog', {'limit': 5, 'order': 'postDate asc'}) %}
```

### Entry URL Assets

To get all of the Asset URLs in a particular Entry (whether in Assets fields or embedded in Matrix/Neo blocks) by passing in a URL or URI to the entry:

```
{% set urls = craft.routeMap.getUrlAssetUrls('/blog/tags-gone-wild') %}
```

By default, it returns only Assets of the type `image`. You can pass in an optional array of Asset types you want returned:

```
{% set urls = craft.routeMap.getUrlAssetUrls('/blog/tags-gone-wild', ['image', 'video', 'pdf']) %}
```

## Using Route Map from your Plugins

The `craft()->routeMap` service gives you access to all of the functions mentioned above via your plugins.

### Route Rules

To get all of your website's route rules:

```
$routeRules = craft()->routeMap->getAllRouteRules();
```

To specify the format that the route rules should be returned in, pass in either `Craft` | `React` | `Vue`:

```
$routeRules = craft()->routeMap->getAllRouteRules('Vue');
```

To get route rules from only a specific section (such as `blog`, in this case), pass in the Section handle:

```
$routeRules = craft()->routeMap->getSectionRouteRules('blog');
```

You can also pass in the optional `format` parameter to get route rules from a specific section, in a particular format:

```
$routeRules = craft()->routeMap->getSectionRouteRules('blog', 'Vue');
```

### Entry URLs

To get all of your website's public Entry URLs:

```
$urls = craft()->routeMap->getAllUrls();
```

To refine the URLs returned, you can pass in optional `ElementCriteriaModel` attributes via key/value pairs:

```
$urls = craft()->routeMap->getAllUrls(array('limit' => 5));
```

or

```
$urls = craft()->routeMap->getAllUrls(array('limit' => 5, 'order' => 'postDate asc'));
```

To get URLs from just a specific Section:

```
$urls = craft()->routeMap->getSectionUrls('blog');
```

To refine the URLs returned, you can pass in optional `ElementCriteriaModel` attributes via key/value pairs:

```
$urls = craft()->routeMap->getSectionUrls('blog', array('limit' => 5));
```

or

```
$urls = craft()->routeMap->getSectionUrls('blog', array('limit' => 5, 'order' => 'postDate asc'));
```

### Entry URL Assets

To get all of the Asset URLs in a particular Entry (whether in Assets fields or embedded in Matrix/Neo blocks) by passing in a URL or URI to the entry:

```
$urls = craft()->routeMap->getUrlAssetUrls('/blog/tags-gone-wild');
```

By default, it returns only Assets of the type `image`. You can pass in an optional array of Asset types you want returned:

```
$urls = craft()->routeMap->getUrlAssetUrls('/blog/tags-gone-wild', array('image', 'video', 'pdf'));
```

## Route Map Roadmap

Some things to do, and ideas for potential features:

* Add support for Category Groups / Category URLs
* Add support for Commerce Products / Variant URLs
* Add support for multiple locales

Brought to you by [nystudio107](https://nystudio107.com)
