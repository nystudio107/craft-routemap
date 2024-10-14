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

use Craft;
use craft\base\Component;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\elements\Asset;
use craft\elements\Category;
use craft\elements\db\ElementQueryInterface;
use craft\elements\Entry;
use craft\elements\MatrixBlock;
use craft\fields\Assets as AssetsField;
use craft\fields\Matrix as MatrixField;
use nystudio107\routemap\helpers\Field as FieldHelper;
use yii\caching\TagDependency;
use function count;
use function in_array;
use function is_array;
use function is_int;
use function is_object;
use function is_string;

/**
 * @author    nystudio107
 * @package   RouteMap
 * @since     1.0.0
 */
class Routes extends Component
{
    // Constants
    // =========================================================================
    /**
     * @var string
     */
    public const ROUTE_FORMAT_CRAFT = 'Craft';

    /**
     * @var string
     */
    public const ROUTE_FORMAT_REACT = 'React';

    /**
     * @var string
     */
    public const ROUTE_FORMAT_VUE = 'Vue';

    /**
     * @var null
     */
    public const ROUTEMAP_CACHE_DURATION = null;

    /**
     * @var int
     */
    public const DEVMODE_ROUTEMAP_CACHE_DURATION = 30;

    /**
     * @var string
     */
    public const ROUTEMAP_CACHE_TAG = 'RouteMap';

    /**
     * @var string
     */
    public const ROUTEMAP_SECTION_RULES = 'Sections';

    /**
     * @var string
     */
    public const ROUTEMAP_CATEGORY_RULES = 'Categories';

    /**
     * @var string
     */
    public const ROUTEMAP_ELEMENT_URLS = 'ElementUrls';

    /**
     * @var string
     */
    public const ROUTEMAP_ASSET_URLS = 'AssetUrls';

    /**
     * @var string
     */
    public const ROUTEMAP_ALL_URLS = 'AllUrls';

    // Public Methods
    // =========================================================================
    /**
     * Return the public URLs for all elements that have URLs
     *
     * @param array $criteria
     * @param ?int $siteId
     * @return array
     */
    public function getAllUrls(array $criteria = [], ?int $siteId = null): array
    {
        $urls = [];
        $elements = Craft::$app->getElements();
        $elementTypes = $elements->getAllElementTypes();
        foreach ($elementTypes as $elementType) {
            /** @noinspection SlowArrayOperationsInLoopInspection */
            $urls = array_merge($urls, $this->getElementUrls($elementType, $criteria, $siteId));
        }

        return $urls;
    }

    /**
     * Return all the section route rules
     *
     * @param string $format 'Craft'|'React'|'Vue'
     * @param ?int $siteId
     * @return array
     */
    public function getAllRouteRules(string $format = 'Craft', ?int $siteId = null): array
    {
        // Get all the sections
        $sections = $this->getAllSectionRouteRules($format, $siteId);
        $categories = $this->getAllCategoryRouteRules($format, $siteId);
        $rules = $this->getRouteRules($siteId);

        return [
            'sections' => $sections,
            'categories' => $categories,
            'rules' => $rules,
        ];
    }

    /**
     * Return the public URLs for a section
     *
     * @param string $section
     * @param array $criteria
     * @param ?int $siteId
     * @return array
     */
    public function getSectionUrls(string $section, array $criteria = [], ?int $siteId = null): array
    {
        $criteria = array_merge([
            'section' => $section,
            'status' => 'enabled',
        ], $criteria);

        return $this->getElementUrls(Entry::class, $criteria, $siteId);
    }

