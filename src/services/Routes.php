<?php
/**
 * Route Map plugin for Craft CMS 3.x
 *
 * Returns a list of public routes for elements with URLs
 *
 * @link      https://nystudio107.com/
 * @copyright Copyright (c) 2017 nystudio107
 */

namespace nystudio107\routemap\services;

use nystudio107\routemap\helpers\Field as FieldHelper;

use craft\base\ElementInterface;
use craft\elements\db\ElementQueryInterface;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\elements\Category;
use craft\helpers\ArrayHelper;

use craft\db\Query;
use craft\fields\Assets as AssetsField;
use craft\fields\Matrix as MatrixField;

use Craft;
use craft\base\Component;
use yii\caching\TagDependency;

/**
 * @author    nystudio107
 * @package   RouteMap
 * @since     1.0.0
 */
class Routes extends Component
{

    // Constants
    // =========================================================================

    const ROUTE_FORMAT_CRAFT = 'Craft';
    const ROUTE_FORMAT_REACT = 'React';
    const ROUTE_FORMAT_VUE = 'Vue';

    const ROUTEMAP_CACHE_DURATION = null;
    const DEVMODE_ROUTEMAP_CACHE_DURATION = 30;

    const ROUTEMAP_CACHE_TAG = 'RouteMap';

    const ROUTEMAP_SECTION_RULES = 'Sections';
    const ROUTEMAP_CATEGORY_RULES = 'Categories';
    const ROUTEMAP_ELEMENT_URLS = 'ElementUrls';
    const ROUTEMAP_ASSET_URLS = 'AssetUrls';
    const ROUTEMAP_ALL_URLS = 'AllUrls';

    // Public Methods
    // =========================================================================

    /**
     * Return the public URLs for all elements that have URLs
     *
     * @param array    $criteria
     * @param int|null $siteId
     *
     * @return array
     */
    public function getAllUrls($criteria = [], $siteId = null)
    {
        $urls = [];
        $elements = Craft::$app->getElements();
        $elementTypes = $elements->getAllElementTypes();
        foreach ($elementTypes as $elementType) {
            $urls = array_merge($urls, $this->getElementUrls($elementType, $criteria, $siteId));
        }

        return $urls;
    }

    /**
     * Return all of the section route rules
     *
     * @param string   $format 'Craft'|'React'|'Vue'
     * @param int|null $siteId
     *
     * @return array
     */
    public function getAllRouteRules(string $format = 'Craft', $siteId = null): array
    {
        // Get all of the sections
        $sections = $this->getAllSectionRouteRules($format, $siteId);
        $categories = $this->getAllCategoryRouteRules($format, $siteId);
        $rules = $this->getAllRules($siteId);

        return ['sections' => $sections, 'categories' => $categories];
    }

    /**
     * Return the public URLs for a section
     *
     * @param string   $section
     * @param array    $criteria
     * @param int|null $siteId
     *
     * @return array
     */
    public function getSectionUrls(string $section, $criteria = [], $siteId = null)
    {
        $criteria = array_merge([
            'section' => $section,
        ], $criteria);

        return $this->getElementUrls(Entry::class, $criteria, $siteId);
    }

    /**
     * Return all of the section route rules
     *
     * @param string   $format 'Craft'|'React'|'Vue'
     * @param int|null $siteId
     *
     * @return array
     */
    public function getAllSectionRouteRules(string $format = 'Craft', $siteId = null): array
    {
        $routeRules = [];
        // Get all of the sections
        $sections = Craft::$app->getSections()->getAllSections();
        foreach ($sections as $section) {
            $routes = $this->getSectionRouteRules($section->handle, $format, $siteId);
            if (!empty($routes)) {
                $routeRules[$section->handle] = $routes;
            }
        }

        return $routeRules;
    }


