[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/nystudio107/craft-routemap/badges/quality-score.png?b=v1)](https://scrutinizer-ci.com/g/nystudio107/craft-routemap/?branch=v1) [![Code Coverage](https://scrutinizer-ci.com/g/nystudio107/craft-routemap/badges/coverage.png?b=v1)](https://scrutinizer-ci.com/g/nystudio107/craft-routemap/?branch=v1) [![Build Status](https://scrutinizer-ci.com/g/nystudio107/craft-routemap/badges/build.png?b=v1)](https://scrutinizer-ci.com/g/nystudio107/craft-routemap/build-status/v1) [![Code Intelligence Status](https://scrutinizer-ci.com/g/nystudio107/craft-routemap/badges/code-intelligence.svg?b=v1)](https://scrutinizer-ci.com/code-intelligence)

# Route Map plugin for Craft CMS 3.x

Returns a list of Craft/Vue/React route rules and element URLs for ServiceWorkers from Craft entries

![Screenshot](resources/img/plugin-logo.png)

Related: [Route Map for Craft 2.x](https://github.com/nystudio107/routemap)

## Requirements

This plugin requires Craft CMS 3.0.0-RC3 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require nystudio107/craft-routemap

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Route Map.

You can also install Route Map via the **Plugin Store** in the Craft Control Panel.

## Route Map Overview

Route Map is a plugin to help bridge the routing gap between frontend technologies like Vue/React and Craft CMS. Using Route Map, you can define your routes in Craft CMS as usual, and use an XHR to get a list of the routes in JSON format for use in your Vue/React frontend (it converts `blog/{slug}` dynamic routes to `/blog/:slug`).

This allows you to create your routes dynamically in Craft CMS using the Control Panel, and have them translate automatically to your frontend framework of choice.

Route Map also assists with ServiceWorkers by providing a list of all of the URLs on your Craft CMS site, or just the specific sections you're interested in. You can limit the URLs returned via any `ElementQuery` criteria, and Route Map can even return a list of URLs to all of the Assets that a particular Entry uses (whether in Assets fields, or embedded in Matrix blocks).

This allows you, for instance, to have a ServiceWorker that will automatically pre-cache the latest 5 blog entries on your site, as well as any images displayed on those pages, so that they will work with offline browsing.

Route Map maintains a cache of each requested set of URLs for excellent performance for repeated requests. This cache is automatically cleared whenever entries are created or modified.

## Configuring Route Map

There's nothing to configure.

## Using Route Map via XHR

### Route Rules

#### All Route Rules

The controller API endpoint `/actions/route-map/routes/get-all-route-rules` will return all of your website's route rules in an array of associative arrays. By default, they are in Craft CMS format (e.g.: `blog/{slug}`):

```
{
  "sections": {
    "notFound": {
      "handle": "notFound",
      "siteId": "1",
      "type": "single",
      "url": "404",
      "template": "404"
    },
    "blog": {
      "handle": "blog",
      "siteId": "1",
      "type": "channel",
      "url": "blog/{slug}",
      "template": "blog/_entry"
    },
    "blogIndex": {
      "handle": "blogIndex",
      "siteId": "1",
      "type": "single",
      "url": "blog",
      "template": "blog/index"
    },
    "homepage": {
      "handle": "homepage",
      "siteId": "1",
      "type": "single",
      "url": "/",
      "template": "index"
    }
  },
  "categories": {
      "handle": "pets",
      "siteId": "1",
      "url": "pets/{slug}",
      "template": "pets/_entry"
  },
  "rules": {
    "blog/archive/<year:\d{4}>": {
      "template": "blog/_archive"
    }
  },
}
```
...where `sections` is an array of Section rules, `categories` is an array of Category rules, and `rules` is an array of rules specified in the Control Panel Settings->Routes combined with any route rules specified in your `config/routes.php`

If your website has multiple sites, Route Map will return the URL rules for each `siteId` as the index:

```
{
  "sections": {
    "notFound": {
      "1": {
        "handle": "notFound",
        "siteId": "1",
        "type": "single",
        "url": "404",
        "template": "404"
      },
      "2": {
        "handle": "notFound",
        "siteId": "2",
        "type": "single",
        "url": "es/404",
        "template": "404"
      }
    },
    "blog": {
      "1": {
        "handle": "blog",
        "siteId": "1",
        "type": "channel",
        "url": "blog/{slug}",
        "template": "blog/_entry"
      },
      "2": {
        "handle": "blog",
        "siteId": "2",
        "type": "channel",
        "url": "es/blog/{slug}",
        "template": "blog/_entry"
      }
    },
    "blogIndex": {
      "1": {
        "handle": "blogIndex",
        "siteId": "1",
        "type": "single",
        "url": "blog",
        "template": "blog/index"
      },
      "2": {
        "handle": "blogIndex",
        "siteId": "2",
        "type": "single",
        "url": "es/blog",
        "template": "blog/index"
      }
    },
    "homepage": {
      "1": {
        "handle": "homepage",
        "siteId": "1",
        "type": "single",
        "url": "/",
        "template": "index"
      },
      "2": {
        "handle": "homepage",
        "siteId": "2",
        "type": "single",
        "url": "es/",
        "template": "index"
      }
    }
  },
  "categories": {
    "1": {
      "handle": "pets",
      "siteId": "1",
      "url": "pets/{slug}",
      "template": "pets/_entry"
    },
    "2": {
      "handle": "pets",
      "siteId": "1",
      "url": "es/pets/{slug}",
      "template": "pets/_entry"
    }
  },
  "rules": {
    "blog/archive/<year:\d{4}>": {
      "template": "blog/_archive"
    }
  },
}
```

The default is to return all route rules for all `siteId`s but you can specify a particular site via the optional `siteId` parameter, e.g.: `/actions/route-map/routes/get-all-route-rules?siteId=2`

The `format` URL parameter allows you to specify either `Craft` | `React` | `Vue` format for your URL routes. For example, the controller API endpoint `/actions/route-map/routes/get-all-route-rules?format=Vue` will return the same route rules above, but formatted for `Vue`  (e.g.: `blog/:slug`):

```
{
  "sections": {
    "notFound": {
      "handle": "notFound",
      "siteId": "1",
      "type": "single",
      "url": "/404",
      "template": "404"
    },
    "blog": {
      "handle": "blog",
      "siteId": "1",
      "type": "channel",
      "url": "/blog/:slug",
      "template": "blog/_entry"
    },
    "blogIndex": {
      "handle": "blogIndex",
      "siteId": "1",
      "type": "single",
      "url": "/blog",
      "template": "blog/index"
    },
    "homepage": {
      "handle": "homepage",
      "siteId": "1",
      "type": "single",
      "url": "/",
      "template": "index"
    }
  },
  "categories": {
      "handle": "pets",
      "siteId": "1",
      "url": "pets/:slug",
      "template": "pets/_entry"
  },
  "rules": {
    "blog/archive/<year:\d{4}>": {
      "template": "blog/_archive"
    }
  },
}
```

Note that `blog/{slug}` was changed to `blog/:slug`. This allows you to easily map both static and dynamic Craft CMS routes to your router of choice.

#### Section Route Rules

If you want just the route rules for a particular section, you can use the controller API endpoint `/actions/route-map/routes/get-section-route-rules?section=blog` (note the required `section` parameter that specifies the Section handle you want):

```
{
  "handle": "blog",
  "siteId": "1",
  "type": "channel",
  "url": "blog/{slug}",
  "template": "blog/_entry"
}
```

Route Map will return the URL rules for each `siteId` as the index if you have multiple sites:

```
{
  "1": {
    "handle": "blog",
    "siteId": "1",
    "type": "channel",
    "url": "blog/{slug}",
    "template": "blog/_entry"
  },
  "2": {
    "handle": "blog",
    "siteId": "2",
    "type": "channel",
    "url": "es/blog/{slug}",
    "template": "blog/_entry"
  }
}
```

The default is to return all route rules for all `siteId`s but you can specify a particular site via the optional `siteId` parameter, e.g.: `/actions/route-map/routes/get-section-route-rules?section=blog&siteId=2`

You can also pass in the optional `format` parameter to get route rules from a specific section, in a particular format via the controller API endpoint `/actions/route-map/routes/get-section-route-rules?section=blog&format=Vue`

```
{
  "handle": "blog",
  "siteId": "1",
  "type": "channel",
  "url": "/blog/:slug",
  "template": "blog/_entry"
}
```

#### Category Route Rules

If you want just the route rules for a particular category, you can use the controller API endpoint `/actions/route-map/routes/get-category-route-rules?category=pets` (note the required `section` parameter that specifies the Section handle you want):

```
{
  "handle": "pets",
  "siteId": "1",
  "url": "pets/{slug}",
  "template": "pets/_entry"
}
```

Route Map will return the URL rules for each `siteId` as the index if you have multiple sites:

```
{
  "1": {
    "handle": "pets",
    "siteId": "1",
    "url": "pets/{slug}",
    "template": "pets/_entry"
  },
  "2": {
    "handle": "pets",
    "siteId": "2",
    "url": "es/pets/{slug}",
    "template": "pets/_entry"
  }
}
```

The default is to return all route rules for all `siteId`s but you can specify a particular site via the optional `siteId` parameter, e.g.: `/actions/route-map/routes/get-category-route-rules?category=pets&siteId=2`

You can also pass in the optional `format` parameter to get route rules from a specific section, in a particular format via the controller API endpoint `/actions/route-map/routes/get-category-route-rules?category=pets&format=Vue`

```
{
  "handle": "pets",
  "siteId": "1",
  "url": "/pets/:slug",
  "template": "pets/_entry"
}
```

#### Custom Route Rules

You can retrieve the custom route rules defined in the Craft CMS 3 Control Panel and in the `routes.php` file via the controller API endpoint `/actions/route-map/routes/get-route-rules`

```
{
    "blog/archive/<year:\d{4}>": {
      "template": "blog/_archive"
    }
}
```

The default is to return all route rules for all `siteId`s but you can specify a particular site via the optional `siteId` parameter, e.g.: `/actions/route-map/routes/get-route-rules?siteId=2`

You can also pass in the optional `includeGlobal` parameter (which defaults to `1` aka `true`) to determine if you want to include any global (for all sites) routes via the controller API endpoint `/actions/route-map/routes/get-route-rules?includeGlobal=0`

### Public URLs

#### All Public URLs

The controller API endpoint `/actions/route-map/routes/get-all-urls` will return a list of _all_ of the public URLs to all of the Elements on your website (Entries, Assets, Categories, even custom Elements):

```
[
  "http://craft3.dev/404",
  "http://craft3.dev/blog/a-gulp-workflow-for-frontend-development-automation",
  "http://craft3.dev/blog/making-websites-accessible-americans-with-disabilities-act-ada",
  "http://craft3.dev/blog/static-caching-with-craft-cms",
  "http://craft3.dev/blog/the-case-of-the-missing-php-session",
  "http://craft3.dev/blog/so-you-wanna-make-a-craft-3-plugin",
  "http://craft3.dev/blog/a-b-split-testing-with-nginx-craft-cms",
  "http://craft3.dev/blog/mobile-testing-local-dev-sharing-with-homestead",
  "http://craft3.dev/blog/simple-static-asset-versioning",
  "http://craft3.dev/blog/tags-gone-wild",
  "http://craft3.dev/blog/local-development-with-vagrant-homestead",
  "http://craft3.dev/blog/mitigating-disaster-via-website-backups",
  "http://craft3.dev/blog/web-hosting-for-agencies-freelancers",
  "http://craft3.dev/blog/implementing-critical-css",
  "http://craft3.dev/blog/autocomplete-search-with-the-element-api-vuejs",
  "http://craft3.dev/blog/json-ld-structured-data-and-erotica",
  "http://craft3.dev/blog/craft-3-beta-executive-summary",
  "http://craft3.dev/blog/prevent-google-from-indexing-staging-sites",
  "http://craft3.dev/blog/loadjs-as-a-lightweight-javascript-loader",
  "http://craft3.dev/blog/creating-a-content-builder-in-craft-cms",
  "http://craft3.dev/blog/service-workers-and-offline-browsing",
  "http://craft3.dev/blog/using-phpstorm-with-vagrant-homestead",
  "http://craft3.dev/blog/frontend-dev-best-practices-for-2017",
  "http://craft3.dev/blog/using-systemjs-as-javascript-loader",
  "http://craft3.dev/blog/a-better-package-json-for-the-frontend",
  "http://craft3.dev/blog/modern-seo-snake-oil-vs-substance",
  "http://craft3.dev/blog/lazy-loading-with-the-element-api-vuejs",
  "http://craft3.dev/blog/installing-mozjpeg-on-ubuntu-16-04-forge",
  "http://craft3.dev/blog/a-pretty-website-isnt-enough",
  "http://craft3.dev/blog/using-vuejs-2-0-with-craft-cms",
  "http://craft3.dev/blog/image-optimization-project-results",
  "http://craft3.dev/blog/database-asset-syncing-between-environments-in-craft-cms",
  "http://craft3.dev/blog/hardening-craft-cms-permissions",
  "http://craft3.dev/blog/multi-environment-config-for-craft-cms",
  "http://craft3.dev/blog/google-amp-should-you-care",
  "http://craft3.dev/blog/creating-optimized-images-in-craft-cms",
  "http://craft3.dev/blog/the-craft-cache-tag-in-depth",
  "http://craft3.dev/blog/twig-processing-order-and-scope",
  "http://craft3.dev/blog/stop-using-htaccess-files-no-really",
  "http://craft3.dev/blog",
  "http://craft3.dev/"
]
```

#### Section Public URLs

You can retrieve just the entries for a particular section via the controller API endpoint `/actions/route-map/routes/get-section-urls?section=blog` (note the required `section` parameter that specifies the Section handle you want):

```
[
  "http://craft3.dev/blog/a-gulp-workflow-for-frontend-development-automation",
  "http://craft3.dev/blog/making-websites-accessible-americans-with-disabilities-act-ada",
  "http://craft3.dev/blog/static-caching-with-craft-cms",
  "http://craft3.dev/blog/the-case-of-the-missing-php-session",
  "http://craft3.dev/blog/so-you-wanna-make-a-craft-3-plugin",
  "http://craft3.dev/blog/a-b-split-testing-with-nginx-craft-cms",
  "http://craft3.dev/blog/mobile-testing-local-dev-sharing-with-homestead",
  "http://craft3.dev/blog/simple-static-asset-versioning",
  "http://craft3.dev/blog/tags-gone-wild",
  "http://craft3.dev/blog/local-development-with-vagrant-homestead",
  "http://craft3.dev/blog/mitigating-disaster-via-website-backups",
  "http://craft3.dev/blog/web-hosting-for-agencies-freelancers",
  "http://craft3.dev/blog/implementing-critical-css",
  "http://craft3.dev/blog/autocomplete-search-with-the-element-api-vuejs",
  "http://craft3.dev/blog/json-ld-structured-data-and-erotica",
  "http://craft3.dev/blog/craft-3-beta-executive-summary",
  "http://craft3.dev/blog/prevent-google-from-indexing-staging-sites",
  "http://craft3.dev/blog/loadjs-as-a-lightweight-javascript-loader",
  "http://craft3.dev/blog/creating-a-content-builder-in-craft-cms",
  "http://craft3.dev/blog/service-workers-and-offline-browsing",
  "http://craft3.dev/blog/using-phpstorm-with-vagrant-homestead",
  "http://craft3.dev/blog/frontend-dev-best-practices-for-2017",
  "http://craft3.dev/blog/using-systemjs-as-javascript-loader",
  "http://craft3.dev/blog/a-better-package-json-for-the-frontend",
  "http://craft3.dev/blog/modern-seo-snake-oil-vs-substance",
  "http://craft3.dev/blog/lazy-loading-with-the-element-api-vuejs",
  "http://craft3.dev/blog/installing-mozjpeg-on-ubuntu-16-04-forge",
  "http://craft3.dev/blog/a-pretty-website-isnt-enough",
  "http://craft3.dev/blog/using-vuejs-2-0-with-craft-cms",
  "http://craft3.dev/blog/image-optimization-project-results",
  "http://craft3.dev/blog/database-asset-syncing-between-environments-in-craft-cms",
  "http://craft3.dev/blog/hardening-craft-cms-permissions",
  "http://craft3.dev/blog/multi-environment-config-for-craft-cms",
  "http://craft3.dev/blog/google-amp-should-you-care",
  "http://craft3.dev/blog/creating-optimized-images-in-craft-cms",
  "http://craft3.dev/blog/the-craft-cache-tag-in-depth",
  "http://craft3.dev/blog/twig-processing-order-and-scope",
  "http://craft3.dev/blog/stop-using-htaccess-files-no-really"
]
```

Both of the above controller API endpoints support an optional `criteria` parameter that lets you pass in an array of `ElementQuery` attribute key/value pairs to be used to refine the Entries selected.

For instance, if you wanted just the most recent 5 Entries from the `blog` section, you'd use the controller API endpoint `/actions/route-map/routes/get-section-urls?section=blog&criteria[limit]=5`:

```
[
  "http://craft3.dev/blog/a-gulp-workflow-for-frontend-development-automation",
  "http://craft3.dev/blog/making-websites-accessible-americans-with-disabilities-act-ada",
  "http://craft3.dev/blog/static-caching-with-craft-cms",
  "http://craft3.dev/blog/the-case-of-the-missing-php-session",
  "http://craft3.dev/blog/so-you-wanna-make-a-craft-3-plugin"
]
```

Or if you wanted the 5 oldest Entries from the `blog` section, you'd use the controller API endpoint `/actions/route-map/routes/get-section-urls?section=blog&criteria[limit]=5&criteria[order]=elements.dateCreated asc`:

```
[
  "http://craft3.dev/blog/stop-using-htaccess-files-no-really",
  "http://craft3.dev/blog/twig-processing-order-and-scope",
  "http://craft3.dev/blog/the-craft-cache-tag-in-depth",
  "http://craft3.dev/blog/creating-optimized-images-in-craft-cms",
  "http://craft3.dev/blog/google-amp-should-you-care"
]
```

The default is to return all URLs for all `siteId`s but you can specify a particular site via the optional `siteId` parameter, e.g.: `/actions/route-map/routes/get-section-urls?section=blog&siteId=2`

#### Category Public URLs

You can retrieve just the entries for a particular category via the controller API endpoint `/actions/route-map/routes/get-category-urls?category=pets` (note the required `category` parameter that specifies the Section handle you want):

```
[
  "http://craft3.dev/pets/african-grey-parrot",
  "http://craft3.dev/pets/pikachu",
  "http://craft3.dev/blog/rescue-dogs",
  "http://craft3.dev/blog/stinky-monkeys",
  "http://craft3.dev/blog/violent-iguanas",
  "http://craft3.dev/blog/fluffy-floof-balls",
  "http://craft3.dev/blog/snakes-in-the-grass"
]
```

Both of the above controller API endpoints support an optional `criteria` parameter that lets you pass in an array of `ElementQuery` attribute key/value pairs to be used to refine the Entries selected.

For instance, if you wanted just the most recent 5 Entries from the `pets` category, you'd use the controller API endpoint `/actions/route-map/routes/get-category-urls?category=pets&criteria[limit]=5`:

```
[
  "http://craft3.dev/pets/african-grey-parrot",
  "http://craft3.dev/pets/pikachu",
  "http://craft3.dev/blog/rescue-dogs",
  "http://craft3.dev/blog/stinky-monkeys",
  "http://craft3.dev/blog/violent-iguanas",
]
```

Or if you wanted the 5 oldest elements from the `pets` category, you'd use the controller API endpoint `/actions/route-map/routes/get-category-urls?category=pets&criteria[limit]=5&criteria[order]=elements.dateCreated asc`:

```
[
  "http://craft3.dev/blog/rescue-dogs",
  "http://craft3.dev/blog/stinky-monkeys",
  "http://craft3.dev/blog/violent-iguanas",
  "http://craft3.dev/blog/fluffy-floof-balls",
  "http://craft3.dev/blog/snakes-in-the-grass"
]
```

The default is to return all URLs for all `siteId`s but you can specify a particular site via the optional `siteId` parameter, e.g.: `/actions/route-map/routes/get-category-urls?section=blog&siteId=2`

### Entry URL Assets

The controller API endpoint `/actions/route-map/routes/get-url-asset-urls?url=/blog/tags-gone-wild` will return all of the image Assets from the Entry with the URI of `/blog/tags-gone-wild`, whether in Assets fields, or embedded in Matrix/Neo blocks (note the required `url` parameter that specifies the URL to the entry you want):

```
[
  "http://craft3.dev/img/blog/buried-in-tag-manager-tags.jpg",
  "http://craft3.dev/img/blog/they-told-two-friends.png",
  "http://craft3.dev/img/blog/tag-manager-tags-gone-wild.png",
  "http://craft3.dev/img/blog/google-chrome-activity-indicator.png",
  "http://craft3.dev/img/blog/tag-javascript-executing.png",
  "http://craft3.dev/img/blog/tags-are-prescription-drugs.jpg",
  "http://craft3.dev/img/blog/taming-tags-whip.jpg"
]

```

Either a full URL or a partial URI can be passed in via the `url` parameter.

By default, it only returns Assets of the type `image` but using the optional parameter `assetTypes` you can pass in an array of the types of Assets you want returned. For instance, if we wanted `image`, `video`, and `pdf` Assets returned, we'd use the controller API endpoint `/actions/route-map/routes/get-url-asset-urls?url=/blog/tags-gone-wild&assetTypes[0]=image&assetTypes[1]=video&assetTypes[2]=pdf'`.

### Arbitrary Element URLS

You can retrieve URLs for arbitrary Elements via the controller API endpoint `/actions/route-map/routes/get-element-urls?elementType=\craft\elements\Asset` (note the required `elementType` parameter that specifies the Element class you want):

```
[  
   "http://craft3.dev/assets/plugin-logo.png",
   "http://craft3.dev/assets/pic-zebra-0-fine.jpg",
   "http://craft3.dev/assets/LQ6J8Ltn7apPvBafn9jmpN29.jpg",
   "http://craft3.dev/assets/maxresdefault.jpg",
   "http://craft3.dev/assets/2f5dd408e26b60540429297ca57a8b76.jpg",
   "http://craft3.dev/assets/shark.jpg",
   "http://craft3.dev/assets/Desert-Leader.jpg",
   "http://craft3.dev/assets/Weimaraner_hero.jpg",
   "http://craft3.dev/assets/weimaraner.jpg",
   "http://craft3.dev/assets/geisha-japan-japanese-geisha.jpg",
   "http://craft3.dev/assets/6985756-beautiful-summer-beach-scenes-wallpapers.jpg",
   "http://craft3.dev/assets/CTO_SocietyOne-Success-Profile.docx",
   "http://craft3.dev/assets/chart.pdf",
   "http://craft3.dev/assets/website-accessibility-ada.jpg",
   "http://craft3.dev/assets/VEXnOnt.jpg",
   "http://craft3.dev/assets/nuggets.jpg",
   "http://craft3.dev/assets/D4gtcSe.png",
   "http://craft3.dev/assets/whiskey_sm.jpg",
   "http://craft3.dev/assets/whiskey-glass.jpg",
   "http://craft3.dev/assets/painted-face_170903_023413.jpg",
   "http://craft3.dev/assets/painted-face.jpg"
]
```

The controller API endpoint supports an optional `criteria` parameter that lets you pass in an array of `ElementQuery` criteria key/value pairs to be used to refine the Entries selected.

For instance, if you wanted just the most recent 5 Elements of the type `\craft\elements\Asset`, you'd use the controller API endpoint `/actions/route-map/routes/get-element-urls?elementType=\craft\elements\Asset&criteria[limit]=5`:

```
[  
   "http://craft3.dev/assets/plugin-logo.png",
   "http://craft3.dev/assets/pic-zebra-0-fine.jpg",
   "http://craft3.dev/assets/LQ6J8Ltn7apPvBafn9jmpN29.jpg",
   "http://craft3.dev/assets/maxresdefault.jpg",
   "http://craft3.dev/assets/2f5dd408e26b60540429297ca57a8b76.jpg"
]

```

Or if you wanted the 5 oldest Elements of the type `\craft\elements\Asset`, you'd use the controller API endpoint `/actions/route-map/routes/get-element-urls?elementType=\craft\elements\Asset&criteria[limit]=5&criteria[orderBy]=elements.dateCreated asc`:

```
[  
   "http://craft3.dev/assets/painted-face.jpg",
   "http://craft3.dev/assets/painted-face_170903_023413.jpg",
   "http://craft3.dev/assets/whiskey-glass.jpg",
   "http://craft3.dev/assets/whiskey_sm.jpg",
   "http://craft3.dev/assets/D4gtcSe.png"
]

```

The default is to return all URLs for all `siteId`s but you can specify a particular site via the optional `siteId` parameter, e.g.: `/actions/route-map/routes/get-element-urls?elementType=\craft\elements\Asset&siteId=2`

## Using Route Map in your Twig Templates

You can also access any of the aforementioned functionality from within Craft CMS Twig templates.

### Route Rules

#### All Route Rules

To get all of your website's route rules:

```
{% set routeRules = craft.routeMap.getAllRouteRules() %}
```

This will return an array of route rules, in the format:

```
  [
  'section' => [],
  'category' => [],
  'rules' => [],
  ]
```

...where `sections` is an array of Section rules, `categories` is an array of Category rules, and `rules` is an array of rules specified in the Control Panel Settings->Routes combined with any route rules specified in your `config/routes.php`

To specify the format that the route rules should be returned in, pass in either `Craft` | `React` | `Vue`:

```
{% set routeRules = craft.routeMap.getAllRouteRules('Vue') %}
```

The default is to return all route rules for all `siteId`s but you can specify a particular site via the optional `siteId` parameter:

```
{% set routeRules = craft.routeMap.getAllRouteRules('Vue', 2) %}
```

#### Section Route Rules

To get route rules from only a specific section (such as `blog`, in this case), pass in the Section handle:

```
{% set routeRules = craft.routeMap.getSectionRouteRules('blog') %}
```

You can also pass in the optional `format` parameter to get route rules from a specific section, in a particular format:

```
{% set routeRules = craft.routeMap.getSectionRouteRules('blog', 'Vue') %}
```

The default is to return all route rules for all `siteId`s but you can specify a particular site via the optional `siteId` parameter:

```
{% set routeRules = craft.routeMap.getSectionRouteRules('blog', 'Vue', 2) %}
```

#### Category Route Rules

To get route rules from only a specific category (such as `pets`, in this case), pass in the Category handle:

```
{% set routeRules = craft.routeMap.getCategoryRouteRules('pets') %}
```

You can also pass in the optional `format` parameter to get route rules from a specific section, in a particular format:

```
{% set routeRules = craft.routeMap.getCategoryRouteRules('pets', 'Vue') %}
```

The default is to return all route rules for all `siteId`s but you can specify a particular site via the optional `siteId` parameter:

```
{% set routeRules = craft.routeMap.getCategoryRouteRules('pets', 'Vue', 2) %}
```

#### Custom Route Rules

You can retrieve the custom route rules defined in the Craft CMS 3 Control Panel and in the `routes.php` file:

```
{% set routeRules = craft.routeMap.getRouteRules() %}
```

The default is to return all route rules for all `siteId`s but you can specify a particular site via the optional `siteId` parameter:

```
{% set routeRules = craft.routeMap.getRouteRules(1) %}
```

You can also pass in the optional `includeGlobal` parameter (which defaults to `1` aka `true`) to determine if you want to include any global (for all sites) routes:

```
{% set routeRules = craft.routeMap.getRouteRules(1, false) %}
```

### Public URLs

#### All Public URLs

To get all of your website's public Entry URLs:

```
{% set urls = craft.routeMap.getAllUrls() %}
```

To refine the URLs returned, you can pass in optional `ElementQuery` criteria via key/value pairs:

```
{% set urls = craft.routeMap.getAllUrls({'limit': 5}) %}
```

or

```
{% set urls = craft.routeMap.getAllUrls({'limit': 5, 'orderBy': 'elements.dateCreated asc'}) %}
```

The default is to return all URLs for all `siteId`s but you can specify a particular site via the optional `siteId` parameter:

```
{% set urls = craft.routeMap.getAllUrls({'limit': 5}, 2) %}
```

#### Section Public URLs

To get URLs from just a specific Section:

```
{% set urls = craft.routeMap.getSectionUrls('blog') %}
```

To refine the URLs returned, you can pass in optional `ElementQuery` criteria via key/value pairs:

```
{% set urls = craft.routeMap.getSectionUrls('blog', {'limit': 5}) %}
```

or

```
{% set urls = craft.routeMap.getSectionUrls('blog', {'limit': 5, 'orderBy': 'elements.dateCreated asc'}) %}
```

The default is to return all URLs for all `siteId`s but you can specify a particular site via the optional `siteId` parameter:

```
{% set urls = craft.routeMap.getSectionUrls('blog', {'limit': 5, 'orderBy': 'elements.dateCreated asc'}, 2) %}
```

#### Category Public URLs

To get URLs from just a specific Section:

```
{% set urls = craft.routeMap.getCategoryUrls('pets') %}
```

To refine the URLs returned, you can pass in optional `ElementQuery` criteria via key/value pairs:

```
{% set urls = craft.routeMap.getCategoryUrls('pets', {'limit': 5}) %}
```

or

```
{% set urls = craft.routeMap.getCategoryUrls('pets', {'limit': 5, 'orderBy': 'elements.dateCreated asc'}) %}
```

The default is to return all URLs for all `siteId`s but you can specify a particular site via the optional `siteId` parameter:

```
{% set urls = craft.routeMap.getCategoryUrls('pets', {'limit': 5, 'orderBy': 'elements.dateCreated asc'}, 2) %}
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

### Arbitrary Element URLs

To get all URLs for an arbitrary Element type:

```
{% set urls = craft.routeMap.getElementUrls('\craft\elements\Asset') %}
```

To refine the URLs returned, you can pass in optional `ElementQuery` criteria via key/value pairs:

```
{% set urls = craft.routeMap.getElementUrls('\craft\elements\Asset', {'limit': 5}) %}
```

or

```
{% set urls = craft.routeMap.getElementUrls('\craft\elements\Asset', {'limit': 5, 'orderBy': 'elements.dateCreated asc'}) %}
```

The default is to return all URLs for all `siteId`s but you can specify a particular site via the optional `siteId` parameter:

```
{% set urls = craft.routeMap.getElementUrls('\craft\elements\Asset', {'limit': 5}, 2) %}
```

## Using Route Map from your Plugins

The `RouteMap::$plugin->routes` service gives you access to all of the functions mentioned above via your plugins.

### Route Rules

#### All Route Rules

To get all of your website's route rules:

```
$routeRules = RouteMap::$plugin->routes->getAllRouteRules();
```

To specify the format that the route rules should be returned in, pass in either `Craft` | `React` | `Vue`:

```
$routeRules = RouteMap::$plugin->routes->getAllRouteRules('Vue');
```

The default is to return all route rules for all `siteId`s but you can specify a particular site via the optional `siteId` parameter:

```
$routeRules = RouteMap::$plugin->routes->getAllRouteRules('Vue', 2);
```

#### Section Route Rules

To get route rules from only a specific section (such as `blog`, in this case), pass in the Section handle:

```
$routeRules = RouteMap::$plugin->routes->getSectionRouteRules('blog');
```

You can also pass in the optional `format` parameter to get route rules from a specific section, in a particular format:

```
$routeRules = RouteMap::$plugin->routes->getSectionRouteRules('blog', 'Vue');
```

The default is to return all route rules for all `siteId`s but you can specify a particular site via the optional `siteId` parameter:

```
$routeRules = RouteMap::$plugin->routes->getSectionRouteRules('blog', 'Vue', 2);
```

#### Category Route Rules

To get route rules from only a specific category (such as `pets`, in this case), pass in the Category handle:

```
$routeRules = RouteMap::$plugin->routes->getCategoryRouteRules('pets');
```

You can also pass in the optional `format` parameter to get route rules from a specific category, in a particular format:

```
$routeRules = RouteMap::$plugin->routes->getCategoryRouteRules('pets', 'Vue');
```

The default is to return all route rules for all `siteId`s but you can specify a particular site via the optional `siteId` parameter:

```
$routeRules = RouteMap::$plugin->routes->getCategoryRouteRules('pets', 'Vue', 2);
```

#### Custom Route Rules

You can retrieve the custom route rules defined in the Craft CMS 3 Control Panel and in the `routes.php` file:

```
$routeRules = RouteMap::$plugin->routes->getRouteRules();
```

The default is to return all route rules for all `siteId`s but you can specify a particular site via the optional `siteId` parameter:

```
$routeRules = RouteMap::$plugin->routes->getRouteRules(1);
```

You can also pass in the optional `includeGlobal` parameter (which defaults to `1` aka `true`) to determine if you want to include any global (for all sites) routes:

```
$routeRules = RouteMap::$plugin->routes->getRouteRules(1, false);
```

### Public URLs

#### All Public URLs

To get all of your website's public Entry URLs:

```
$urls = RouteMap::$plugin->routes->getAllUrls();
```

To refine the URLs returned, you can pass in optional `ElementQuery` criteria via key/value pairs:

```
$urls = RouteMap::$plugin->routes->getAllUrls(['limit' => 5]);
```

or

```
$urls = RouteMap::$plugin->routes->getAllUrls(['limit' => 5, 'orderBy' => 'elements.dateCreated asc']);
```

The default is to return all URLs for all `siteId`s but you can specify a particular site via the optional `siteId` parameter:

```
$urls = RouteMap::$plugin->routes->getAllUrls(['limit' => 5], 2);
```

#### Section Public URLs

To get URLs from just a specific Section:

```
$urls = RouteMap::$plugin->routes->getSectionUrls('blog');
```

To refine the URLs returned, you can pass in optional `ElementQuery` criteria via key/value pairs:

```
$urls = RouteMap::$plugin->routes->getSectionUrls('blog', ['limit' => 5]);
```

or

```
$urls = RouteMap::$plugin->routes->getSectionUrls('blog', ['limit' => 5, 'ordery' => 'elements.dateCreated asc']);
```

The default is to return all URLs for all `siteId`s but you can specify a particular site via the optional `siteId` parameter:

```
$urls = RouteMap::$plugin->routes->getSectionUrls('blog', ['limit' => 5], 2);
```

#### Category Public URLs

To get URLs from just a specific Category:

```
$urls = RouteMap::$plugin->routes->getCategoryUrls('pets');
```

To refine the URLs returned, you can pass in optional `ElementQuery` criteria via key/value pairs:

```
$urls = RouteMap::$plugin->routes->getCategoryUrls('pets', ['limit' => 5]);
```

or

```
$urls = RouteMap::$plugin->routes->getCategoryUrls('pets', ['limit' => 5, 'ordery' => 'elements.dateCreated asc']);
```

The default is to return all URLs for all `siteId`s but you can specify a particular site via the optional `siteId` parameter:

```
$urls = RouteMap::$plugin->routes->getCategoryUrls('pets', ['limit' => 5], 2);
```

### Entry URL Assets

To get all of the Asset URLs in a particular Entry (whether in Assets fields or embedded in Matrix/Neo blocks) by passing in a URL or URI to the entry:

```
$urls = RouteMap::$plugin->routes->getUrlAssetUrls('/blog/tags-gone-wild');
```

By default, it returns only Assets of the type `image`. You can pass in an optional array of Asset types you want returned:

```
$urls = RouteMap::$plugin->routes->getUrlAssetUrls('/blog/tags-gone-wild', ['image', 'video', 'pdf']);
```

### Arbitrary Element URLs

To get all URLs for an arbitrary Element type:

```
$urls = RouteMap::$plugin->routes->getElementUrls('\craft\elements\Asset');
```

To refine the URLs returned, you can pass in optional `ElementQuery` criteria via key/value pairs:

```
$urls = RouteMap::$plugin->routes->getElementUrls('\craft\elements\Asset', ['limit': 5]);
```

or

```
$urls = RouteMap::$plugin->routes->getElementUrls('\craft\elements\Asset', ['limit': 5, 'orderBy': 'elements.dateCreated asc']);
```

The default is to return all URLs for all `siteId`s but you can specify a particular site via the optional `siteId` parameter:

```
$urls = RouteMap::$plugin->routes->getElementUrls('\craft\elements\Asset', ['limit': 5], 2);
```

## Route Map Roadmap

Some things to do, and ideas for potential features:

* Add support for Commerce Products / Variant URLs

Brought to you by [nystudio107](https://nystudio107.com)
