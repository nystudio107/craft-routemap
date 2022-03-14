<?php
/**
 * Route Map plugin for Craft CMS 3.x
 *
 * Returns a list of public routes for elements with URLs
 *
 * @link      https://nystudio107.com/
 * @copyright Copyright (c) 2017 nystudio107
 */

namespace nystudio107\routemap\variables;

use nystudio107\routemap\RouteMap;

use craft\base\ElementInterface;

/**
 * @author    nystudio107
 * @package   RouteMap
 * @since     1.0.0
 */
class RouteMapVariable
{
    // Public Methods
    // =========================================================================
    /**
     * Return the public URLs for all elements that have URLs
     *
     * @param int|null $siteId
     * @return mixed[]
     */
    public function getAllUrls(array $criteria = [], $siteId = null): array
    {
        return RouteMap::$plugin->routes->getAllUrls($criteria, $siteId);
    }

    /**
     * Return all of the section and category route rules
     *
     * @param string $format 'Craft'|'React'|'Vue'
     * @param int|null $siteId
     *
     * @return mixed[]
     */
    public function getAllRouteRules(string $format = 'Craft', $siteId = null): array
    {
        return RouteMap::$plugin->routes->getAllRouteRules($format, $siteId);
    }


    /**
     * Return the public URLs for a section
     *
     * @param int|null $siteId
     *
     * @return mixed[]
     */
    public function getSectionUrls(string $section, array $criteria = [], $siteId = null): array
    {
        return RouteMap::$plugin->routes->getSectionUrls($section, $criteria, $siteId);
    }


    /**
     * Return all of the section route rules
     *
     * @param string $format 'Craft'|'React'|'Vue'
     * @param int|null $siteId
     *
     * @return mixed[]
     */
    public function getAllSectionRouteRules(string $format = 'Craft', $siteId = null): array
    {
        return RouteMap::$plugin->routes->getAllSectionRouteRules($format, $siteId);
    }

    /**
     * Return the route rules for a specific section
     *
     * @param string $format 'Craft'|'React'|'Vue'
     * @param int|null $siteId
     * @return mixed[]
     */
    public function getSectionRouteRules(string $section, string $format = 'Craft', $siteId = null): array
    {
        return RouteMap::$plugin->routes->getSectionRouteRules($section, $format, $siteId);
    }

    /**
     * Return the public URLs for a category group
     *
     * @param int|null $siteId
     *
     * @return mixed[]
     */
    public function getCategoryUrls(string $category, array $criteria = [], $siteId = null): array
    {
        return RouteMap::$plugin->routes->getCategoryUrls($category, $criteria, $siteId);
    }

    /**
     * Return all of the cateogry group route rules
     *
     * @param string $format 'Craft'|'React'|'Vue'
     * @param int|null $siteId
     *
     * @return mixed[]
     */
    public function getAllCategoryRouteRules(string $format = 'Craft', $siteId = null): array
    {
        return RouteMap::$plugin->routes->getAllCategoryRouteRules($format, $siteId);
    }

    /**
     * Return the route rules for a specific category
     *
     * @param string $format 'Craft'|'React'|'Vue'
     * @param int|null $siteId
     * @return mixed[]
     */
    public function getCategoryRouteRules(string $category, string $format = 'Craft', $siteId = null): array
    {
        return RouteMap::$plugin->routes->getCategoryRouteRules($category, $format, $siteId);
    }


    /**
     * Get all of the assets of the type $assetTypes that are used in the Entry
     * that matches the $url
     *
     * @param int|null $siteId
     *
     * @return mixed[]
     */
    public function getUrlAssetUrls(string $url, array $assetTypes = ['image'], $siteId = null): array
    {
        return RouteMap::$plugin->routes->getUrlAssetUrls($url, $assetTypes, $siteId);
    }

    /**
     * Returns all of the URLs for the given $elementType based on the passed in
     * $criteria and $siteId
     *
     * @var string|ElementInterface $elementType
     * @var array                   $criteria
     * @var int|null                $siteId
     *
     * @return mixed[]
     */
    public function getElementUrls($elementType, array $criteria = [], $siteId = null): array
    {
        return RouteMap::$plugin->routes->getElementUrls($elementType, $criteria, $siteId);
    }

    /**
     * Get all routes rules defined in the config/routes.php file and CMS
     *
     * @var int $siteId
     * @var bool $incGlobalRules - merge global routes with the site rules
     *
     * @return mixed[]
     */
    public function getRouteRules($sideId = null, $incGlobalRules = true): array
    {
        return RouteMap::$plugin->routes->getRouteRules($sideId, $incGlobalRules);
    }
}