    /**
     * Return the route rules for a specific section
     *
     * @param string   $section
     * @param string   $format 'Craft'|'React'|'Vue'
     * @param int|null $siteId
     *
     * @return array
     */
    public function getSectionRouteRules(string $section, string $format = 'Craft', $siteId = null): array
    {
        $devMode = Craft::$app->getConfig()->getGeneral()->devMode;
        $cache = Craft::$app->getCache();

        // Set up our cache criteria
        $cacheKey = $this->getCacheKey($this::ROUTEMAP_SECTION_RULES, [$section, $format, $siteId]);
        $duration = $devMode ? $this::DEVMODE_ROUTEMAP_CACHE_DURATION : $this::ROUTEMAP_CACHE_DURATION;
        $dependency = new TagDependency([
            'tags' => [
                $this::ROUTEMAP_CACHE_TAG,
            ],
        ]);

        // Just return the data if it's already cached
        $routes = $cache->getOrSet($cacheKey, function () use ($section, $format, $siteId) {
            Craft::info(
                'Route Map cache miss: '.$section,
                __METHOD__
            );
            $resultingRoutes = [];

            $section = Craft::$app->getSections()->getSectionByHandle($section);
            if ($section) {
                $sites = $section->getSiteSettings();

                foreach ($sites as $site) {
                    if ($site->hasUrls && ($siteId == null || $site->siteId == $siteId)) {
                        // Get section data to return
                        $route = [
                            'handle'   => $section->handle,
                            'siteId'   => $site->siteId,
                            'type'     => $section->type,
                            'url'      => $site->uriFormat,
                            'template' => $site->template,
                        ];

                        // Normalize the routes based on the format
                        $resultingRoutes[$site->siteId] = $this->normalizeFormat($format, $route);
                    }
                }
            }
            // If there's only one siteId for this section, just return it
            if (count($resultingRoutes) === 1) {
                $resultingRoutes = reset($resultingRoutes);
            }

            return $resultingRoutes;
        }, $duration, $dependency);

        return $routes;
    }

    /**
     * Return the public URLs for a category
     *
     * @param string   $category
     * @param array    $criteria
     * @param int|null $siteId
     *
     * @return array
     */
    public function getCategoryUrls(string $category, $criteria = [], $siteId = null)
    {

        $criteria = array_merge([
            'group' => $category,
        ], $criteria);

        return $this->getElementUrls(Category::class, $criteria, $siteId);
    }


    /**
     * Return all of the cateogry group route rules
     *
     * @param string   $format 'Craft'|'React'|'Vue'
     * @param int|null $siteId
     *
     * @return array
     */
    public function getAllCategoryRouteRules(string $format = 'Craft', $siteId = null): array
    {
        $routeRules = [];
        // Get all of the sections
        $groups = Craft::$app->getCategories()->getAllGroups();
        foreach ($groups as $group) {
            $routes = $this->getCategoryRouteRules($group->handle, $format, $siteId);
            if (!empty($routes)) {
                $routeRules[$group->handle] = $routes;
            }
        }

        return $routeRules;
    }

    /**
     * Return the route rules for a specific category
     *
     * @param int|string $category
     * @param string     $format 'Craft'|'React'|'Vue'
     * @param int|null   $siteId
     *
     * @return array
     */
    public function getCategoryRouteRules($category, string $format = 'Craft', $siteId = null): array
    {
        $devMode = Craft::$app->getConfig()->getGeneral()->devMode;
        $cache = Craft::$app->getCache();

        if (is_numeric($category)) {
            $category = Craft::$app->getCategories()->getGroupById($category);
            $handle = $category->handle;
        } else {
            $handle = $category;
        }

        // Set up our cache criteria
        $cacheKey = $this->getCacheKey($this::ROUTEMAP_CATEGORY_RULES, [$category, $handle, $format, $siteId]);
        $duration = $devMode ? $this::DEVMODE_ROUTEMAP_CACHE_DURATION : $this::ROUTEMAP_CACHE_DURATION;
        $dependency = new TagDependency([
            'tags' => [
                $this::ROUTEMAP_CACHE_TAG,
            ],
        ]);
        // Just return the data if it's already cached
        $routes = $cache->getOrSet($cacheKey, function () use ($category, $handle, $format, $siteId) {
            Craft::info(
                'Route Map cache miss: '.$category,
                __METHOD__
            );
            $resultingRoutes = [];
            $category = is_object($category) ? $category : Craft::$app->getCategories()->getGroupByHandle($handle);
            if ($category) {
                $sites = $category->getSiteSettings();

                foreach ($sites as $site) {
                    if ($site->hasUrls && ($siteId == null || $site->siteId == $siteId)) {
                        // Get section data to return
                        $route = [
                            'handle'   => $category->handle,
                            'siteId'   => $site->siteId,
                            'url'      => $site->uriFormat,
                            'template' => $site->template,
                        ];

                        // Normalize the routes based on the format
                        $resultingRoutes[$site->siteId] = $this->normalizeFormat($format, $route);
                    }
                }
            }
            // If there's only one siteId for this section, just return it
            if (count($resultingRoutes) === 1) {
                $resultingRoutes = reset($resultingRoutes);
            }

            return $resultingRoutes;
        }, $duration, $dependency);

        return $routes;
    }