    /**
     * Return all the section route rules
     *
     * @param string $format 'Craft'|'React'|'Vue'
     * @param ?int $siteId
     * @return array
     */
    public function getAllSectionRouteRules(string $format = 'Craft', ?int $siteId = null): array
    {
        $routeRules = [];
        // Get all the sections
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
     * @param string $section
     * @param string $format 'Craft'|'React'|'Vue'
     * @param ?int $siteId
     * @return array
     */
    public function getSectionRouteRules(string $section, string $format = 'Craft', ?int $siteId = null): array
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
        return $cache->getOrSet($cacheKey, function() use ($section, $format, $siteId): array {
            Craft::info(
                'Route Map cache miss: ' . $section,
                __METHOD__
            );
            $resultingRoutes = [];

            $section = Craft::$app->getSections()->getSectionByHandle($section);
            if ($section) {
                $sites = $section->getSiteSettings();

                foreach ($sites as $site) {
                    if ($site->hasUrls && ($siteId === null || (int)$site->siteId === $siteId)) {
                        // Get section data to return
                        $route = [
                            'handle' => $section->handle,
                            'siteId' => $site->siteId,
                            'type' => $section->type,
                            'url' => $site->uriFormat,
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
    }

    /**
     * Return the public URLs for a category
     *
     * @param string $category
     * @param array $criteria
     * @param ?int $siteId
     *
     * @return array
     */
    public function getCategoryUrls(string $category, array $criteria = [], ?int $siteId = null): array
    {
        $criteria = array_merge([
            'group' => $category,
        ], $criteria);

        return $this->getElementUrls(Category::class, $criteria, $siteId);
    }

    /**
     * Return all the cateogry group route rules
     *
     * @param string $format 'Craft'|'React'|'Vue'
     * @param ?int $siteId
     * @return array
     */
    public function getAllCategoryRouteRules(string $format = 'Craft', ?int $siteId = null): array
    {
        $routeRules = [];
        // Get all the sections
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
     * @param string $format 'Craft'|'React'|'Vue'
     * @param ?int $siteId
     * @return array
     */
    public function getCategoryRouteRules(int|string $category, string $format = 'Craft', ?int $siteId = null): array
    {
        $devMode = Craft::$app->getConfig()->getGeneral()->devMode;
        $cache = Craft::$app->getCache();

        if (is_int($category)) {
            $categoryGroup = Craft::$app->getCategories()->getGroupById($category);
            if ($categoryGroup === null) {
                return [];
            }

            $handle = $categoryGroup->handle;
        } else {
            $handle = $category;
        }

        if ($handle === null) {
            return [];
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
        return $cache->getOrSet($cacheKey, function() use ($category, $handle, $format, $siteId): array {
            Craft::info(
                'Route Map cache miss: ' . $category,
                __METHOD__
            );
            $resultingRoutes = [];
            $category = Craft::$app->getCategories()->getGroupByHandle($handle);
            if ($category) {
                $sites = $category->getSiteSettings();

                foreach ($sites as $site) {
                    if ($site->hasUrls && ($siteId === null || (int)$site->siteId === $siteId)) {
                        // Get section data to return
                        $route = [
                            'handle' => $category->handle,
                            'siteId' => $site->siteId,
                            'url' => $site->uriFormat,
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
    }

    /**
     * Get all the assets of the type $assetTypes that are used in the Entry
     * that matches the $url
     *
     * @param string $url
     * @param array $assetTypes
     * @param ?int $siteId
     * @return array
     */
    public function getUrlAssetUrls(string $url, array $assetTypes = ['image'], ?int $siteId = null): array
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
        return $cache->getOrSet($cacheKey, function() use ($uri, $assetTypes, $siteId): array {
            Craft::info(
                'Route Map cache miss: ' . $uri,
                __METHOD__
            );
            $resultingAssetUrls = [];

            // Find the element that matches this URI
            /** @var ?Entry $element */
            $element = Craft::$app->getElements()->getElementByUri($uri, $siteId, true);
            if ($element) {
                // Iterate any Assets fields for this entry
                $assetFields = FieldHelper::fieldsOfType($element, AssetsField::class);
                foreach ($assetFields as $assetField) {
                    /** @var Asset[] $assets */
                    $assets = $element[$assetField]->all();
                    foreach ($assets as $asset) {
                        /** @var $asset Asset */
                        if (in_array($asset->kind, $assetTypes, true)
                            && !in_array($asset->getUrl(), $resultingAssetUrls, true)) {
                            $resultingAssetUrls[] = $asset->getUrl();
                        }
                    }
                }

                // Iterate through any Assets embedded in Matrix fields
                $matrixFields = FieldHelper::fieldsOfType($element, MatrixField::class);
                foreach ($matrixFields as $matrixField) {
                    /** @var MatrixBlock[] $matrixBlocks */
                    $matrixBlocks = $element[$matrixField]->all();
                    foreach ($matrixBlocks as $matrixBlock) {
                        $assetFields = FieldHelper::matrixFieldsOfType($matrixBlock, AssetsField::class);
                        foreach ($assetFields as $assetField) {
                            foreach ($matrixBlock[$assetField] as $asset) {
                                /** @var $asset Asset */
                                if (in_array($asset->kind, $assetTypes, true)
                                    && !in_array($asset->getUrl(), $resultingAssetUrls, true)) {
                                    $resultingAssetUrls[] = $asset->getUrl();
                                }
                            }
                        }
                    }
                }
            }

            return $resultingAssetUrls;
        }, $duration, $dependency);
    }

    /**
     * Returns all the URLs for the given $elementType based on the passed in
     * $criteria and $siteId
     *
     * @param string|ElementInterface $elementType
     * @param array $criteria
     * @param ?int $siteId
     * @return array
     */
    public function getElementUrls(string|ElementInterface $elementType, array $criteria = [], ?int $siteId = null): array
    {
        $devMode = Craft::$app->getConfig()->getGeneral()->devMode;
        $cache = Craft::$app->getCache();

        // Merge in the $criteria passed in
        $criteria = array_merge([
            'siteId' => $siteId,
            'limit' => null,
        ], $criteria);
        // Set up our cache criteria
        /* @var ElementInterface $elementInterface */
        $elementInterface = is_object($elementType) ? $elementType : new $elementType();
        $cacheKey = $this->getCacheKey($this::ROUTEMAP_ELEMENT_URLS, [$elementInterface, $criteria, $siteId]);
        $duration = $devMode ? $this::DEVMODE_ROUTEMAP_CACHE_DURATION : $this::ROUTEMAP_CACHE_DURATION;
        $dependency = new TagDependency([
            'tags' => [
                $this::ROUTEMAP_CACHE_TAG,
            ],
        ]);

        // Just return the data if it's already cached
        return $cache->getOrSet($cacheKey, function() use ($elementInterface, $criteria): array {
            Craft::info(
                'Route Map cache miss: ' . $elementInterface::class,
                __METHOD__
            );
            $resultingUrls = [];

            // Get all of the entries in the section
            $query = $this->getElementQuery($elementInterface, $criteria);
            $elements = $query->all();

            // Iterate through the elements and grab their URLs
            foreach ($elements as $element) {
                if ($element instanceof Element
                    && $element->uri !== null
                    && !in_array($element->uri, $resultingUrls, true)
                ) {
                    $uri = $this->normalizeUri($element->uri);
                    $resultingUrls[] = $uri;
                }
            }

            return $resultingUrls;
        }, $duration, $dependency);
    }

    /**
     * Invalidate the RouteMap caches
     */
    public function invalidateCache(): void
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
     * @return array
     * @property ?int $siteId
     *
     * @property bool $includeGlobal - merge global routes with the site rules
     */
    public function getRouteRules(?int $siteId = null, bool $includeGlobal = true): array
    {
        $globalRules = [];

        $siteRoutes = $this->getDbRoutes($siteId);

        return array_merge(
            Craft::$app->getRoutes()->getConfigFileRoutes(),
            $globalRules,
            $siteRoutes
        );
    }

    // Protected Methods
    // =========================================================================
    /**
     * Query the database for db routes
     *
     * @param ?int $siteId
     * @return array
     */
    protected function getDbRoutes(?int $siteId = null): array
    {
        return Craft::$app->getRoutes()->getProjectConfigRoutes();
    }

    /**
     * Normalize the routes based on the format
     *
     * @param string $format 'Craft'|'React'|'Vue'
     * @param array $route
     * @return array
     */
    protected function normalizeFormat(string $format, array $route): array
    {
        // Normalize the URL
        $route['url'] = $this->normalizeUri($route['url']);
        // Transform the URLs depending on the format requested
        switch ($format) {
            // React & Vue routes have a leading / and {slug} -> :slug
            case $this::ROUTE_FORMAT_REACT:
            case $this::ROUTE_FORMAT_VUE:
                $matchRegEx = '`{(.*?)}`';
                $replaceRegEx = ':$1';
                $route['url'] = preg_replace($matchRegEx, $replaceRegEx, $route['url']);
                // Add a leading /
                $route['url'] = '/' . ltrim($route['url'], '/');
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
     * @param string $url
     * @return string
     */
    protected function normalizeUri(string $url): string
    {
        // Handle the special '__home__' URI
        if ($url === '__home__') {
            $url = '/';
        }

        return $url;
    }

    /**
     * Generate a cache key with the combination of the $prefix and an md5()
     * hashed version of the flattened $args array
     */
    protected function getCacheKey(string $prefix, array $args = []): string
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

        return $cacheKey . $flattenedArgs;
    }

    /**
     * Returns the element query based on $elementType and $criteria
     *
     * @param array $criteria
     * @param ElementInterface $elementType
     * @return ElementQueryInterface
     */
    protected function getElementQuery(ElementInterface $elementType, array $criteria): ElementQueryInterface
    {
        $query = $elementType::find();
        Craft::configure($query, $criteria);

        return $query;
    }
}