    /**
     * Get all of the assets of the type $assetTypes that are used in the Entry
     * that matches the $url
     *
     * @param string   $url
     * @param array    $assetTypes
     * @param int|null $siteId
     *
     * @return array
     */
    public function getUrlAssetUrls($url, $assetTypes = ['image'], $siteId = null)
    {
        $devMode = Craft::$app->getConfig()->getGeneral()->devMode;
        $cache = Craft::$app->getCache();

        // Extract a URI from the URL
        $uri = parse_url($url, PHP_URL_PATH);
        $uri = ltrim($uri, '/');
        // Set up our cache criteria
        $cacheKey = $this->getCacheKey($this::ROUTEMAP_ASSET_URLS, [$uri, $assetTypes, $siteId]);
        $duration = $devMode ? $this::DEVMODE_ROUTEMAP_CACHE_DURATION : $this::ROUTEMAP_CACHE_DURATION;
        $dependency = new TagDependency([
            'tags' => [
                $this::ROUTEMAP_CACHE_TAG,
            ],
        ]);

        // Just return the data if it's already cached
        $assetUrls = $cache->getOrSet($cacheKey, function () use ($uri, $assetTypes, $siteId) {
            Craft::info(
                'Route Map cache miss: '.$uri,
                __METHOD__
            );
            $resultingAssetUrls = [];

            // Find the element that matches this URI
            $element = Craft::$app->getElements()->getElementByUri($uri, $siteId, true);
            /** @var  $element Entry */
            if ($element) {
                // Iterate any Assets fields for this entry
                $assetFields = FieldHelper::fieldsOfType($element, AssetsField::className());
                foreach ($assetFields as $assetField) {
                    $assets = $element[$assetField]->all();
                    foreach ($assets as $asset) {
                        /** @var $asset Asset */
                        if (in_array($asset->kind, $assetTypes)) {
                            if (!in_array($asset->getUrl(), $resultingAssetUrls, true)) {
                                array_push($resultingAssetUrls, $asset->getUrl());
                            }
                        }
                    }
                }
                // Iterate through any Assets embedded in Matrix fields
                $matrixFields = FieldHelper::fieldsOfType($element, MatrixField::className());
                foreach ($matrixFields as $matrixField) {
                    $matrixBlocks = $element[$matrixField]->all();
                    foreach ($matrixBlocks as $matrixBlock) {
                        $assetFields = FieldHelper::matrixFieldsOfType($matrixBlock, AssetsField::className());
                        foreach ($assetFields as $assetField) {
                            foreach ($matrixBlock[$assetField] as $asset) {
                                /** @var $asset Asset */
                                if (in_array($asset->kind, $assetTypes)) {
                                    if (!in_array($asset->getUrl(), $resultingAssetUrls, true)) {
                                        array_push($resultingAssetUrls, $asset->getUrl());
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $resultingAssetUrls;
        }, $duration, $dependency);


        return $assetUrls;
    }

    /**
     * Returns all of the URLs for the given $elementType based on the passed in
     * $criteria and $siteId
     *
     * @var string|ElementInterface $elementType
     * @var array                   $criteria
     * @var int|null                $siteId
     *
     * @return array
     */
    public function getElementUrls($elementType, $criteria = [], $siteId = null)
    {
        $devMode = Craft::$app->getConfig()->getGeneral()->devMode;
        $cache = Craft::$app->getCache();

        // Merge in the $criteria passed in
        $criteria = array_merge([
            'siteId' => $siteId,
            'limit'  => null,
        ], $criteria);
        // Set up our cache criteria
        $elementClass = is_object($elementType) ? get_class($elementType) : $elementType;
        $cacheKey = $this->getCacheKey($this::ROUTEMAP_ELEMENT_URLS, [$elementClass, $criteria, $siteId]);
        $duration = $devMode ? $this::DEVMODE_ROUTEMAP_CACHE_DURATION : $this::ROUTEMAP_CACHE_DURATION;
        $dependency = new TagDependency([
            'tags' => [
                $this::ROUTEMAP_CACHE_TAG,
            ],
        ]);

        // Just return the data if it's already cached
        $urls = $cache->getOrSet($cacheKey, function () use ($elementClass, $criteria) {
            Craft::info(
                'Route Map cache miss: '.$elementClass,
                __METHOD__
            );
            $resultingUrls = [];

            // Get all of the entries in the section
            $query = $this->getElementQuery($elementClass, $criteria);
            $elements = $query->all();

            // Iterate through the elements and grab their URLs
            foreach ($elements as $element) {
                if (!empty($element->url) && !in_array($element->url, $resultingUrls, true)) {
                    array_push($resultingUrls, $element->url);
                }
            }

            return $resultingUrls;
        }, $duration, $dependency);

        return $urls;
    }

    /**
     * Invalidate the RouteMap caches
     */
    public function invalidateCache()
    {
        $cache = Craft::$app->getCache();
        TagDependency::invalidate($cache, self::ROUTEMAP_CACHE_TAG);
        Craft::info(
            'Route Map cache cleared',
            __METHOD__
        );
    }

    /**
     * Get all routes rules defined in the config/routes.php file and CMS
     *
     * @var int  $siteId
     * @var bool $incGlobalRules - merge global routes with the site rules
     *
     * @return array
     */
    public function getRouteRules($siteId = null, $incGlobalRules = true)
    {
        $globalRules = $incGlobalRules === true ? $this->getDbRoutes('global') : [];

        $siteRoutes = $this->getDbRoutes($siteId);

        $rules = array_merge(
            Craft::$app->getRoutes()->getConfigFileRoutes(),
            $globalRules,
            $siteRoutes
        );

        return $rules;
    }

    // Protected Methods
    // =========================================================================


    /**
     * Query the database for db routes
     *
     * @param int $siteId
     *
     * @return array
     */
    protected function getDbRoutes($siteId = null)
    {
        if ($siteId === 'global') {
            $siteId = null;
        } else if (is_null($siteId)) {
            $siteId = Craft::$app->getSites()->currentSite->id;
        };

        // Normalize the URL
        $results = (new Query())
            ->select(['uriPattern', 'template'])
            ->from(['{{%routes}}'])
            ->where([
                'or',
                ['siteId' => $siteId],
            ])
            ->orderBy(['sortOrder' => SORT_ASC])
            ->all();

        return ArrayHelper::map($results, 'uriPattern', function ($results) {
            return ['template' => $results['template']];
        });
    }


    /**
     * Normalize the routes based on the format
     *
     * @param string $format 'Craft'|'React'|'Vue'
     * @param array  $route
     *
     * @return array
     */
    protected function normalizeFormat($format, $route)
    {
        // Normalize the URL
        $route['url'] = $this->normalizeUri($route['url']);
        // Transform the URLs depending on the format requested
        switch ($format) {
            // React & Vue routes have a leading / and {slug} -> :slug
            case $this::ROUTE_FORMAT_REACT:
            case $this::ROUTE_FORMAT_VUE:
                $matchRegEx = "`{(.*?)}`i";
                $replaceRegEx = ":$1";
                $route['url'] = preg_replace($matchRegEx, $replaceRegEx, $route['url']);
                // Add a leading /
                $route['url'] = '/'.ltrim($route['url'], '/');
                break;

            // Craft-style URLs don't need to be changed
            case $this::ROUTE_FORMAT_CRAFT:
            default:
                // Do nothing
                break;
        }

        return $route;
    }

    /**
     * Normalize the URI
     *
     * @param $url
     *
     * @return string
     */
    protected function normalizeUri($url)
    {
        // Handle the special '__home__' URI
        if ($url == '__home__') {
            $url = '/';
        }

        return $url;
    }

    /**
     * Generate a cache key with the combination of the $prefix and an md5()
     * hashed version of the flattened $args array
     *
     * @param string $prefix
     * @param array  $args
     *
     * @return string
     */
    protected function getCacheKey($prefix, $args = [])
    {
        $cacheKey = $prefix;
        $flattenedArgs = '';
        // If an array of $args is passed in, flatten it into a concatenated string
        if (!empty($args)) {
            foreach ($args as $arg) {
                if ((is_object($arg) || is_array($arg)) && !empty($arg)) {
                    $flattenedArgs .= http_build_query($arg);
                }
                if (is_string($arg)) {
                    $flattenedArgs .= $arg;
                }
            }
            // Make an md5 hash out of it
            $flattenedArgs = md5($flattenedArgs);
        }

        return $cacheKey.$flattenedArgs;
    }

    /**
     * Returns the element query based on $elementType and $criteria
     *
     * @var string|ElementInterface $elementType
     * @var array                   $criteria
     *
     * @return ElementQueryInterface
     */
    protected function getElementQuery($elementType, array $criteria): ElementQueryInterface
    {
        /** @var string|ElementInterface $elementType */
        $query = $elementType::find();
        Craft::configure($query, $criteria);

        return $query;
    }
}
